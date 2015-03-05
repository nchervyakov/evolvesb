<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.03.2015
 * Time: 13:22
 */


namespace App\Controller;


use App\Model\User;

class Google extends \PHPixie\Auth\Controller\Google
{

    /**
     * @inheritdoc
     */
    public function new_user($access_token, $return_url, $display_mode)
    {
        $data = $this->provider->request("https://www.googleapis.com/plus/v1/people/me?access_token=".$access_token);
        $data = json_decode($data, true);
        //Save the new user
        $model = new User($this->pixie);
        $user = $model->saveOAuthUser('gl'.$data['id'], $data['id'], 'google');

        if (is_array($data['emails']) && count($data['emails'])) {
            $user->email = $data['emails'][0]['value'];
        }

        if (is_array($data['name'])) {
            $user->first_name = $data['name']['givenName'];
            $user->last_name = $data['name']['familyName'];
        }
        $user->save();

        //Get the 'pixie' role
        /*
        $role=$this->pixie->orm->get('role')
            ->where('name','pixie')
            ->find();

        //Add the 'pixie' role to the user
        $fairy->add('roles',$role);
 */
        //Finally set the user inside the provider
        $this->provider->set_user($user, $access_token);

        //And redirect him back.
        $this->return_to_url($display_mode, $return_url);
    }
}