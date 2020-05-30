<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

$router->get('/get-motto', function () use ($router) {
    return response()->json([
    	'success'=>true,
    	'data'=>'Hidup adalah seperti ayah dan ibu, kadang diatas kadang dibawah',
    	'message'=>'Get Motto Success'
    ],200);
});


// $router->post('/login',[
// 	'middleware'=>'throttle:5,1',
// 	'uses'=>'AuthController@login'
// ]);

// $router->get('/token/{token}',[
// 	'middleware'=>'throttle:70,2',
// 	'uses'=>'AuthController@token'
// ]);

// $router->get('/get-motto/{angka1}/{angka2}', function ($angka1, $angka2) use ($router) {
// 	$hasil = $angka1 * $angka2;
//     return response()->json([
//     	'success'=>true,
//     	'data'=>$hasil,
//     	'message'=>'Get Motto Success'
//     ],200);
// });

// $router->get('/get-motto', function () use ($router) {
//     return response()->json([
//     	'success'=>true,
//     	'data'=>'Hidup adalah seperti ayah dan ibu, kadang diatas kadang dibawah',
//     	'message'=>'Get Motto Success'
//     ],200);
// });

$router->get('/scoreboard',[
	'middleware'=>'auth',
	'uses'=>'ScoreBoardController@index'
]);

$router->group(['middleware' => 'throttle:5,5'], function () use ($router) {
	$router->post('/register','AuthController@register');
	$router->post('/resendemail','AuthController@resendemail');
	$router->get('/verifyemail','AuthController@verifyemail');

	$router->post('/forgotpassword','AuthController@forgotpassword');
	$router->get('/cekforgotpassword','AuthController@cekforgotpassword');
	$router->post('/resetpassword','AuthController@resetpassword');
});

$router->group(['middleware' => 'auth'], function () use ($router) {
	$router->get('/logout','AuthController@logout');
	$router->patch('/account','AccountController@update');

	$router->group(['prefix' => 'role'], function () use ($router) {
		$router->get('/','RoleController@index');
		$router->get('show/{id}','RoleController@show');
		$router->get('permission','RoleController@permission');
		$router->post('store','RoleController@store');
		$router->patch('update/{id}','RoleController@update');
		$router->delete('destroy/{id}','RoleController@destroy');
	});

	$router->group(['prefix' => 'user'], function () use ($router) {
		$router->get('/','UserController@index');
		$router->get('show/{id}','UserController@show');
		$router->post('store','UserController@store');
		$router->patch('update/{id}','UserController@update');
		$router->delete('destroy/{id}','UserController@destroy');
	});

	$router->group(['prefix' => 'challenge'], function () use ($router) {
		$router->get('/','ChallengeController@index');
		$router->post('store','ChallengeController@store');
		$router->patch('update/{id}','ChallengeController@update');
		$router->delete('destroy/{id}','ChallengeController@destroy');
		$router->delete('destroyFile/{id}/{file}','ChallengeController@destroyFile');

		$router->post('check/{id}',[
			'middleware'=>'throttle:5,2',
			'uses'=>'ChallengeLogController@cekFlag'
		]);
	});
});