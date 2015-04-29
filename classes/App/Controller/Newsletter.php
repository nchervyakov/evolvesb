<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.09.2014
 * Time: 10:41
 */


namespace App\Controller;


use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\NewsletterSignup;
use App\Page;

/**
 * Class Newsletter
 * @property NewsletterSignup $model
 * @package App\Controller
 */
class Newsletter extends Page
{
    protected $modelName = 'NewsletterSignup';

    public $mainView = 'maintpl';

    public function action_signup()
    {
        if ($this->request->method != 'POST') {
            throw new HttpException('Method Not Allowed', 405, null, 'Method Not Allowed');
        }

        $email = $this->request->post('email');

        if (!$email || !\Swift_Validate::email($email)) {
            $this->jsonResponse(['error' => 1, 'message' => 'Пожалуйста укажите корректный адрес email.']);
            return;
        }

        /** @var NewsletterSignup $subscription */
        $subscription = $this->model->where('email', $email)->find();
        if (!$subscription->loaded()) {
            $subscription = $this->model->create($email);
        }

        if ($subscription->loaded()) {
            $this->sendSubscribeEmail($subscription);
            $this->jsonResponse(['success' => 1, 'message' => 'Благодарим за подписку!']);

        } else {
            $this->jsonResponse(['error' => 1, 'message' => 'Произошла ошибка.']);
        }
    }

    public function action_unsubscribe()
    {
        $token = $this->request->get('token');

        if (!$token) {
            throw new NotFoundException;
        }

        /** @var NewsletterSignup $subscription */
        $subscription = $this->model->where('unsubscribe_token', $token)->find();
        if (!$subscription->loaded()) {
            throw new NotFoundException;
        }

        $subscription->delete();

        $this->view->subview = 'newsletter/unsubscribed';
        $this->view->pageTitle = 'Unsubscribtion';
        $this->view->pageHeader = 'Unsubscribtion';
    }

    public function sendSubscribeEmail(NewsletterSignup $subscription)
    {
        $parameters = $this->pixie->config->get('parameters') ?: [];
        $robotEmail = $parameters['robot_email'] ?: 'robot@evolveskateboards.ru';
        $view = $this->pixie->view('newsletter/signup_email');
        $view->siteUrl = $this->request->getSiteUrl();
        $view->subscription = $subscription;
        $this->pixie->email->send($subscription->email, $robotEmail, 'Newsletter Subscription', $view->render());
    }
}