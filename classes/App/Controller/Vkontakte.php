<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.03.2015
 * Time: 13:22
 */


namespace App\Controller;


use App\Model\User;

class Vkontakte extends \PHPixie\Auth\Controller\Vkontakte
{
    /**
     * @inheritdoc
     */
    public function new_user($access_token, $return_url, $display_mode)
    {
        $config = 'default';
        $data = $this->provider->request("https://api.vk.com/method/users.get?"
            ."user_id={$this->user_id}"
            ."&fields=".implode(',',$this->pixie->config->get("auth.{$config}.login.vkontakte.permissions", array()))
            ."&v={$this->pixie->config->get("auth.{$config}.login.vkontakte.api_version", 5.2)}"
            ."&access_token=".$access_token);
        $data = json_decode($data);
        $data = current($data->response);

        //Save the new user
        $model = new User($this->pixie);
        $user = $model->saveOAuthUser('vk'.$data->id, $data->id, 'vkontakte');
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