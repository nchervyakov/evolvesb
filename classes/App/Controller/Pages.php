<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 23.09.2014
 * Time: 12:21
 */


namespace App\Controller;


use App\Exception\NotFoundException;
use App\Page;

class Pages extends Page
{
    protected $modelName = 'Page';

    public $mainView = 'maintpl';

    public function action_show()
    {
        $alias = $this->request->param('alias');

        if (!$alias) {
            throw new NotFoundException;
        }

        /** @var \App\Model\Page $page */
        $page = $this->pixie->orm->get('page')->where('alias', $alias)->find();
        if (!$page->loaded()) {
            throw new NotFoundException;
        }

        $this->view->subview = 'pages/page';
        $this->view->page = $page;
        $this->view->pageTitle = $page->title;
        $this->view->pageHeader = $this->view->pageTitle;
    }
} 