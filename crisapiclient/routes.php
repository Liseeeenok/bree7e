<?php

Route::group(['prefix' => 'api/v1'], function () {
    Route::apiResource('publications', 'Bree7e\CrisApiClient\Api\Publications')
        ->only(['index', 'show']);        
    Route::get('publications/search/{phrase}', 'Bree7e\CrisApiClient\Api\Publications@search');

    Route::apiResource('authors', 'Bree7e\CrisApiClient\Api\Authors')
        ->only(['index', 'show']);
    });

Route::get('api/hello', function() {
    return 'Hello World';
});

// Route::get('/welcome', 'authorName\pluginName\WelcomeController@index');

// Route::get('api/populate', function(){
//     $faker = Faker\Factory::create();
//     for($i = 0; $i < 20; $i++){
//         Todo::create([
//             'title' => $faker->sentence($nbWords = 6, $variableNbWords = true),
//             'description' => $faker->text($maxNbChars = 200),
//             'status' => $faker->boolean($chanceOfGettingTrue = 50),
//             'created_at' => $faker->date($format = 'Y-m-d H:i:s', $max = 'now')
//         ]);
//     }
//     return "Todos Created!";
// });
// Route::get('api/todos', function() {
//     $todos = Todo::all();
//     return $todos;
// });
// Route::post('api/add-todo', function(Request $req){
//     $data = $req->input();
//     Todo::create([
//         'title' => $data['title'],
//         'description' => $data['description'],
//         'status' => $data['status']
//     ]);
// });
// Route::post('api/delete-todo', function(Request $req){
//     $data = $req->input();
//     Todo::destroy($data['id']);
// });
// Route::post('api/toggle-todo', function(Request $req){
//     $data = $req->input();
//     Todo::where('id', $data['id'])->update(['status' => $data['status']]);
// });
// Route::post('api/update-todo', function(Request $req){
//     $data = $req->input();
//     Todo::where('id', $data['id'])
//     ->update([
//         'status' => $data['status'],
//         'title' => $data['title'],
//         'description' => $data['description']
//     ]);
// });
