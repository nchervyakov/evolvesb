<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.08.2014
 * Time: 11:53
 */


namespace App\Controller;


use App\Exception\HttpException;
use App\Page;

class Error extends Page
{
    public $mainView = 'maintpl';

    public function action_view()
    {
        /** @var \Exception|HttpException $exception */
        $exception = $this->request->param('exception', null, false);
        $status = method_exists($exception, 'getStatus')
            ? $exception->getStatus() : $exception->getCode() . ' ' .$exception->getMessage();

        header($this->request->server("SERVER_PROTOCOL").' '.$status);
        header("Status: {$status}");

        if (strpos(strtolower($this->request->server('HTTP_ACCEPT')), 'json') !== false) {
            if ($exception) {
                $this->jsonResponse([
                    'error' => true,
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'status' => $status
                ]);

            } else {
                $this->jsonResponse(['error' => true]);
            }

        } else {
            $this->view->subview = 'error/view';
            $this->view->exception = $exception;
            $this->view->pageTitle = 'Error: ' . $exception->getCode() . ' ' . $exception->getMessage();
            $this->view->pageHeader = $this->view->pageTitle;
        }
    }
} 