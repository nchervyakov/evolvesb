<?php
namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\Cart as CartModel;
use App\Model\Cart;
use App\Model\Order;
use App\Model\PaymentOperation;
use App\Page;

class Checkout extends Page {

    /**
     * require auth
     */
    public function before()
    {
        $this->secure();
        if (is_null($this->pixie->auth->user())) {
            $this->redirect('/user/login?return_url=' . rawurlencode($this->request->server('REQUEST_URI')));
        }
        parent::before();
    }

    /**
     * validate restrict for actions by last step checkout
     * @param int $actionStep
     */
    protected function restrictActions($actionStep)
    {
        $lastStep = $this->pixie->orm->get('Cart')->getCart()->last_step;
        if ($lastStep == CartModel::STEP_ORDER && $actionStep != CartModel::STEP_ORDER) {
            $this->redirect('/checkout/order');
        }
        if ($actionStep > $lastStep) {
            if ($this->request->is_ajax()) {
                $this->jsonResponse(['success' => 1]);
            } else {
                $this->redirect('/cart/view');
            }
        }
    }

    /**
     * 1. Set cart customer
     * 2. if ajax create/update customer addresses
     * 3. default show shipping step
     */
    public function action_shipping() {
        $this->restrictActions(CartModel::STEP_SHIPPING);
        $customerAddresses = $this->pixie->orm->get('CustomerAddress')->getAll();
        /** @var Cart $cartModel */
        $cartModel = $this->pixie->orm->get('Cart');
        $cart = $cartModel->getCart();

        if (!$cart->customer_id) {
            $cartModel->setCustomer();
        }

        if ($this->request->is_ajax()) {
            $this->checkCsrfToken('checkout_step2', null, false);

            $post = $this->request->post();
            $addressId = isset($post['address_id']) ? $post['address_id'] : 0;
            if (!$addressId) {
                $addressId = $this->pixie->orm->get('CustomerAddress')->create($post);
            }
            $this->pixie->orm->get('Cart')->updateAddress($addressId, 'shipping');
            $this->execute = false;
        } else {
            $this->view->subview = 'cart/shipping';
            $this->view->tab = 'shipping';//active tab
            $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
            $currentAddress = $cart->getShippingAddress() ;
            $this->view->shippingAddress = is_array($currentAddress) ? [] : $currentAddress->as_array();
            $this->view->customerAddresses = $customerAddresses;
        }
    }

//    /**
//     * if ajax create/update customer addresses
//     * default show billing step
//     */
//    public function action_billing() {
//        $this->restrictActions(CartModel::STEP_BILLING);
//        $customerAddresses = $this->pixie->orm->get('CustomerAddress')->getAll();
//
//        if ($this->request->is_ajax()) {
//            $this->checkCsrfToken('checkout_step3', null, false);
//
//            $post = $this->request->post();
//            $addressId = isset($post['address_id']) ? $post['address_id'] : 0;
//            if (!$addressId) {
//                $addressId = $this->pixie->orm->get('CustomerAddress')->create($post);
//            }
//            $this->pixie->orm->get('Cart')->updateAddress($addressId, 'billing');
//            $this->execute = false;
//
//        } else {
//            /** @var Cart $cartModel */
//            $cartModel = $this->pixie->orm->get('Cart');
//            $cart = $cartModel->getCart();
//
//            $this->view->subview = 'cart/billing';
//            $this->view->tab = 'billing';
//            $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
//            $currentAddress = $cart->getBillingAddress();
//            $this->view->billingAddress = is_array($currentAddress) ? [] : $currentAddress->as_array();
//            $this->view->customerAddresses = $customerAddresses;
//        }
//    }

    /**
     * ajax action return CustomerAddress json by address id
     */
    public function action_getAddress()
    {
        $post = $this->request->post();
        $addressId = $post['address_id'];
        $address = $this->pixie->orm->get('CustomerAddress')->getById($addressId);
        $this->jsonResponse($address);
    }

    /**
     * delete address by id
     */
    public function action_deleteAddress()
    {
        $post = $this->request->post();
        $addressId = $post['address_id'];
        $this->pixie->orm->get('CustomerAddress')->deleteById($addressId);
        $this->execute = false;
    }

