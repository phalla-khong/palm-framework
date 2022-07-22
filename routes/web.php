<?php

Route::get('/', 'HomeController@index');

Route::get('/sugar', 'SugarController@index');
Route::get('/fruit', 'FruitController@index');

Route::get('/route-info', function(){
    Route::info();
});