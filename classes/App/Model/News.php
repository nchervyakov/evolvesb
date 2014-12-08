<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.09.2014
 * Time: 12:50
 */


namespace App\Model;

/**
 * Class News
 * @property int $id
 * @property string $date
 * @property string $title
 * @property string $text
 * @property string $brief
 * @property string $Pict
 * @property string $enable
 * @property string $meta_title
 * @property string $meta_keywords
 * @property string $meta_desc
 * @property string $hurl
 * @property string $canonical
 * @package App\Model
 */
class News extends BaseModel
{
    public $table = 'tbl_news';

    public function preInsert()
    {
        if (!isset($this->date) || !$this->date) {
            $this->date = date('Y-m-d H:i:s');
        }
    }

    public function preUpdate()
    {
        if (!$this->date) {
            //$this->date = date('Y-m-d H:i:s');
        }
    }
}