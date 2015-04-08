<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 23.09.2014
 * Time: 12:42
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Helpers\HttpHelper;

class Pages extends CRUDController
{
    public $modelName = 'Page';

    protected $alias = 'pages';

    public $modelNamePlural = 'Страницы';
    public $modelNameSingle = 'Страница';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'alias' => [
                    'type' => 'link',
                    'max_length' => '255',
                    'strip_tags' => true,
                    'title' => 'Алиас',
                ],
                'title' => [
                    'type' => 'link',
                    'max_length' => '255',
                    'strip_tags' => true,
                    'title' => 'Название'
                ],
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function getEditFields()
    {
        return [
            'id' => [],
            'alias' => [
                'required' => true,
                'class_names' => 'js-alias-field js-suggest-source',
                'label' => 'Алиас'
            ],
            'title' => [
                'type' => 'text',
                'required' => true,
                'class_names' => 'js-suggest-source',
                'label' => 'Название'
            ],
            'text' => [
                'type' => 'textarea',
                'required' => true,
                'class_names' => 'js-editor',
                'row' => 6,
                'label' => 'Текст (HTML)'
            ],
            'tag' => [
                'type' => 'text',
                'label' => 'Тег'
            ],
            'h1' => [
                'type' => 'text',
                'label' => 'H1'
            ],
            'is_active' => [
                'type' => 'boolean',
                'default_value' => true,
                'label' => 'Активна'
            ],
            'meta_title' => [
                'type' => 'textarea',
            ],
            'meta_keywords' => [
                'type' => 'textarea',
            ],
            'meta_description' => [
                'type' => 'textarea',
            ],
        ];
    }

    public function action_edit()
    {
        parent::action_edit();
        if (!$this->execute) {
            return;
        }
        $this->view->pageHeader = 'Страница &laquo;' . htmlspecialchars(trim($this->view->item->title)) . '&raquo;';
    }

    public function action_suggest_alias()
    {
        $this->jsonResponse(['alias' => HttpHelper::clearUrlSegment($this->request->get('alias'))]);
    }
} 