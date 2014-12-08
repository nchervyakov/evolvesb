<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.09.2014
 * Time: 14:30
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;

/**
 * Class NewsletterSignup
 * @property \App\Model\NewsletterSignup $model
 * @package App\Admin\Controller
 */
class NewsletterSignup extends CRUDController
{
    public $modelNamePlural = 'Подписки';

    public $modelNameSingle = 'Подписка';

    public $alias = 'newsletter-signup';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'email' => [
                    'title' => 'E-mail',
                    'type' => 'link',
                    'max_length' => '255',
                    'strip_tags' => true,
                ],
                'created_on' => [
                    'title' => 'Создано',
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
            'email' => [
                'required' => true,
                'type' => 'email'
            ],
        ];
    }

    public function action_edit()
    {
        parent::action_edit();
        if (!$this->execute) {
            return;
        }
        $this->view->pageHeader = 'Newsletter Signup №' . $this->view->item->id();
    }

    public function action_index()
    {
        parent::action_index();
        if (!$this->execute) {
            return;
        }
        $this->view->tableHeader = 'Список подписок';
    }


}