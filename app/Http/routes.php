<?php

Route::get('/', ['as'=>'visitorHome', 'middleware'=>'guest', 'uses'=>'VisitorsController@home']);

Route::group(['middleware'=>'auth'], function(){
    Route::resource('school', 'SchoolsController');
    Route::resource('division', 'DivisionsController');
    Route::get('/download', ['uses'=>'CsvExportController@exportTbbEnrollmentCounts', 'as'=>'csv.tbbEnrollment']);
    Route::get('reports', ['uses'=>'ValidationErrorReportController@latest', 'as'=>'reports.latest']);
    Route::get('reports/{processToken}', ['uses'=>'ValidationErrorReportController@index', 'as'=>'reports.index']);
    Route::get('reports/{processToken}/{schoolCode}', ['uses'=>'ValidationErrorReportController@show', 'as'=>'reports.show']);
    Route::get('reports/{processToken}/school-id/{schoolId}', ['uses'=>'ValidationErrorReportController@lookupSchoolById', 'as'=>'reports.lookupSchool']);
});

Route::group(['prefix' => 'api/v1', 'namespace' => 'API'], function () {
    Route::get('status', ['uses' => 'ApiController@status']);
});

Route::get('login', ['as'=>'login', 'uses'=>'GoogleAuthController@login']);
Route::get('logout', ['as'=>'logout', 'uses'=>'GoogleAuthController@logout']);
