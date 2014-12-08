<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 25.09.2014
 * Time: 10:59
 */


namespace App\Model;

/**
 * Class NewsletterTemplate
 * @property int id
 * @property string $title
 * @property string $text
 * @property string $type
 * @property string $created_on
 * @property string $updated_on
 *
 * @property Newsletter $newsletters
 *
 * @package App\Model
 */
class NewsletterTemplate extends BaseModel
{
    public $table = 'tbl_newsletter_templates';

    protected $has_many = array(
        'newsletters' => array(
            'model' => 'Newsletter',
            'key' => 'template_id',
        )
    );

    /**
     * @param array $data
     * @return NewsletterTemplate
     */
    public function create(array $data)
    {
        /** @var NewsletterTemplate $template */
        $template = $this->pixie->orm->get($this->model_name);
        $template->title = $data['title'];
        $template->text = $data['text'];
        $template->created_on = date('Y-m-d H:i:s');
        $template->updated_on = $template->created_on;
        $template->save();
        return $template;
    }

    public function preInsert()
    {
        $now = date('Y-m-d H:i:s');

        if (!isset($this->created_on) || !$this->created_on) {
            $this->created_on = $now;
        }

        if (!isset($this->updated_on) || !$this->updated_on) {
            $this->updated_on = $now;
        }
    }
} 