    /**
     * show confirmation step
     */
    public function action_confirmation()
    {
        $this->restrictActions(CartModel::STEP_CONFIRM);
        $this->view->subview = 'cart/confirmation';
        $this->view->tab = 'confirmation';
        $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
        $this->view->cart = $this->pixie->orm->get('Cart')->getCart();
    }

    /**
     * ajax action which create order
     */
    public function action_placeOrder()
    {
        $this->checkCsrfToken('checkout_step4', null, false);
        $this->restrictActions(CartModel::STEP_ORDER);

        /** @var Order $order */
        $order = $this->pixie->orm->get('Cart')->placeOrder();

        $result = [];

        if ($order && $order->loaded()) {
            $this->pixie->orm->get('Cart')->getCart()->delete();

            $result['order_uid'] = $order->uid;
            $result['success'] = 1;
            if ($this->request->is_ajax()) {
                $this->jsonResponse($result);
            } else {
                $this->redirect("/checkout/payment/" . $order->uid);
            }

        } else {
            $result['error'] = 1;
            if ($this->request->is_ajax()) {
                $this->jsonResponse($result);
            } else {
                $this->redirect("/checkout/confirmation");
            }
        }

        $this->execute = false;
    }

    public function action_payment()
    {
        $orderUid = $this->request->param('id');
        if (!$orderUid) {
            throw new NotFoundException();
        }

        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->getByUid($orderUid);
        if (!$order) {
            throw new NotFoundException();
        }

        if (!in_array($order->status, [Order::STATUS_NEW, Order::STATUS_WAITING_PAYMENT])) {
            $this->redirect('/checkout/order/' . $order->uid);
        }

        $paymentConfig = $this->pixie->config->get('payment');
        $usePost = $paymentConfig['use_post_for_request'];

        if ($usePost) {
            if (!$order->isPayable()) {
                $this->redirect('/account/orders/' . $order->uid);
                return;
                //throw new \RuntimeException("Order {$order->uid} cannot be payed.");
            }

            $payment = $order->payment;
            if (!$payment || !$payment->loaded()) {
                $payment = $this->pixie->payments->createOrderPayment($order);
                $order->refresh();
            }

            if (!$payment->isPayable()) {
                throw new \RuntimeException("Payment for order {$order->uid} cannot be performed.");
            }

            $operation = $payment->payment_operation;
            if (!$operation || !$operation->loaded() || $operation->status != PaymentOperation::STATUS_COMPLETED) {
                $operation = $this->pixie->payments->createImmediatePaymentOperation($payment);
                $payment->payment_operation_id = $operation->id();
                $payment->save();
            }
            $operation->setStatus(PaymentOperation::STATUS_PENDING);
            $operation->save();
            $request = $this->pixie->payments->createRequestFromPaymentOperation($operation);
            $request->setPSign($this->pixie->payments->calculateRequestMAC($request));

            $this->view->gatewayParameters = $request->getParametersArray();
            $this->view->gatewayUrl = $paymentConfig['gateway_url'];
        }


        $this->view->usePost = $usePost;
        $this->view->flash = $this->pixie->session->flash('payment_error');
        $this->view->subview = 'cart/payment';
        $this->view->tab = 'payment';
        $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel(CartModel::STEP_PAYMENT);
        $this->view->orderUid = $order->uid;
    }

    /**
     * show order step success
     */
    public function action_order()
    {
        $orderUid = $this->request->param('id');
        if (!$orderUid) {
            throw new NotFoundException();
        }

        /** @var Order $order */
        $order = $this->pixie->orm->get('Order')->getByUid($orderUid);
        if (!$order) {
            throw new NotFoundException();
        }

        if ($order->isPayable()) {
            $this->redirect('/checkout/payment/' . $order->uid);

        } else if ($order->success_notified) {
            $this->redirect('/account/orders/' . $order->uid);
            return;
        }

        $order->success_notified = 1;
        $order->save();

        //$this->restrictActions(CartModel::STEP_ORDER);
        $this->view->subview = 'cart/order';
        $this->view->tab = 'order';
        $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel(CartModel::STEP_ORDER);
    }

    /**
     * set shipping & payment methods
     */
    public function action_index() {
        /** @var \App\Model\Cart $cart */
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $cart->message = '';
        $cart->save();
        $this->pixie->orm->get('Cart')->updateLastStep(CartModel::STEP_SHIPPING);
        $this->redirect('/checkout/shipping');
    }
}