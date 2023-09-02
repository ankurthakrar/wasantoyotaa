<?php
//@@bidroot123##
return [
 
    'API_CODES' => [
        'SUCCESS' => 200,
        'SUCCESS_STATUS' => 1,
        'ERROR'=> 400,
        'ERROR_STATUS' => 2
    ],
    'MESSAGE_CODES'=>[
        'EMAIL_EXIST'=> '1001',
        'EMAIL_NOT_EXIST'=> '1002',
        'UUID_NOT_EXIST'=>'1003',
        
    ],
    'MESSAGE'=>[
        '1001'=>'Email exist',
        '1002'=>'Email not exist',
        '1003'=>'User UUID Not Exist'

    ],

    'VALIDATIONS' => [
        'INVALID_EMAIL'=>'Invalid email',
        'EMAIL_EXIST'=>'Email id already exist',
        'REQUIRED_FIELD'=>'Please enter required details',
        'REGISTRATION_SUCCESS'=>'Registration successful.',
        'EMAIL_NOT_EXIST'=>'Email id not present',
        'USERNAME_NOT_EXIST'=>'Username not present',
        'UUID_NOT_EXIST_MESSAGE'=>'User uuid dose not exists.',
        'FAQ_SUCCESS'=>'FAQ data fetched successfully.',
        'LOGOUT_SUCCESS'=>'Logout successful.',
        'INVALID_TOKEN'=>'Invalid token',
        'LOGOUT_REQUIRED_FIELD'=>'Token required',
        'LOGIN_SUCCESS'=>'Login successful.',
        'REGISTRATION_FAIL'=>'Registration failed.',
        'LOGIN_FAIL'=>'Login failed.',
        'INVALID_DATA' => 'Invalid Data',
        'STOCK_SUCCESS' => 'Stock created successfully',
        'WRONG_CREDENTIAL'=> 'Your credentials are incorrect.',
        
     ],

     'LOGIN_TYPE' => [
        'HARD_LOGIN' => '1',
        'SOFT_LOGIN' => '2',
     ],


];
