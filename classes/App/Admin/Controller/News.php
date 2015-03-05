<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.09.2014
 * Time: 14:30
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;

class News extends CRUDController
{
    public $modelNamePlural = 'Новости';
    public $modelNameSingle = 'Новость';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'hurl' => [
                    'title' => 'Alias',
                    'type' => 'link',
                    'max_length' => '255',
                    'strip_tags' => true,
                ],
                'title' => [
                    'type' => 'link',
                    'max_length' => '255',
                    'strip_tags' => true,
                ],
                'enable' => [
                    'type' => 'boolean',
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
            'hurl' => [
                'label' => 'Alias',
                'required' => true,
                'class_names' => 'js-alias-field js-suggest-source',
            ],
            'title' => [
                'type' => 'text',
                'required' => true,
                'class_names' => 'js-suggest-source'
            ],
            'text' => [
                'type' => 'textarea',
                'required' => true,
                'class_names' => 'js-editor',
                'row' => 6
            ],
            'brief' => [
                'type' => 'textarea',
                'required' => true,
                'class_names' => 'js-editor',
                'row' => 6
            ],
            'enable' => [
                'label' => 'Is Enabled',
                'type' => 'boolean',
                'default_value' => true
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
        $this->view->pageHeader = 'News entry &laquo;' . htmlspecialchars(trim($this->view->item->title)) . '&raquo;';
    }
}