<?php

return array(
    'default' => array(
        'model' => 'user',
        //Login providers
        'login' => array(
              'password' => array(
                'login_field' => 'username',
                //Make sure that the corresponding field in the database
                //is at least 50 characters long
                'password_field' => 'password'
            ),
            'facebook' => array(
                //Facebook App ID and Secret
//                'app_id' => '725422934182477',
//                'app_secret' => '533fff80993f05d1ec606a93d13b1436',
                'app_id' => '1524599041117229',
                'app_secret' => '3ad0a21285754823ed3c976fff78b277',
                //Permissions to request from the user
                'permissions' => array('user_about_me'),
                //'fbid_field' => 'fb_id',
                'fbid_field' => 'oauth_uid',
                //Redirect user here after he logs in
                'return_url' => '/home'
            ),
            'twitter' => array(
                'oauth_consumer_key' => '90mjd5RJRUNm0vpfarTql4xKz',
                'oauth_consumer_secret' => 'Y2y5lpDa1VnbrR4xE6Z20k6qlWqgOxkPSqtT2LlyrCPePZ1Xuf',
                'twid_field' => 'oauth_uid',
                //'permissions' => array('user_about_me'),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_callback' => '/home',
                'oauth_version' => '1.0'
            ),
            'vkontakte' => array(
                //Vkontakte App ID and Secret
                'app_id' => '4811149',
                'app_secret' => '3EXa9d3VzqxJkZ4qWaZL',
                'api_version' => '5.2',
                //Permissions to request from the user
                'permissions' => array('email'),
                //'vkid_field' => 'vk_id',
                'vkid_field' => 'oauth_uid',
                //Redirect user here after he logs in
                'return_url' => '/collections/all'
            ),
            'google' => array(
                //Google App ID and Secret
                'app_id' => '534749019021-6g13vcvvrpleng2lgbimgi51c4as367s.apps.googleusercontent.com',
                'app_secret' => 'u4bZtog5fcXUAwZAhUGi3U9o',
                //Permissions to request from the user
                'permissions' => array('email','profile'),
                //'google_id_field' => 'google_id',
                'google_id_field' => 'oauth_uid',
                //Redirect user here after he logs in
                'return_url' => '/collections/all',
                'scope' => 'https://www.googleapis.com/auth/userinfo.profile'
            ),
            'odnoklassniki' => array(
                //Google App ID and Secret
                'app_id' => '1126925312',
                'app_public' => 'CBAFIMCEEBABABABA',
                'app_secret' => '4F53CE12E10E17CE28C5D43B',
                //Permissions to request from the user
                'permissions' => array(),
                //'google_id_field' => 'google_id',
                'okid_field' => 'oauth_uid',
                //Redirect user here after he logs in
                'return_url' => '/collections/all',
            ),
        ),
        //Role driver configuration
        'roles' => array(
            'driver' => 'relation',
            'type' => 'has_many',
            //Field in the roles table
            //that holds the models name
            'name_field' => 'name',
            'relation' => 'roles'
        )
    )
);
