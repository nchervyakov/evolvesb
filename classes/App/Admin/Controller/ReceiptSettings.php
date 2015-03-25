<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.03.2015
 * Time: 21:21
 */


namespace App\Admin\Controller;


use App\Admin\Controller;

class ReceiptSettings extends Controller
{
    public function action_index()
    {
        $data = $this->pixie->config->get('parameters.receipt', []);
        $errors = [];

        if ($this->request->method == 'POST') {
            $fields = ['bank_name', 'bank_bic', 'bank_account', 'company_account', 'company_name', 'company_address', 'company_inn', 'company_kpp', 'facsimile'];
            $newData = $this->request->post();

            if ($_FILES['facsimile'] && $_FILES['facsimile']['tmp_name']) {
                $targetDir = __DIR__.'./../../../../web';
                $relativePath = '/upload/images/' . $_FILES['facsimile']['name'];
                $targetPath = $targetDir . '/' . $relativePath;
                move_uploaded_file($_FILES['facsimile']['tmp_name'], $targetPath);
                $newData['facsimile'] = $relativePath;

            } else {
                $newData['facsimile'] = $data['facsimile'];
            }

            foreach ($fields as $field) {
                if (!trim($newData[$field])) {
                    $errors[$field][] = 'Пожалуйста заполните это поле';
                }
            }

            $data = array_merge($data, $newData);
            //var_dump($data, $newData, $errors, $_FILES);exit;
            if (!count($errors)) {
                $this->pixie->config->set('parameters.receipt', $data);
                $this->pixie->config->write('parameters');

                $this->pixie->session->flash('success', "Конфигурация успешно сохранена");
                $this->redirect('/admin/receipt-settings');
                return;
            }
        }

        $this->view->subview = 'settings/receipt/index';
        $this->view->pageTitle = "Параметры квитанции";
        $this->view->pageHeader = $this->view->pageTitle;
        $this->view->data = $data;
        $this->view->errors = $errors;
    }
}