<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use \App\Model\SpecialOffers;
use \App\Model\Category;
use PHPixie\DB\PDOV\Result;

/**
 * Class Product
 * @property \App\Model\Product model
 * @package App\Controller
 */
class Product extends \App\Page
{
    public function action_view()
    {
        $productAlias = $this->request->param('alias');//$this->request->param('id');
        $categoryAlias = $this->request->param('category');//$this->request->param('id');
        if (!$productAlias) {
            throw new NotFoundException;
        }
        /** @var \App\Model\Product $product */
        $product = $this->model->where('hurl', '=', $productAlias)->find();
        /** @var Category $category */
        $category = $this->pixie->orm->get('Category')->loadCategoryByAlias($categoryAlias);
        $category = $category && $category->loaded() ? $category : false;

        if (!$product->loaded()) {
            throw new NotFoundException;
        }

        $this->view->product = $product;
        $this->view->category = $category;
        $this->view->productImages = $product->images->find_all()->as_array();
        $productModel = new \App\Model\Product($this->pixie);
        $productModel->prepareForCategory($category);
        $this->view->relatedProducts = $productModel->where('and', ['productID', '!=', $product->id()])
            ->limit(3)->find_all()->as_array();

        $prevNext = $this->getPrevAndNextProducts($product, $category);
        $this->view->prevItem = $prevNext[0];
        $this->view->nextItem = $prevNext[1];
        $this->view->pageTitle = $product->name;
        $this->view->subview = 'product/product';
    }

    private function getBreadcrumbs()
    {
        $categories = $this->view->product->categories->find_all();
        $breadcrumbs = [];
        foreach ($categories as $cat) {
            $parents = $cat->parents();
            $breadcrumbsParts = [];
            foreach ($parents as $p) {
                $breadcrumbsParts['/category/view?id='.$p->categoryID] = $p->name;
            }
			$breadcrumbsParts['/category/view?id='.$cat->categoryID] = $cat->name;
            $breadcrumbsParts['/product/view?id='.$this->view->product->productID] = $this->view->product->name;
            $breadcrumbs[] = array_merge(['/' => 'Home'], $breadcrumbsParts);
        }
        return $breadcrumbs;
    }

    public function getPrevAndNextProducts($product, $category)
    {
        $productModel = new \App\Model\Product($this->pixie);
        $productModel->prepareForCategory($category);
        $productModel->prepare_relations();
        $productModel->query->fields('tbl_products.productID');
        /** @var Result $result */
        $result = $productModel->query->execute();
        $ids = $result->as_array();

        $prevItem = null;
        $nextItem = null;
        foreach ($ids as $key => $row) {
            if ($row->productID == $product->id()) {
                if ($ids[$key - 1] && $ids[$key - 1]->productID) {
                    $prevItem = $this->pixie->orm->get('product', $ids[$key - 1]->productID);
                }
                if ($ids[$key + 1] && $ids[$key + 1]->productID) {
                    $nextItem = $this->pixie->orm->get('product', $ids[$key + 1]->productID);
                }
                break;
            }
        }

        return [$prevItem, $nextItem];
    }
}