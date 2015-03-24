<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.03.2015
 * Time: 21:21
 */


namespace App\Admin\Controller;


use App\Admin\Controller;

class ReceiptSettings extends Controller
{
    public function action_index()
    {
        $this->view->subview = 'settings/receipt/index';
    }
}