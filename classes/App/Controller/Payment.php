<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 11.12.2014
 * Time: 13:27
 */


namespace App\Controller;



use App\Exception\ForbiddenException;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\Order;
use App\Model\PaymentOperation;
use App\Page;
use App\Payment\Request;

class Payment extends Page
{
    /** @var  Order */
    protected $order;

    public function before()
    {
        $this->secure();
        parent::before();
    }

    public function action_return_url()
    {
        try {
            $this->processResponse();

        } catch (HttpException $e) {
            if ($this->order) {
                $this->pixie->session->flash("payment_error", "При оплате произошла ошибка. Попробуйте снова.");
                $this->redirect("/checkout/payment/" . $this->order->uid);
                exit;

            } else {
                throw $e;
            }
        }

        throw new ForbiddenException();
    }

    /**
     * Handles
     * @throws ForbiddenException
     */
    public function processResponse()
    {
        if (!$this->request->method == 'POST') {
            throw new ForbiddenException();
        }

        $data = $this->request->post();
        $orderUid = $data['ORDER'];
        $transactionType = $data['TRTYPE'];

        if (!$orderUid) {
            throw new ForbiddenException();
        }

        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->getByUid($orderUid);

        if (!$order || !$order->payment || !$order->payment->loaded()) {
            throw new ForbiddenException();
        }

        $this->order = $order;

        switch ($transactionType) {
            case Request::TR_TYPE_AUTHORIZED_PAYMENT:
                break;

            case Request::TR_TYPE_IMMEDIATE_PAYMENT:
                $this->respondPay($order, $data);
                return;


        }
    }

    public function respondPay(Order $order, $data)
    {
        $operation = $order->payment->payment_operation;
        if (!!$operation || !$operation->loaded()) {
            throw new ForbiddenException();
        }

        if (!$this->checkFieldsMatch($order, $order->payment, $operation, $data)) {
            throw new ForbiddenException();
        }

        if ($operation->status != PaymentOperation::STATUS_COMPLETED) {
            $operation->rrn = $data['RRN'];
            $operation->action = $data['ACTION'];
            $operation->rc = $data['RC'];
            $operation->save();
        }

        if ($data['ACTION'] = 0 && $data['RC'] == 0) {
            $order->payment->status = \App\Model\Payment::STATUS_PAYED;
            $order->payment->save();
        }

        if ($data['ACTION'] == 1 || $data['ACTION'] == 0) {
            $this->redirect('/checkout/order/' . $order->id());
            exit;
        }
    }

    protected function checkFieldsMatch(Order $order, \App\Model\Payment $payment, PaymentOperation $operation, $data)
    {
        return $order->amount == $data['AMOUNT']
            && $payment->currency == $data['CURRENCY']
            && $operation->merchant_name == $data['MERCH_NAME']
            && $operation->terminal == $data['TERMINAL'];
    }


    public function action_pay() {
        $orderUid = $this->request->param('id');

        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->where('uid', $orderUid)->find();

        if (!$order || !$order->loaded()) {
            throw new NotFoundException("Заказа с номером $orderUid не существует.");
        }

        if ($order->isPayed()) {
            throw new HttpException("Заказ №" . $order->uid . " уже оплачен.");
        }

        if ($order->status == Order::STATUS_CANCELLED) {
            throw new HttpException("Заказ №" . $order->uid . " отменён.");
        }

        $this->pixie->payments->sendPayOrderRequest($order->id());
    }
}