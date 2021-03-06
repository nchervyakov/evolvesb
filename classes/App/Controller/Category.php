<?php

namespace App\Controller;
use App\Exception\NotFoundException;
use App\Model\Product;
use App\Page;
use App\SearchFilters\FilterFabric;
use PHPixie\Paginate\Pager\ORM;

/**
 * Class Category
 * @package App\Controller
 * @property \App\Model\Category $model
 */
class Category extends Page
{
    public function before()
    {
        parent::before();
        $this->view->host = $this->request->getSiteUrl();
    }

    public function action_view()
    {
        $perPage = 21;
        $categoryAlias = $this->request->param('category');//$this->request->param('id');

        if (!$categoryAlias) {
            throw new NotFoundException();
        }

        $category = $this->model->loadCategoryByAlias($categoryAlias);
        if ($category instanceof \App\Model\Category) {
            $helper = $this->pixie->view_helper();

            $this->pageTitle = trim($category->meta_title) ? $category->meta_title : $category->getPrintName();
            $this->pageDescription = trim($category->meta_desc) ? $category->meta_desc : $helper->excerpt($category->description, 300);
            $this->pageKeywords = $category->meta_keywords;

            $this->view->categoryName = $category->getPrintName();
            $this->view->h1 = trim($category->h1) ? $category->h1 : $category->getPrintName();
            $this->view->description = $category->description;

            $page = $this->request->get('page', 1);
            $productModel = new Product($this->pixie);
            $productModel->prepareForCategory($category);

            /** @var ORM $pager */
            $pager = $this->pixie->paginate->orm($productModel, $page, $perPage);
            $this->view->products = $pager->current_items()->as_array();
            $this->view->pager = $pager;
            $this->view->subview = 'category/category';
            $this->view->breadcrumbs = $this->getBreadcrumbs($category);
            $this->view->categoryID = $category->id();
            $this->view->category = $category;

        } else {
            throw new NotFoundException;
        }
    }

    private function getBreadcrumbs(&$category)
    {
        $breadcrumbs = [];
        $parents = $category->parents();
        $breadcrumbs['/'] = 'Home';
        foreach ($parents as $p) {
            $breadcrumbs['/category/view?id=' . $p->categoryID] = $p->name;
        }
        $breadcrumbs[''] = $category->name;
        return $breadcrumbs;
    }

}