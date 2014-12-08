<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.09.2014
 * Time: 13:00
 */


namespace App\Controller;


use App\Exception\NotFoundException;
use App\Page;

/**
 * Class News
 * @property \App\Model\News $model
 * @package App\Controller
 */
class News extends Page
{
    public function action_index()
    {
        $news = $this->model->order_by('date', 'desc')->find_all()->as_array();

        $this->view->subview = 'news/index';
        $this->view->pageTitle = 'News';
        $this->view->pageHeader = 'News';
        $this->view->news = $news;
        $this->view->host = $this->request->getSiteUrl();
    }

    public function action_show()
    {
        $alias = $this->request->param('alias');
        if (!$alias) {
            throw new NotFoundException;
        }

        /** @var \App\Model\News $newsItem */
        $newsItem = $this->model->where('hurl', $alias)->find();
        if (!$newsItem->loaded()) {
            throw new NotFoundException;
        }

        $news = $this->pixie->orm->get($this->model->model_name)
            ->where('id', '!=', $newsItem->id())
            ->order_by('date', 'desc')
            ->limit(5)->find_all()->as_array();

        $nextNewsItems = $this->pixie->orm->get($this->model->model_name)
            ->where('date', '>=', $newsItem->date)
            ->where('or', ['id', $newsItem->id()])
            ->order_by('date', 'asc')
            ->limit(5)->find_all()->as_array();

        $found = false;
        $nextNews = null;
        foreach ($nextNewsItems as $item) {
            if ($item->id() == $newsItem->id()) {
                $found = true;
                continue;
            }
            if ($found) {
                $nextNews = $item;
                break;
            }
        }

        $prevNewsItems = $this->pixie->orm->get($this->model->model_name)
            ->where('date', '<=', $newsItem->date)
            ->where('or', ['id', $newsItem->id()])
            ->order_by('date', 'desc')
            ->limit(5)->find_all()->as_array();

        $found = false;
        $prevNews = null;
        foreach ($prevNewsItems as $item) {
            if ($item->id() == $newsItem->id()) {
                $found = true;
                continue;
            }
            if ($found) {
                $prevNews = $item;
                break;
            }
        }

        $this->view->pageTitle = $newsItem->title;
        $this->view->pageHeader = $newsItem->title;
        $this->view->subview = 'news/show';
        $this->view->newsItem = $newsItem;
        $this->view->nextNews = $nextNews;
        $this->view->prevNews = $prevNews;
        $this->view->news = $news;
        $this->view->host = $this->request->getSiteUrl();
    }
} 