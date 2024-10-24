<?php namespace Bree7e\Cris\Components;

use DB;
use Lang;
use Auth;
use Mail;
use Event;
use Flash;
use Input;
use Request;
use Redirect;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\Settings as UserSettings;
use Exception;
use Bree7e\Cris\Models\{Project, Publication, PublicationType, Author, AuthorReference, Docx};
use System\Models\File;
use October\Rain\Argon\Argon;

/**
 * Account component
 *
 * Allows users to register, sign in and update their account. They can also
 * deactivate their account and resend the account verification email.
 */
class PersonalAccounts extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => /*Account*/'rainlab.user::lang.account.account',
            'description' => /*User management form.*/'rainlab.user::lang.account.account_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => /*Redirect to*/'rainlab.user::lang.account.redirect_to',
                'description' => /*Page name to redirect to after update, sign in or registration.*/'rainlab.user::lang.account.redirect_to_desc',
                'type'        => 'dropdown',
                'default'     => ''
            ],
            'paramCode' => [
                'title'       => /*Activation Code Param*/'rainlab.user::lang.account.code_param',
                'description' => /*The page URL parameter used for the registration activation code*/ 'rainlab.user::lang.account.code_param_desc',
                'type'        => 'string',
                'default'     => 'code'
            ],
            'forceSecure' => [
                'title'       => /*Force secure protocol*/'rainlab.user::lang.account.force_secure',
                'description' => /*Always redirect the URL with the HTTPS schema.*/'rainlab.user::lang.account.force_secure_desc',
                'type'        => 'checkbox',
                'default'     => 0
            ],
        ];
    }

    public function getRedirectOptions()
    {
        return [''=>'- refresh page -', '0' => '- no redirect -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * Executed when this component is initialized
     */
    public function prepareVars()
    {
        $this->page['user'] = $this->user();
        if ($this->page['user']) {
            $this->page['author'] = $this->getAuthor();
            $this->page['canRegister'] = $this->canRegister();
            $this->page['loginAttribute'] = $this->loginAttribute();
            $this->page['loginAttributeLabel'] = $this->loginAttributeLabel();
            $this->page['rememberLoginMode'] = $this->rememberLoginMode();
            $this->page['authors'] = $this->getAuthors();
            $this->page['projectsNIR'] = $this->getProjectsNIR();
            $this->page['publicationTypes'] = $this->getPublicationTypes();
            $this->page['author_references'] = $this->getAuthorReferences();
            $this->page['docx'] = $this->getDocx();
            $this->getProjects();
        }
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        /*
         * Redirect to HTTPS checker
         */
        if ($redirect = $this->redirectForceSecure()) {
            return $redirect;
        }
        /*
         * Activation code supplied
         */
        if ($code = $this->activationCode()) {
            $this->onActivate($code);
        }

        $this->prepareVars();
    }

    //
    // Properties
    //

    /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    /**
     * Flag for allowing registration, pulled from UserSettings
     */
    public function canRegister()
    {
        return UserSettings::get('allow_registration', true);
    }

    /**
     * Returns the login model attribute.
     */
    public function loginAttribute()
    {
        return UserSettings::get('login_attribute', UserSettings::LOGIN_EMAIL);
    }

    /**
     * Returns the login label as a word.
     */
    public function loginAttributeLabel()
    {
        return Lang::get($this->loginAttribute() == UserSettings::LOGIN_EMAIL
            ? /*Email*/'rainlab.user::lang.login.attribute_email'
            : /*Username*/'rainlab.user::lang.login.attribute_username'
        );
    }

    /**
     * Returns the login remember mode.
     */
    public function rememberLoginMode()
    {
        return UserSettings::get('remember_login', UserSettings::REMEMBER_ALWAYS);
    }

    /**
     * Looks for the activation code from the URL parameter. If nothing
     * is found, the GET parameter 'activate' is used instead.
     * @return string
     */
    public function activationCode()
    {
        $routeParameter = $this->property('paramCode');

        if ($code = $this->param($routeParameter)) {
            return $code;
        }

        return get('activate');
    }

    //
    // AJAX
    //

    /**
     * Sign in the user
     */
    public function onSignin()
    {
        try {
            /*
             * Validate input
             */
            $data = post();
            $rules = [];

            $rules['login'] = $this->loginAttribute() == UserSettings::LOGIN_USERNAME
                ? 'required|between:2,255'
                : 'required|email|between:6,255';

            $rules['password'] = 'required|between:4,255';

            if (!array_key_exists('login', $data)) {
                $data['login'] = post('username', post('email'));
            }

            $validation = Validator::make($data, $rules);
            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            /*
             * Authenticate user
             */
            $credentials = [
                'login'    => array_get($data, 'login'),
                'password' => array_get($data, 'password')
            ];

            /*
            * Login remember mode
            */
            switch ($this->rememberLoginMode()) {
                case UserSettings::REMEMBER_ALWAYS:
                    $remember = true;
                    break;
                case UserSettings::REMEMBER_NEVER:
                    $remember = false;
                    break;
                case UserSettings::REMEMBER_ASK:
                    $remember = (bool) array_get($data, 'remember', false);
                    break;
            }

            Event::fire('rainlab.user.beforeAuthenticate', [$this, $credentials]);

            $user = Auth::authenticate($credentials, $remember);
            if ($user->isBanned()) {
                Auth::logout();
                throw new AuthException(/*Sorry, this user is currently not activated. Please contact us for further assistance.*/'rainlab.user::lang.account.banned');
            }

            /*
             * Redirect
             */
            if ($redirect = $this->makeRedirection(true)) {
                return $redirect;
            }
        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Register the user
     */
    public function onRegister()
    {
        try {
            if (!$this->canRegister()) {
                throw new ApplicationException(Lang::get(/*Registrations are currently disabled.*/'rainlab.user::lang.account.registration_disabled'));
            }

            /*
             * Validate input
             */
            $data = post();

            if (!array_key_exists('password_confirmation', $data)) {
                $data['password_confirmation'] = post('password');
            }

            $rules = [
                'email'    => 'required|email|between:6,255',
                'password' => 'required|between:4,255|confirmed'
            ];

            if ($this->loginAttribute() == UserSettings::LOGIN_USERNAME) {
                $rules['username'] = 'required|between:2,255';
            }

            $validation = Validator::make($data, $rules);
            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            /*
             * Register user
             */
            Event::fire('rainlab.user.beforeRegister', [&$data]);

            $requireActivation = UserSettings::get('require_activation', true);
            $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
            $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
            $user = Auth::register($data, $automaticActivation);

            Event::fire('rainlab.user.register', [$user, $data]);

            /*
             * Activation is by the user, send the email
             */
            if ($userActivation) {
                $this->sendActivationEmail($user);

                Flash::success(Lang::get(/*An activation email has been sent to your email address.*/'rainlab.user::lang.account.activation_email_sent'));
            }

            /*
             * Automatically activated or not required, log the user in
             */
            if ($automaticActivation || !$requireActivation) {
                Auth::login($user);
            }

            /*
             * Redirect to the intended page after successful sign in
             */
            if ($redirect = $this->makeRedirection(true)) {
                return $redirect;
            }
        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Activate the user
     * @param  string $code Activation code
     */
    public function onActivate($code = null)
    {
        try {
            $code = post('code', $code);

            $errorFields = ['code' => Lang::get(/*Invalid activation code supplied.*/'rainlab.user::lang.account.invalid_activation_code')];

            /*
             * Break up the code parts
             */
            $parts = explode('!', $code);
            if (count($parts) != 2) {
                throw new ValidationException($errorFields);
            }

            list($userId, $code) = $parts;

            if (!strlen(trim($userId)) || !strlen(trim($code))) {
                throw new ValidationException($errorFields);
            }

            if (!$user = Auth::findUserById($userId)) {
                throw new ValidationException($errorFields);
            }

            if (!$user->attemptActivation($code)) {
                throw new ValidationException($errorFields);
            }

            Flash::success(Lang::get(/*Successfully activated your account.*/'rainlab.user::lang.account.success_activation'));

            /*
             * Sign in the user
             */
            Auth::login($user);

        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Update the user
     */
    public function onUpdate()
    {
        if (!$user = $this->user()) {
            return;
        }

        if (Input::hasFile('avatar')) {
            $user->avatar = Input::file('avatar');
        }

        $user->fill(post());
        $user->save();

        /*
         * Password has changed, reauthenticate the user
         */
        if (strlen(post('password'))) {
            Auth::login($user->reload(), true);
        }

        Flash::success(post('flash', Lang::get(/*Settings successfully saved!*/'rainlab.user::lang.account.success_saved')));

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }

        $this->prepareVars();
    }

    /**
     * Deactivate user
     */
    public function onDeactivate()
    {
        if (!$user = $this->user()) {
            return;
        }

        if (!$user->checkHashValue('password', post('password'))) {
            throw new ValidationException(['password' => Lang::get('rainlab.user::lang.account.invalid_deactivation_pass')]);
        }

        Auth::logout();
        $user->delete();

        Flash::success(post('flash', Lang::get(/*Successfully deactivated your account. Sorry to see you go!*/'rainlab.user::lang.account.success_deactivation')));

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }
    }

    /**
     * Trigger a subsequent activation email
     */
    public function onSendActivationEmail()
    {
        try {
            if (!$user = $this->user()) {
                throw new ApplicationException(Lang::get(/*You must be logged in first!*/'rainlab.user::lang.account.login_first'));
            }

            if ($user->is_activated) {
                throw new ApplicationException(Lang::get(/*Your account is already activated!*/'rainlab.user::lang.account.already_active'));
            }

            Flash::success(Lang::get(/*An activation email has been sent to your email address.*/'rainlab.user::lang.account.activation_email_sent'));

            $this->sendActivationEmail($user);

        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }
    }

    //
    // Helpers
    //

    /**
     * Returns a link used to activate the user account.
     * @return string
     */
    protected function makeActivationUrl($code)
    {
        $params = [
            $this->property('paramCode') => $code
        ];

        if ($pageName = $this->property('activationPage')) {
            $url = $this->pageUrl($pageName, $params);
        }
        else {
            $url = $this->currentPageUrl($params);
        }

        if (strpos($url, $code) === false) {
            $url .= '?activate=' . $code;
        }

        return $url;
    }

    /**
     * Sends the activation email to a user
     * @param  User $user
     * @return void
     */
    protected function sendActivationEmail($user)
    {
        $code = implode('!', [$user->id, $user->getActivationCode()]);

        $link = $this->makeActivationUrl($code);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.activate', $data, function($message) use ($user) {
            $message->to($user->email, $user->name);
        });
    }

    /**
     * Redirect to the intended page after successful update, sign in or registration.
     * The URL can come from the "redirect" property or the "redirect" postback value.
     * @return mixed
     */
    protected function makeRedirection($intended = false)
    {
        $method = $intended ? 'intended' : 'to';

        $property = trim((string) $this->property('redirect'));

        // No redirect
        if ($property === '0') {
            return;
        }
        // Refresh page
        if ($property === '') {
            return Redirect::refresh();
        }

        $redirectUrl = $this->pageUrl($property) ?: $property;

        if ($redirectUrl = post('redirect', $redirectUrl)) {
            return Redirect::$method($redirectUrl);
        }
    }

    /**
     * Checks if the force secure property is enabled and if so
     * returns a redirect object.
     * @return mixed
     */
    protected function redirectForceSecure()
    {
        if (
            Request::secure() ||
            Request::ajax() ||
            !$this->property('forceSecure')
        ) {
            return;
        }

        return Redirect::secure(Request::path());
    }

    public function getAuthors()
    {
        return Author::orderBy('surname')->get();
    }

    public function getProjectsNIR()
    {
        return Project::whereNotNull('nioktr_number')->orderBy('start_year_date', 'DESC')->get();
    }

    public function getPublicationTypes()
    {
        return PublicationType::make()->get();
    }

    public function getAuthor()
    {
        $author = Author::findOrFail($this->page['user']->id);
        $author->publicationCount = $author->publications->count();

        $author->publicationsGroupedByYear = $author->publications->sortByDesc('year')->groupBy('year');

        $this->getAwards($author);

        //dd($author->publicationsGroupedByYear);
        return $author;
    }

    public function getAwards($author)
    {
        $awards = DB::table('bree7e_cris_awards')
        ->join('bree7e_cris_award_types', 'bree7e_cris_awards.id_award_type', '=', 'bree7e_cris_award_types.id')
        ->join('bree7e_cris_institutions', 'bree7e_cris_awards.id_institution', '=', 'bree7e_cris_institutions.id')
        ->where('id_author', $author->id)->select('bree7e_cris_awards.*', 'bree7e_cris_award_types.name as name_types', 'bree7e_cris_institutions.name as name_institution')->orderBy('aw_date', 'desc')->get(); //todo переделать
        $time = null;
        foreach($awards as $award) {
            $award->aw_date = date('d.m.Y', strtotime($award->aw_date));
        }
        $awards = $awards->groupBy(function($p) {
            return Argon::parse($p->aw_date)->format('Y');
        });

        $this->page['awards'] = $awards;
    }

    public function onSaveAuthorReference()
    {
        $file = new File();
        $file->data = Input::file('file_publication');
        $file->is_public = true;
        $file->save();
        
        $authors_str = post('authors_str');
        $positions_str = post('positions_str') ?? "";
        $id_publication_type = post('id_publication_type');
        $full_name_publication = post('full_name_publication');
        $count_pages = post('count_pages') ?? 0;
        $count_images = post('count_images') ?? 0;
        $is_literary_sources = post('is_literary_sources') ?? false;
        $is_information_not_rospatent = post('is_information_not_rospatent') ?? false;
        $is_information_other_people = post('is_information_other_people') ?? false;
        $is_information_inventions = post('is_information_inventions') ?? false;
        $inventions = post('inventions') ?? "";
        $is_lock = post('is_lock') ?? false;
        $NIR = post('NIR') ?? "";
        $information = post('information') ?? "";
        $user_id = post('user_id');
        $id_zav_lab_otdel = post('id_zav_lab_otdel') ?: null;
        $need_expert_opinion = post('need_expert_opinion') ?? false;
        $need_export_control = post('need_export_control') ?? false;
        $need_export_permit = post('need_export_permit') ?? false;
        
        $this->page['id_author_reference'] = AuthorReference::create([
            'full_name_publication' => $full_name_publication,
            'authors_str' => $authors_str,
            'positions_str' => $positions_str,
            'id_publication_type' => $id_publication_type,
            'count_pages' => $count_pages,
            'count_images' => $count_images,
            'is_literary_sources' => $is_literary_sources === 'on',
            'is_information_not_rospatent' => $is_information_not_rospatent === 'on',
            'is_information_other_people' => $is_information_other_people === 'on',
            'is_information_inventions' => $is_information_inventions === 'on',
            'inventions' => $inventions,
            'is_lock' => $is_lock === 'on',
            'NIR' => $NIR,
            'information' => $information,
            'id_author' => $user_id,
            'id_zav_lab_otdel' => $id_zav_lab_otdel,
            'need_expert_opinion' => $need_expert_opinion === 'on',
            'need_export_control' => $need_export_control === 'on',
            'need_export_permit' => $need_export_permit === 'on',
        ]);

        $this->page['id_author_reference']->material_pdf()->add($file);
    }

    public function onSaveUserAvatar() {
        $file = new File();
        $file->data = Input::file('userfile');
        $file->is_public = true;
        $file->save();
        
        $user = Auth::getUser();

        $user->avatar()->add($file);
    }

    public function getAuthorReferences() {
        //dd(AuthorReference::get());
        return AuthorReference::where(['id_author' => $this->page['user']->id])->orderBy('full_name_publication')->get();
    }

    public function getDocx() {
        return Docx::make()->get();
    }

    public function onDownloadAuthorReference() {
        $idAuthorRef = post('author_ref');

        $AuthorRef = AuthorReference::find($idAuthorRef);
        $time = strtotime($AuthorRef->created_at);

        $docx = Docx::where('name', 'like', "%author_reference_{$idAuthorRef}_%")->first();

        echo "<script>window.open('docx/{$docx->name}.docx');</script>";
    }

    public function onRenderDocx() 
    {
        $this->page['docx'] = Docx::where('id_author', post('author'))->get();
    }

    public function onRenderAuthorRef() 
    {
        $idAuthorRef = post('author_ref');

        $this->page['author_ref'] = Docx::where('name', 'like', "%author_reference_{$idAuthorRef}_%")->first();
        if ($this->page['author_ref'])
        {
            $this->page['author_ref_added'] = Docx::where(['id_author_reference' => $idAuthorRef])->where('id', '!=', $this->page['author_ref']->id)->get();
        }
    }

    public function getProjects()
    {
        $author = $this->page['author'];
        $projects = Project::ofLeader($author)->orderBy('start_year_date', 'desc')->get();
        $projectsGroupedByYear = $projects->groupBy(function($p) {
            return Argon::parse($p->start_year_date)->format('Y');
        });

        $projectsPartId = DB::table('bree7e_cris_authors_projects')->where('rb_author_id', $author->id)->pluck('project_id'); //Переделать
        $projectsPart = DB::table('bree7e_cris_projects')->whereIn('id', $projectsPartId)->orderBy('start_year_date', 'desc')->get(); //Переделать
        $projectsPartGroupedByYear = $projectsPart->groupBy(function($p) {
            return Argon::parse($p->start_year_date)->format('Y');
        });

        $this->page['projectsPartGroupedByYear'] = $projectsPartGroupedByYear;
        $this->page['projectsByYears'] = $projectsGroupedByYear;
    }
}
