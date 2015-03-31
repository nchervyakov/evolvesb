<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.03.2015
 * Time: 13:22
 */


namespace App\Controller;


use App\Model\User;

class Odnoklassniki extends \PHPixie\Auth\Controller\Odnoklassniki
{
    /**
     * @inheritdoc
     */
    public function new_user($access_token, $return_url, $display_mode)
    {
        $data = $this->provider->request("http://api.odnoklassniki.ru/fb.do?"
            ."method=users.getCurrentUser"
            ."&access_token=" . $access_token
            ."&application_key=".$this->provider->getAppKey()
            ."&sig=".$this->provider->getSignature($access_token));

        $data = json_decode($data);

        //Save the new user
        $model = new User($this->pixie);
        $user = $model->saveOAuthUser('ok'.$data->uid, $data->uid, 'odnoklassniki');
        $user->first_name = $data->first_name;
        $user->last_name = $data->last_name;
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