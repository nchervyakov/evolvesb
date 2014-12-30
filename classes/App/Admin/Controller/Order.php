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
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Helpers\ArraysHelper;
use App\Model\PaymentOperation;

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

            $paymentConfig = $this->pixie->config->get('payment');
            $isTesting = !!$paymentConfig['testing'];
            $this->view->isTesting = $isTesting;

            if ($order->isRefundable() || $isTesting) {
                $payment = $order->payment;

                $canRefundOrder = !((!$order->isRefundable() && !$isTesting) || !$payment || !$payment->loaded());

                if ($canRefundOrder) {
                    $canRefundPayment = $payment->isRefundable() || $isTesting;

                    if ($canRefundPayment) {
                        $operation = $payment->refund_operation;

                        if (!$operation || !$operation->loaded()) {
                            $operation = $this->pixie->payments->createRefundOperation($payment);
                            $payment->refund_operation_id = $operation->id();
                            $payment->save();
                        }

                        if ($operation->status != PaymentOperation::STATUS_COMPLETED) {
                            $operation->setStatus(PaymentOperation::STATUS_PENDING);
                            $operation->save();
                        }

                        $request = $this->pixie->payments->createRequestFromPaymentOperation($operation);
                        $request->setMerchantUrl($this->pixie->payments->getMerchantUrl());

                        $macFields = null;
                        if ($isTesting && ($macFieldsArr = $this->request->get('mac_fields')) && is_array($macFieldsArr)) {
                            $macFields = $macFieldsArr;
                        }
                        $request->setPSign($this->pixie->payments->calculateRequestMAC($request, $macFields));

                        $this->view->gatewayParameters = $request->getParametersArray();
                        $this->view->gatewayUrl = $paymentConfig['gateway_url'];
                    }
                }
            }


            $this->view->subview = 'order/edit';
            $this->view->pageTitle = $this->modelNameSingle . ' №' . $order->uid;
            $this->view->pageHeader = $this->view->pageTitle;
        }
    }

    public function action_refund()
    {
        if ($this->request->method != 'POST') {
            throw new HttpException("Invalid request method: " . $this->request->method);
        }

        $id = (string)$this->request->post('id');
        if (!$id) {
            throw new HttpException("Не указан номер заказа.");
        }

        /** @var \App\Model\Order $order */
        $order = $this->pixie->orm->get('Order', $id)->find();

        if (!$order || !$order->loaded()) {
            throw new NotFoundException("Заказа с номером '$id' не существует.");
        }

        if (!$order->isRefundable()) {
            throw new HttpException("Для заказа №" . $order->uid . " невозможно выполнить возврат платежа.");
        }

        $this->pixie->payments->sendRefundOrderRequest($order->id());
    }
}