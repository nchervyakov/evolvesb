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
                'app_id' => '765305316890718',
                'app_secret' => '3974bb95167be2929c09a2b9c93729b3',
                //Permissions to request from the user
                'permissions' => array('user_about_me'),
                //'fbid_field' => 'fb_id',
                'fbid_field' => 'oauth_uid',
                //Redirect user here after he logs in
                'return_url' => '/'
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
