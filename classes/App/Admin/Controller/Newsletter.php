<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 25.09.2014
 * Time: 11:41
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\NewsletterInstance;
use App\Model\NewsletterSignup;
use App\Pixie;
use PHPixie\ORM\Model;

/**
 * Class Newsletter
 * @property \App\Model\Newsletter $model
 * @package App\Admin\Controller
 */
class Newsletter extends CRUDController
{
    public $editView = 'newsletter/edit';

    public $modelNamePlural = 'Рассылки';

    public $modelNameSingle = 'Рассылка';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'subject' => [
                    'title' => 'Тема',
                    'is_link' => true
                ],
                'template.title' => [
                    'title' => 'Шаблон',
                    'type' => 'link',
                    'max_length' => '255',
                    'strip_tags' => true,
                ],
                'template.type' => [
                    'title' => 'Тип'
                ],
                'recipient_count' => [
                    'title' => 'Кол-во получателей',
                ],
                'completed_count' => [
                    'title' => 'Кол-во отправленных',
                ],
                'completed' => [
                    'title' => 'Завершено?',
                    'type' => 'boolean'
                ],
                'created_on' => [
                    'title' => 'Создано',
                ],
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function tuneModelForList()
    {
        $this->model->with('template');
    }

    protected function getEditFields()
    {
        return [
            'id' => [],
            'template_id' => [
                'label' => 'Шаблон',
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\Newsletter::getTemplatesList',
                'required' => true
            ],
            'subject' => [
                'label' => 'Тема',
                'type' => 'text',
                'required' => true
            ],
            'content' => [
                'label' => 'Содержимое',
                'type' => 'textarea',
                'class_names' => 'js-editor',
            ]
        ];
    }

    public function action_edit()
    {
        parent::action_edit();
        if (!$this->execute) {
            return;
        }

        $subscriberIds = isset($this->view->item->subscriber_ids) ? $this->view->item->subscriber_ids : '';
        $this->view->selectedSubscribers = preg_split('/\s*,\s*/', $subscriberIds, -1, PREG_SPLIT_NO_EMPTY);
        $this->view->subscribers = $this->getSubscribers();
        $this->view->pageHeader = 'Письмо №' . $this->view->item->id();
    }

    public function action_new()
    {
        parent::action_new();
        if (!$this->execute) {
            return;
        }

        $subscriberIds = isset($this->view->item->subscriber_ids) ? $this->view->item->subscriber_ids : '';
        $this->view->selectedSubscribers = preg_split('/\s*,\s*/', $subscriberIds, -1, PREG_SPLIT_NO_EMPTY);
        $this->view->subscribers = $this->getSubscribers();
    }

    public function getSubscribers()
    {
        $list = $this->pixie->orm->get('NewsletterSignup')->order_by('email', 'asc')->find_all()->as_array();
        uasort($list, function ($item1, $item2) {
            return strnatcasecmp($item1->email, $item2->email);
        });
        return $list;
    }

    public function action_index()
    {
        parent::action_index();
        if (!$this->execute) {
            return;
        }
        $this->view->tableHeader = 'Список рассылок';
    }

    public static function getTemplatesList(Pixie $pixie)
    {
        $tpls = $pixie->orm->get('NewsletterTemplate')->order_by('title', 'asc')->find_all()->as_array();
        $templates = [];
        foreach ($tpls as $tpl) {
            $templates[$tpl->id()] = $tpl->title;
        }
        return $templates;
    }

    public function action_subscribers_table()
    {
        $newsletterId = $this->request->get('newsletter_id');
        $subscriberIds = '';

        if ($newsletterId) {
            $newsletter = $this->model->where('id', $newsletterId)->find();
            if ($newsletter->loaded()) {
                $subscriberIds = $newsletter->subscriber_ids;
            }
        }

        $this->view = $this->view('newsletter/_subscribers_table');
        $this->view->selectedSubscribers = preg_split('/\s*,\s*/', $subscriberIds, -1, PREG_SPLIT_NO_EMPTY);
        $this->view->subscribers = $this->getSubscribers();

        $this->jsonResponse(['html' => $this->view->render()]);
    }

