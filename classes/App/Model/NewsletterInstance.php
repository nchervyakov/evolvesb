<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 25.09.2014
 * Time: 11:07
 */


namespace App\Model;

/**
 * Class NewsletterInstance
 * @property int $id
 * @property int $newsletter_id
 * @property int $subscriber_id
 * @property int $completed
 *
 * @property Newsletter $newsletter
 * @property NewsletterSignup $subscriber
 *
 * @package App\Model
 */
class NewsletterInstance extends BaseModel
{
    public $table = 'tbl_newsletter_instances';

    protected $belongs_to = [
        'newsletter' => [
            'model' => 'Newsletter',
            'key' => 'newsletter_id'
        ],
        'subscriber' => [
            'model' => 'NewsletterSignup',
            'key' => 'subscriber_id'
        ]
    ];
} 