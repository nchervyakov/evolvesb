<?php

namespace App\Controller;

use App\Model\Product as Product;
use App\Model\Review;
use \App\Model\SpecialOffers as SpecialOffers;
use App\DataImport\BestBuyReviewImporter;
use App\Page;

class Home extends Page {

    const COUNT_RND_PRODUCTS = 3; //count products for rnd block of main page

    /**
     * @var int Count of products in top viewed products on homepage.
     */

    protected $topViewedCount = 4;

    /**
     * @var int Count of products in related to visited products on homepage.
     */
    protected $relatedToVisitedCount = 8;

    /**
     * @var int Count of products in best choice block on homepage.
     */
    protected $bestChoiceCount = 4;

    /**
     * @var int Count of reviews on homepage.
     */
    protected $reviewsCount = 2;

    public $mainView = 'main';

    public function action_index() {
        if ($this->request->method == 'POST') {
            $data = $this->request->post();
            if (trim($data['ORDER'])) {
                $this->redirect('/account/orders/' . trim($data['ORDER']));
            }
        }

        $this->view->bodyClass = "index";
        $page = $this->pixie->orm->get('page')->where('alias', 'main')->find();

        $this->view->common_path = $this->common_path;
        $this->view->subview = 'home/home';
        $this->view->page = $page;
        $this->view->pageTitle = $page->title;
        $this->view->pageHeader = $this->view->pageTitle;        
    }

    public function action_404() {
        $this->view->subview = '404';
        $this->view->message = "Index page";
    }

    /**
     * Method for generating reviews from outer source.
     */
    public function generateReviews() {
        set_time_limit(300);
        // Get product ids as parents for reviews
        $res = $this->pixie->db->query('select')->table('tbl_products')->fields('productID')->execute();
        $productIds = array();
        foreach ($res as $row) {
            $productIds[] = (int) $row->productID;
        }

        $importer = new BestBuyReviewImporter($this->pixie);
        $reviews = $importer->getReviews(500);

        foreach ($reviews as $rev) {
            $review = new Review($this->pixie);
            $review->values($rev);
            $parentKey = array_rand($productIds);
            $review->productID = $productIds[$parentKey];
            $review->save();
        }
    }

    /**
     * @return int
     */
    public function getBestChoiceCount() {
        return $this->bestChoiceCount;
    }

    /**
     * @param int $bestChoiceCount
     */
    public function setBestChoiceCount($bestChoiceCount) {
        $this->bestChoiceCount = $bestChoiceCount;
    }

    /**
     * @return int
     */
    public function getRelatedToVisitedCount() {
        return $this->relatedToVisitedCount;
    }

    /**
     * @param int $relatedToVisitedCount
     */
    public function setRelatedToVisitedCount($relatedToVisitedCount) {
        $this->relatedToVisitedCount = $relatedToVisitedCount;
    }

    /**
     * @return int
     */
    public function getReviewsCount() {
        return $this->reviewsCount;
    }

    /**
     * @param int $reviewsCount
     */
    public function setReviewsCount($reviewsCount) {
        $this->reviewsCount = $reviewsCount;
    }

    /**
     * @return int
     */
    public function getTopViewedCount() {
        return $this->topViewedCount;
    }

    /**
     * @param int $topViewedCount
     */
    public function setTopViewedCount($topViewedCount) {
        $this->topViewedCount = $topViewedCount;
    }
}