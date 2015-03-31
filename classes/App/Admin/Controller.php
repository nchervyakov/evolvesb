<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 13:40
 */


namespace App\Admin;


use App\Core\BaseController;
use App\Core\View;
use App\Exception\HttpException;
use App\Model\User;


class Controller extends BaseController
{
    /**
     * @var View
     */
    public $view;
    public $common_path;

    public $root = '/admin';

    /**
     * @var User
     */
    protected $user;

    public function before()
    {
        $this->secure();
        parent::before();

        $user = $this->pixie->auth->user();
        $this->user = $user;

        $this->view = $this->view('main');
        $config = $this->pixie->config->get('parameters');
        $this->common_path = $config['common_path'];
        $this->view->common_path = $config['common_path'];
        $this->view->returnUrl = '';
        $this->view->controller = $this;
        $this->view->adminRoot = $this->root;
        $className = isset($this->modelName) && $this->modelName ? $this->modelName : $this->get_real_class($this);
        $this->view->sidebarLinks = $this->getSidebarLinks();

        $this->view->user = $user;
        $this->view->pageHeader = 'Dashboard';

        $classModel = "App\\Model\\" . $className;
        if (class_exists($classModel)) {
            $this->model = new $classModel($this->pixie);
        } else {
            $this->model = null;
        }
    }

    public function after()
    {
        $this->response->body = $this->view->render();

        parent::after();
    }

    public function view($name, $group = 'admin')
    {
        return $this->pixie->view(($group ? $group . '/' : '') . $name);
    }

    public function run($action)
    {
        try {
            parent::run($action);
        } catch (HttpException $e) {
            $e->setOrigin(HttpException::ORIGIN_ADMIN);
            throw $e;
        }
    }

    public function getSidebarLinks()
    {
        return [
            $this->root => ['label' => 'Главная', 'link_class' => 'fa fa-dashboard fa-fw'],
            $this->root.'/user' => ['label' => 'Пользователи', 'link_class' => 'fa fa-user fa-fw'],
            $this->root.'/role' => ['label' => 'Роли', 'link_class' => 'fa fa-puzzle-piece fa-fw'],
            $this->root.'/pages' => ['label' => 'Страницы', 'link_class' => 'fa fa-book fa-fw'],
            $this->root.'/news' => ['label' => 'Новости', 'link_class' => 'fa fa-rss-square fa-fw'],
            $this->root.'/category' => ['label' => 'Категории продуктов', 'link_class' => 'fa fa-sitemap fa-fw'],
            $this->root.'/product' => ['label' => 'Продукты', 'link_class' => 'fa fa-archive fa-fw'],
            $this->root.'/option' => ['label' => 'Свойства продуктов', 'link_class' => 'fa fa-check-circle-o fa-fw'],
            $this->root.'/order' => ['label' => 'Заказы', 'link_class' => 'fa fa-shopping-cart fa-fw'],
            /*
            $this->root.'/enquiry' => ['label' => 'Запросы', 'link_class' => 'fa fa-life-saver fa-fw'],
            $this->root.'/faq' => ['label' => 'Вопросы', 'link_class' => 'fa fa-question-circle fa-fw'],
            */
            [
                'label' => 'Рассылка',
                'link_class' => 'fa fa-question-circle fa-fw',
                'items' => [
                    $this->root.'/newsletter-signup' => ['label' => 'Подписки', 'link_class' => 'fa fa-check fa-fw'],
                    $this->root.'/newsletter-template' => ['label' => 'Шаблоны писем', 'link_class' => 'fa fa-code fa-fw'],
                    $this->root.'/newsletter' => ['label' => 'Письма', 'link_class' => 'fa fa-envelope fa-fw'],
                ]
            ],
            [
                'label' => 'Настройки',
                'link_class' => 'fa fa-gears fa-fw',
                'items' => [
                    $this->root.'/receipt-settings' => ['label' => 'Параметры квитанции', 'link_class' => 'fa fa-newspaper-o fa-fw'],
                ]
            ],
        ];
    }
}