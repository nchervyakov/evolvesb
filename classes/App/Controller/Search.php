<?php

namespace App\Controller;

use App\Page;
use App\SearchFilters\FilterFabric;
use App\Model\Product as Product;
use App\Paginate;
use PHPixie\DB\PDOV\Query;
use PHPixie\ORM\Model;
use PHPixie\Paginate\Pager\DB;

class Search extends Page {

    protected $_products;

    public function action_index() {
        $q = $this->request->get('q');
        $type = $this->request->get('type');

        if (!$q || strlen($q) < 3) {
            $this->view->subview = 'search/form';
            return;
        }

        $searchValues = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
        if (!count($searchValues)) {
            $this->view->subview = 'search/form';
            return;
        }

        $types = [
            'product' => [
                'model' => 'Product',
                'fields' => ['name', 'description']
            ],
            'page' => [
                'model' => 'Page',
                'fields' => ['title', 'text']
            ],
            'news' => [
                'model' => 'News',
                'fields' => ['title', 'text', 'brief']
            ],
            'faq' => [
                'model' => 'Faq',
                'fields' => ['question', 'answer']
            ]
        ];

        /** @var Query $query */
        $query = null;
        foreach($types as $typeName => $typeData) {
            if (!$typeData['fields']) {
                continue;
            }

            if ($type && $typeName != $type) {
                continue;
            }

            $searchConditions = [];

            foreach ($typeData['fields'] as $field) {
                $fieldSearchConditions = [];
                foreach ($searchValues as $sVal) {
                    if (strlen($sVal) < 3) {
                        continue;
                    }
                    $fieldSearchConditions[] = ['and', [$field, 'LIKE', "%$sVal%"]];
                }
                if ($fieldSearchConditions) {
                    $searchConditions[] = ['or', $fieldSearchConditions];
                }
            }
            if ($searchConditions) {
                $modelClass = '\\App\\Model\\'.$typeData['model'];
                /** @var Model $model */
                $model = new $modelClass($this->pixie);
                /** @var Query $subquery */
                $subquery = $this->pixie->db->query('select')->table($model->table)->where($searchConditions);

                if ($typeName == 'faq') {
                    $subquery->limit(1);
                }
                $fieldsToFetch = [
                    [$model->id_field, 'id'],
                    [$this->pixie->db->expr('"'.addslashes($modelClass).'"'), 'type']
                ];
                call_user_func_array([$subquery, 'fields'], $fieldsToFetch);
                if (!$query) {
                    $query = $subquery;
                } else {
                    $query->union($subquery);
                }
            }
        }

        if (!$query) {
            $this->view->subview = 'search/form';
            return;
        }

        $current_page = $this->request->get('page');
        if(empty($current_page)) {
            $current_page = 1;
        }

        /** @var Paginate\Paginate\Pager\DB $pager */
        $pager = new DB($this->pixie, $query, $current_page, 12);

        $pager->set_url_callback(function($page) use ($q, $type) {
            $url = "/search/?page=$page&q=$q";
            if ($type) {
                $url .= "&type=$type";
            }
            return $url;
        });

        $currentItems = $pager->current_items();
        $currentItems = $this->prepareSearchItems($currentItems);

        if ($this->request->is_ajax()) {
            $view = $this->pixie->view('search/search_results');

            $view->searchString = is_null($q) ? '' : $q;
            $view->pageTitle = 'Поиск по &laquo;' . $q . '&raquo;';
            $view->pager = $pager;
            $view->currentItems = $currentItems;
            $view->searchValues = $searchValues;

            $this->response->body = $view->render();
            $this->execute = false;

        } else {
            $this->view->searchString = is_null($q) ? '' : $q;
            $this->view->pageTitle = 'Поиск по &laquo;' . $q . '&raquo;';
            $this->view->pager = $pager;
            $this->view->currentItems = $currentItems;
            $this->view->searchValues = $searchValues;
            $this->view->subview = 'search/search_results';
        }
    }

    protected function prepareSearchItems($currentItems)
    {
        $modelSeparated = [];
        $result = [];
        foreach ($currentItems as $key => $item) {
            $modelSeparated[$item->type][] = $item->id;
            $result[$key] = $item;
        }

        if (!count($modelSeparated)) {
            return [];
        }

        foreach ($modelSeparated as $modelClass => $items) {
            /** @var Model $model */
            $model = new $modelClass($this->pixie);
            $model->where($model->id_field, 'IN', $this->pixie->db->expr('(' . implode(',', $items) . ')'));
            $res = $model->find_all()->as_array();
            /** @var Model $item */
            foreach ($res as $item) {
                foreach ($result as $cKey => $cItem) {
                    if ($cItem->id == $item->id() && substr($cItem->type, 1) == get_class($item)) {
                        $result[$cKey]->item = $item;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}