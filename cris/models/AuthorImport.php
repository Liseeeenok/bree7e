<?php namespace Bree7e\Cris\Models;

use Backend\Models\ImportModel;
use RainLab\User\Models\User;
use Bree7e\Cris\Models\Author;

/**
 * AuthorImport ImportModel
 * The class must define a method called importData used 
 * for processing the imported data. The first parameter 
 * $results will contain an array containing the data to import. 
 */
class AuthorImport extends ImportModel
{
    /**
     * @var array Rules
     */
    public $rules = [];

    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data)
        {
            $data += [
                'is_activated' => true,
            ];

            if (empty($data['username'])) {
                $data['username'] = $data['email'];
            }

            if (empty($data['password'])) {
                $data['password'] = $data['username'];
            }

            try {
                $user = new Author();
                // $user = new User();
                $user->fill($data);

                // save user
                $user->save();

                // activate user (it sends welcome email)
                // $user->attemptActivation($user->activation_code);

                $this->logCreated();

            } catch (\Exception $ex) {
                $this->logError($row, $ex->getMessage());
            }
        }
    }
}
