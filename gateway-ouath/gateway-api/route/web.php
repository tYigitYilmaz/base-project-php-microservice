<?php
use Core\Route;

Route::run('try','category@listAllCategories');

Route::group([
    'trigger_api'=>'user-service',
    'prefix'=>'user'],function (){
    Route::request('register','gateway@register', 'POST');
    Route::request('login','gateway@login', 'POST');
});

Route::group([
    'trigger_api'=>'category-service',
    'prefix'=>'category'],function (){
    Route::run('createCategory','category@createCategory','POST');
    Route::run('listAllCategories','category@listAllCategories');
    Route::run('selectCategory/{id}','category@selectCategory');
    Route::run('deleteCategory','category@deleteCategory', 'DELETE');
});

Route::group([
    'trigger_api'=>'title-subtitle-task-service',
    'prefix'=>'title'],function (){
    Route::run('createTitle','title@createTitle', 'POST');
    Route::run('listAllTitles','title@listAllTitles');
    Route::run('selectTitle/{id}','title@selectTitle');
    Route::run('deleteTitle','title@deleteTitle', 'DELETE');
});

Route::group([
    'trigger_api'=>'title-subtitle-task-service',
    'prefix'=>'subtitle'],function (){
    Route::run('createSubtitle','title@createSubtitle', 'POST');
    Route::run('listAllSubtitles','title@listAllSubtitles');
    Route::run('selectSubtitle/{id}','title@selectSubtitle');
    Route::run('deleteSubtitle','title@deleteSubtitle', 'DELETE');
});

Route::group([
    'trigger_api'=>'title-subtitle-task-service',
    'prefix'=>'task'],function (){
    Route::run('createTask','title@createTask', 'POST');
    Route::run('listAllTasks','title@listAllTasks');
    Route::run('selectTask/{id}','title@selectTask');
    Route::run('writeNote','task@writeNote', 'PATCH');
    Route::run('deleteTask','title@deleteTask', 'DELETE');
});