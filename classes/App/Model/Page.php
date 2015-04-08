<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 23.09.2014
 * Time: 12:41
 */


namespace App\Model;


/**
 * Class Page
 * @property string $title
 * @property string $text
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $tag
 * @property string $alias
 * @property int $is_active
 * @property string $created_on
 * @property string $modified_on
 * @property string $h1
 * @package App\Model
 */
class Page extends BaseModel
{
    public $table = 'tbl_pages';
} 