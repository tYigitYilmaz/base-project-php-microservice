<?php
use Core\Route;

Route::group(['prefix'=>'entity'],function (){
    Route::run('generate','generator@generate', 'POST');
    Route::run('save','generator@save', 'POST');
    Route::run('bulkInsert','generator@bulkInsert', 'POST');
    Route::run('bulkInsertReturnId','generator@bulkInsertReturnId', 'POST');
    Route::run('update','generator@update', 'PATCH');
    Route::run('get','generator@get');
});
