<?php

namespace App\Controller;

use App\Events\OrderStatusChangedEvent;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Helpers\UserPictureUploader;
use App\Model\Order;
use App\Model\User;
use App\Page;
use App\Rest\Events;
use PHPixie\Paginate\Pager\ORM as ORMPager;

class Account extends Page {

    /**
     * require auth
     */
    public function before() {
        $this->secure();
        if (is_null($this->getUser())) {
            $this->redirect('/user/login?return_url=' . rawurlencode($this->request->server('REQUEST_URI')));
        }
        parent::before();
    }

    public function action_index() {
        /** @var ORMPager $ordersPager */
        $ordersPager = $this->pixie->orm->get('Order')->order_by('created_at', 'DESC')->getMyOrdersPager(1, 20);
        $myOrders = $ordersPager->current_items()->as_array();
        $this->view->user = $this->pixie->auth->user();
        $this->view->myOrders = $myOrders;
        $this->view->subview = 'account/account';
    }

    public function action_orders()
    {
        /** @var Order $orderModel */
        $orderModel = $this->pixie->orm->get('Order');

        if ($orderId = $this->request->param('id')) { // Show single order
            $order = $orderModel->getByIncrement($orderId);
            if (!$order->loaded() || $order->customer_id != $this->getUser()->id()) {
                throw new NotFoundException();
            }
            $this->view->id = $orderId;
            $this->view->order = $order;
            $this->view->items = $order->orderItems->find_all()->as_array();
            $this->view->subview = 'account/order';

        } else { // List orders
            $page = $this->request->get('page', 1);
            /** @var ORMPager $ordersPager */
            $ordersPager = $orderModel->order_by('created_at', 'DESC')->getMyOrdersPager($page, 20);
            $myOrders = $ordersPager->current_items()->as_array();
            $this->view->pager = $ordersPager;
            $this->view->myOrders = $myOrders;
            $this->view->subview = 'account/orders';
        }
    }

    public function action_cancel_order()
    {
        if ('POST' != $this->request->method) {
            throw new HttpException('Некорректный метод запроса', 405, null, 'Method Not Allowed');
        }

        $uid = $this->request->post('uid');

        if (!$uid) {
            throw new HttpException("Не указан ID заказа");
        }

        /** @var Order $orderModel */
        $orderModel = $this->pixie->orm->get('Order');

        $order = $orderModel->getByIncrement($uid);
        if (!$order->loaded() || $order->customer_id != $this->getUser()->id()) {
            throw new NotFoundException("Заказа №$uid не существует.");
        }

        if (!$order->isCancellable()) {
            throw new HttpException("Заказ №$uid нельзя отменить.");
        }

        $order->status = Order::STATUS_CANCELLED;
        $order->save();
        $this->pixie->dispatcher->dispatch(\App\Events\Events::ORDER_STATUS_CHANGED, new OrderStatusChangedEvent($order, $order->status));

        $this->redirect('/account/orders/' . $uid);
    }

    public function action_edit_profile()
    {
        $user = $this->getUser();
        $fields = ['first_name', 'last_name', 'user_phone', 'password'];
        $errors = [];
        $this->view->success = false;

        if ($this->request->method == 'POST') {
            $this->checkCsrfToken('profile');

            $photo = $this->request->uploadedFile('photo', [
                'extensions' => ['jpeg', 'jpg', 'gif', 'png'],
                'types' => ['image']
            ]);

            if ($photo->isLoaded() && !$photo->isValid()) {
                $errors[] = 'Некорректное изображение для аватарки.';
            }

            $passwordConfirmation = $this->request->post('password_confirmation');
            $data = $user->filterValues($this->request->post(), $fields);

            if (!$data['password'] && !$passwordConfirmation) {
                unset($data['password']);

            } else {
                if ($data['password'] != $passwordConfirmation) {
                    $errors[] = 'Passwords must match.';

                } else {
                    $data['password'] = $this->pixie->auth->provider('password')->hash_password($data['password']);
                }
            }

            if (!count($errors)) {
                UserPictureUploader::create($this->pixie, $user, $photo, $this->request->post('remove_photo'))
                    ->execute();

                $user->values($data);
                $user->save();

                $this->pixie->session->flash('success', 'Вы успешно обновили свой профиль.');

                if ($this->request->post('_submit2')) {
                    $this->redirect('/account#profile');

                } else {
                    $this->redirect('/account/profile/edit');
                }

                return;

            } else {
                $data['photo'] = $user->photo;
                $data['password'] = '';
                $data['password_confirmation'] = '';
            }

        } else {
            $data = $user->getFields(array_merge($fields, ['photo']));
            $data['password'] = '';
            $data['password_confirmation'] = '';
        }

        foreach ($data as $key => $value) {
            $this->view->$key = $value;
        }
        $this->view->success = $this->pixie->session->flash('success') ?: '';
        $this->view->errorMessage = implode('<br>', $errors);
        $this->view->user = $user;
        $this->view->subview = 'account/edit_profile';
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->pixie->auth->user();
    }
}