    /**
     * @param \App\Model\Newsletter|\PHPixie\ORM\Model $newsletter
     */
    protected function onSuccessfulEdit(Model $newsletter)
    {
        if ($this->request->post('send')) {
            $this->pixie->db->query('delete')->table('tbl_newsletter_instances')
                ->where('newsletter_id', $newsletter->id())->execute();

            $sql = "INSERT INTO tbl_newsletter_instances (newsletter_id, subscriber_id)\n"
                ."SELECT ".$newsletter->id().", ns.id\n"
                . "FROM tbl_newsletter_signups ns";

            if (!$newsletter->send_to_all) {
                $subscriberIds = preg_split('/\s*,\s*/', $newsletter->subscriber_ids, -1, PREG_SPLIT_NO_EMPTY);

                if (!$subscriberIds) {
                    $newsletter->save();
                    return;
                }
                $sql .= "\nWHERE ns.id IN (" . implode(', ', $subscriberIds) . ")";
            }

            /** @var \PDO $conn */
            $conn = $this->pixie->db->get()->conn;
            $conn->exec($sql);

            $newsletter->recipient_count = $newsletter->instances->count_all();
            $newsletter->status = \App\Model\Newsletter::STATUS_SENDING;
            $newsletter->save();
        }
    }

    /**
     * @param \App\Model\Newsletter $item
     * @param array $data
     * @throws \App\Exception\HttpException
     */
    protected function preProcessEdit($item, $data)
    {
        if ($item->status != \App\Model\Newsletter::STATUS_NEW) {
            throw new HttpException('You can\'t change started newsletter');
        }
    }

    public function action_send()
    {
        $newsletterId = $this->request->get('newsletter_id');

        if (!$newsletterId) {
            throw new NotFoundException;
        }

        /** @var \App\Model\Newsletter $newsletter */
        $newsletter = $this->model->where('id', $newsletterId)->find();
        if (!$newsletter->loaded()) {
            throw new NotFoundException;
        }

        if ($newsletter->status != \App\Model\Newsletter::STATUS_SENDING) {
            throw new NotFoundException('Only newsletters with status "sending" can be sent.');
        }

        $totalRemaining = $newsletter->instances->count_all();

        $instances = $newsletter->instances->limit(10)->find_all()->as_array();

        foreach ($instances as $instance) {
            $this->sendNewsletter($instance, $newsletter);
            $instance->delete();
        }

        $newsletter->completed_count = $newsletter->recipient_count - $totalRemaining + count($instances);
        $completed = count($instances) == 0 || $newsletter->completed_count >= $newsletter->recipient_count;

        if ($completed) {
            $newsletter->completed = $completed;
            $newsletter->status = \App\Model\Newsletter::STATUS_COMPLETE;
            $newsletter->completed_on = date('Y-m-d H:i:s');
        }
        $newsletter->save();

        $this->jsonResponse([
            'completedCount' => $newsletter->completed_count,
            'complete' => $completed
        ]);
    }

    public function sendNewsletter(NewsletterInstance $instance, \App\Model\Newsletter $newsletter)
    {
        $template = $newsletter->template;
        $tplHtml = $template->text;

        if (strpos($tplHtml, '%content%') != false) {
            $tplHtml = str_replace('%content%', $newsletter->content, $tplHtml);
        }

        $result = str_replace(
            [
                '%email%',
                '%date%',
                '%date_local%',
                '%title%',
                '%subject%',
            ],
            [
                $instance->subscriber->email,
                date('Y.m.d'),
                $this->view->getHelper()->localDate(time(), 'ru'),
                $template->title,
                $newsletter->subject,
            ],
            $tplHtml
        );

        $this->pixie->email->send(
            $instance->subscriber->email,
            'robot@evolveskateboards.ru',
            $newsletter->subject,
            $result,
            $template->type == 'html'
        );
    }
}