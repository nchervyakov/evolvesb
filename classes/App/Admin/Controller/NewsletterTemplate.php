<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 25.09.2014
 * Time: 11:41
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Helpers\ArraysHelper;

/**
 * Class NewsletterTemplate
 * @property \App\Model\NewsletterTemplate $model
 * @package App\Admin\Controller
 */
class NewsletterTemplate extends CRUDController
{
    public $modelNamePlural = 'Шаблоны писем';

    public $modelNameSingle = 'Шаблон письма';

    public $alias = 'newsletter-template';

    public $editView = 'newsletter_template/edit';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'title' => [
                    'title' => 'Название',
                    'type' => 'link',
                    'max_length' => '255',
                    'strip_tags' => true,
                ],
                'type' => [
                    'title' => 'Тип'
                ],
                'created_on' => [
                    'title' => 'Создан'
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
            'title' => [
                'required' => true,
            ],
            'type' => [
                'type' => 'select',
                'option_list' => ArraysHelper::arrayFillEqualPairs(['html', 'txt'])
            ],
            'text' => [
                'type' => 'textarea',
                'class_names' => 'js-editor',
            ]
        ];
    }

    public function action_index()
    {
        parent::action_index();
        if (!$this->execute) {
            return;
        }
        $this->view->pageHeader = 'Шаблоны писем';
        $this->view->tableHeader = 'Список шаблонов';
    }

    public function action_edit()
    {
        parent::action_edit();
        if (!$this->execute) {
            return;
        }
        $this->view->pageHeader = 'Шаблон №' . $this->view->item->id();
    }
} 