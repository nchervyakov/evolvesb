<?php

namespace App;

use App\Core\BaseController;
use App\Model\Cart;
use App\Model\Category as Category;
use App\Model\Product;
use PHPixie\View;

/**
 * Base controller
 * @inheritdoc
 */
class Page extends BaseController
{
    public $mainView = 'main';

    /**
     * Basic part of the title which is appended to the target title.
     * @var string
     */
    public $titleBase;

    public $pageTitle = '';

    public $pageDescription = '';

    public $pageKeywords = '';

    public function before() {
        parent::before();
        if (!$this->execute) {
            return;
        }

        $params = $this->pixie->config->get('parameters');
        $this->titleBase = trim($params['page_title_base']);

        $this->initView($this->mainView);

        if (!$this->installationProcess) {
            $className = isset($this->modelName) && $this->modelName ? $this->modelName : $this->get_real_class($this);
            $category = new Category($this->pixie);
            $this->view->sidebar = $category->getCategoriesSidebar();
            $this->view->search_category = $this->getSearchCategory($className);
            $this->view->search_subcategories = $this->getAllCategories($this->view->sidebar);
            $this->view->pages = $this->pixie->orm->get('page')->where('is_active', 1)->find_all()->as_array();
            $this->view->cart = $this->getCart();
            $this->view->cartItems = $this->getCart()->items->find_all()->as_array();

            if ($className != "Home") {
                $this->view->categories = $category->getRootCategories();
            }

            $classModel = "App\\Model\\" . $className;
            if (class_exists($classModel)) {
                $this->model = new $classModel($this->pixie);
            } else {
                $this->model = null;
            }
        }
    }

    protected function initView($name = 'main')
    {
        $this->view = $this->pixie->view($name);
        $config = $this->pixie->config->get('parameters');
        $this->view->common_path = $config['common_path'];
        $this->common_path = $config['common_path'];
        $this->view->returnUrl = '';
        $this->view->controller = $this;
        $this->view->getHelper()->setController($this);
    }

    public function after() {
        $this->prepareViewBeforeRendering();
        //var_dump($this->view->pageTitle);exit;
        $this->response->body = $this->view->render();
        parent::after();
    }

    protected function getSearchCategory($className) {
        $params = $this->pixie->config->get("parameters");
        switch ($className) {
            case 'Category':
                $category = new Category($this->pixie);
                $search_category = $category->getPageTitle($this->request->param('id'));
                $value = $this->request->param('id');
                break;
            case 'Search':
                $value = $this->request->get("id");
                $category = new Category($this->pixie);
                $search_category = $category->getPageTitle($this->request->get('id'));
				$search_category = ($search_category == "")
                    ? ($params['root_category_name'] ?: "All") : $search_category;
                break;
            default:
                $search_category = $params['root_category_name'] ?: 'All';
                $value = '';
                break;
        }
        return ['value' => $value, 'label' => $search_category];
    }

    protected function getAllCategories($categories) {
        $all_categories = array();
        foreach ($categories as $category) {
            $all_categories[$category->categoryID] = $category->name;
            foreach ($category->childs as $subcategory) {
                $all_categories[$subcategory->categoryID] = $subcategory->name;
            }
        }
        return $all_categories;
    }

    /**
     * @return Product
     */
    protected function getProductsInCart() {
        $cart = $this->getCart();
        return $cart->products->find_all();
    }

    /**
     * @return Cart
     */
    protected function getCart() {
        /** @var Cart $model */
        $model = $this->pixie->orm->get('Cart');
        return $model->getCart();
    }

    public function getProductsInCartIds() {
        /** @var Product[] $items */
        $items = $this->getProductsInCart()->as_array();
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item->id();
        }
        return $ids;
    }

    protected function prepareViewBeforeRendering()
    {
        $this->view->titleBase = $this->titleBase;
        $helper = $this->pixie->view_helper();

        foreach (['titleBase', 'pageDescription', 'pageKeywords'] as $prop) {
            if (!isset($this->view->$prop)) {
                $this->view->$prop = $helper->escape(trim($this->$prop), null, false);
            }
        }

        if (!isset($this->view->pageTitle)) {
            $this->view->pageTitle = implode(' &mdash; ', [
                $helper->escape(trim($this->pageTitle), null, false),
                $helper->escape($this->titleBase, null, false)
            ]);
        }
    }
}
