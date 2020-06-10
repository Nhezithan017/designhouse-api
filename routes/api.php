<?php

//Public Routes
Route::get('me', 'User\MeController@getMe');

//Route group for authenticated user only

Route::group(['middleware' => ['auth:api']], function(){
    Route::post('logout','Auth\LoginController@logout');
    Route::put('settings/profile','User\SettingController@updateProfile');
    Route::put('settings/password','User\SettingController@updatePassword');
});


//Routes for guest only

Route::group(['middleware' => ['guest:api']], function(){
    Route::post('register','Auth\RegisterController@register');
    Route::post('verification/verify/{user}','Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend','Auth\VerificationController@resend');
    Route::post('login','Auth\LoginController@login');
    Route::post('password/email','Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset','Auth\ResetPasswordController@reset');
});
