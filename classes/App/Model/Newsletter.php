<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 25.09.2014
 * Time: 11:04
 */


namespace App\Model;


/**
 * Class Newsletter
 * @property int $id
 * @property int $template_id
 * @property string $subject
 * @property string $status
 * @property string $send_to_all
 * @property string $subscriber_ids
 * @property string $content
 * @property int $recipient_count
 * @property int $completed
 * @property int $completed_count
 * @property string $created_on
 * @property string $updated_on
 * @property string $completed_on
 *
 * @property NewsletterTemplate $template
 * @property NewsletterInstance $instances
 *
 * @package App\Model
 */
class Newsletter extends BaseModel
{
    const STATUS_NEW = 'new';
    const STATUS_SENDING = 'sending';
    const STATUS_COMPLETE = 'complete';

    public $table = 'tbl_newsletters';

    protected $belongs_to = [
        'template' => [
            'model' => 'NewsletterTemplate',
            'key' => 'template_id'
        ]
    ];

    protected $has_many = array(
        'instances' => array(
            'model' => 'NewsletterInstance',
            'key' => 'newsletter_id',
        )
    );

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