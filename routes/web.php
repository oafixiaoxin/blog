<?php
	use Illuminate\Http\Request;
	use Dingo\Api\Routing\Router;
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

$app->get('/', function () use ($app) {
    return "yanshuxin:";
});

//$app->get('/user', 'ExampleController@user');
//$app->get('/getEmptyTables', 'TableController@getEmptyTables');

$app->group(['prefix' => 'api2/v1'], function($app)
{
	$app->get('/getEmptyTables', 'TableController@getEmptyTables');
	$app->get('/getAllMenuType', 'TableController@getAllMenuType');
	$app->get('/getMenuItem/{menuTypeId}', 'TableController@getMenuItem');
	$app->get('/getAllRecommand', 'TableController@getAllRecommand');
	$app->get('/getRecommandDetail/{id}', 'TableController@getRecommandDetail');
	$app->get('/searchEmptyTable/{tableId}', 'TableController@searchEmptyTable');
	$app->get('/login/{loginName}/{loginPassword}', 'TableController@login');
	$app->get('/getUserInfo/{userId}', 'TableController@getUserInfo');
	$app->get('/getAllTable/{type}', 'TableController@getAllTable');
	$app->get('/searchMenuItem/{regex}', 'TableController@searchMenuItem');
	
	$app->post('/sendOrder', 'TableController@sendOrder');
});
