<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.09.2014
 * Time: 10:43
 */


namespace App\Model;

/**
 * Class NewsletterSignup
 * @property int $id
 * @property string $email
 * @property string $unsubscribe_token
 * @property string $created_on
 * @property string $updated_on
 *
 * @property Newsletter $newsletters
 *
 * @package App\Model
 */
class NewsletterSignup extends BaseModel
{
    public $table = 'tbl_newsletter_signups';

    protected $has_many = array(
        'newsletters' => array(
            'model' => 'Newsletter',
            'through' => 'tbl_newsletter_instances',
            'key' => 'subscriber_id',
            'foreign_key' => 'newsletter_id'
        )
    );

    /**
     * @param $email
     * @return NewsletterSignup
     */
    public function create($email)
    {
        /** @var NewsletterSignup $subscription */
        $subscription = $this->pixie->orm->get($this->model_name);
        $subscription->email = $email;
        $subscription->unsubscribe_token = sha1($email.time());
        $subscription->created_on = date('Y-m-d H:i:s');
        $subscription->updated_on = $subscription->created_on;
        $subscription->save();
        return $subscription;
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