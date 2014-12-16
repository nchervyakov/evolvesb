<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:01
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Events\Events;
use App\Events\OrderStatusChangedEvent;
use App\Exception\NotFoundException;
use App\Helpers\ArraysHelper;

class Order extends CRUDController
{
    public $modelNamePlural = 'Заказы';
    public $modelNameSingle = 'Заказ';


    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'column_classes' => 'dt-id-column',
                    'order' => 'desc'
                ],
                'uid' => [
                    'is_link' => true,
                    'title' => 'Номер заказа'
                ],
                'customer_firstname' => [
                    'title' => 'Имя',
                ],
                'customer_lastname' => [
                    'title' => 'Фамилия',
                ],
                'customer_email' => [
                    'title' => 'Email',
                ],
                'customer.username' => [
                    'title' => 'Пользователь',
                    'is_link' => true,
                    'template' => '/admin/user/edit/%customer.id%'
                ],
                'status' => [
                    'type' => 'status',
                    'title' => 'Статус'
                ],
                //'payment_method' => [],
                //'shipping_method' => []
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function getEditFields()
    {
        return [
            'id' => [],
            'status' => [
                'type' => 'select',
                'option_list' => ArraysHelper::arrayFillEqualPairs(\App\Model\Order::getOrderStatuses()),
                'required' => true,
                'label' => 'Статус',
               // 'readonly' => true
            ],
            'customer_id' => [
                'label' => 'Клиент',
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\User::getAvailableUsers',
                'required' => true
            ],
            'customer_firstname' => [
                'label' => 'Имя клиента'
            ],
            'customer_lastname' => [
                'label' => 'Фамилия клиента'
            ],
            'customer_email' => [
                'label' => 'Email'
            ],
            'amount' => [
                'label' => 'Сумма'
            ],
//            'payment_method' => [
//                'required' => true
//            ],
//            'shipping_method' => [
//                'required' => true
//            ],
            'comment' => [
                'type' => 'textarea',
                'label' => 'Комментарий'
            ],
            'created_at' => [
                'data_type' => 'date',
                'label' => 'Создан'
            ],
            'updated_at' => [
                'data_type' => 'date',
                'label' => 'Обновлён'
            ],
        ];
    }

    protected function tuneModelForList()
    {
        $this->model->with('customer');
    }

    public function action_edit()
    {
        $oldStatus = null;
        $id = $this->request->param('id');

        if ($this->request->method == 'POST') {
            $item = null;
            if ($id) {
                /** @var \App\Model\Order $item */
                $item = $this->pixie->orm->get($this->model->model_name, $id);
            }


            if (!$item || !$item->loaded()) {
                throw new NotFoundException();
            }

            $oldStatus = $item->status;
        }

        parent::action_edit();

        /** @var \App\Model\Order $order */
        $order = $this->pixie->orm->get($this->model->model_name, $id);

        if ($this->request->method == 'POST') {
            if ($oldStatus != $order->status) {
                $this->pixie->dispatcher->dispatch(Events::ORDER_STATUS_CHANGED, new OrderStatusChangedEvent($order, $order->status));
            }
        }

        if (!$this->execute) {
            return;
        }

        $this->view->order = $order;
        $this->view->orderItems = $order->orderItems->find_all()->as_array();
        if ($order->id()) {
            $this->view->subview = 'order/edit';
            $this->view->pageTitle = $this->modelNameSingle . ' №' . $order->uid;
            $this->view->pageHeader = $this->view->pageTitle;
        }

    }
}