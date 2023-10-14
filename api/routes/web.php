<?php

define('TOKENSECRET','wasan@123#*');


/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('register', 'UserController@register');
$router->post('login', 'UserController@login');
$router->post('rtoNotificationList', 'UserController@rtoNotificationList');
// $router->post('rtoDocketDateUpdate', 'UserController@rtoDocketDateUpdate');
$router->post('createStock', 'StockController@addStock');
$router->post('createStockBulk', 'BulkStockController@addStockBulk');
$router->post('logout', 'StockController@logout');

$router->post('stockList', 'StockController@getStockList');
$router->post('getStock', 'StockController@getStockDetails');
$router->post('getStockByVinNo', 'StockController@getStockDetailsByVinNo');
$router->post('stockVinNoList', 'StockController@getStockVinNoList');




$router->post('updateStock', 'StockController@updateStockDetails');

$router->post('createModel', 'MasterController@addModel');
$router->post('modelList', 'MasterController@getModelList');
$router->post('deleteModel', 'MasterController@deleteModelDetails');

$router->post('createSuffix', 'MasterController@addSuffix');
$router->post('suffixList', 'MasterController@getSuffixList');
$router->post('deleteSuffix', 'MasterController@deleteSuffixDetails');

$router->post('createGrade', 'MasterController@addGrade');
$router->post('gradeList', 'MasterController@getGradeList');
$router->post('deleteGrade', 'MasterController@deleteGradeDetails');

$router->post('createIntColor', 'MasterController@addIntColor');
$router->post('intcolorList', 'MasterController@getIntColorList');
$router->post('deleteIntColor', 'MasterController@deleteIntColorDetails');

$router->post('createExtColor', 'MasterController@addExtColor');
$router->post('extcolorList', 'MasterController@getExtColorList');
$router->post('deleteExtColor', 'MasterController@deleteExtColorDetails');

$router->post('createTeam', 'MasterController@addTeam');
$router->post('teamList', 'MasterController@getTeamList');
$router->post('deleteTeam', 'MasterController@deleteTeamDetails');

$router->post('createLocation', 'MasterController@addLocation');
$router->post('locationList', 'MasterController@getLocationList');
$router->post('deleteLocation', 'MasterController@deleteLocationDetails');

$router->post('createDocket', 'DocketController@addDocket');
$router->post('createDocketBulk', 'BulkStockController@addDocketBulk');
$router->post('docketList', 'DocketController@getDocketList');
$router->post('getDocket', 'DocketController@getDocketDetails');
$router->post('scrapDocket', 'DocketController@scrapDocket');
$router->post('updateDocket', 'DocketController@updateDocketDetails');
$router->post('updatePaymentDetails', 'DocketController@updatePaymentDetails');

$router->post('userList', 'MasterController@getUserList');
$router->post('getUser', 'MasterController@getUserDetails');
$router->post('updateUser', 'MasterController@updateUserDetails');


$router->post('createSuffixOldNew', 'MasterController@addSuffixOldNew');
$router->post('suffixOldNewList', 'MasterController@getSuffixOldNewList');
$router->post('deleteSuffixOldNew', 'MasterController@deleteSuffixOldNewDetails');


$router->post('available-suffix-list', 'MasterController@availableSuffixList');
$router->post('add-link', 'MasterController@addLink'); 
$router->post('link-list', 'MasterController@getLinkList'); 
$router->post('link-detail', 'MasterController@linkDetail'); 
$router->post('update-link', 'MasterController@updateLink'); 
$router->post('getDataBySuffix', 'MasterController@getDataDetailBySuffix');

$router->post('getSubLocationUser', 'MasterController@getSubLocationUser');
$router->post('getTeamFromLocation', 'MasterController@getTeamFromLocation');
$router->post('getReportingUserList', 'MasterController@getReportingUserList');
