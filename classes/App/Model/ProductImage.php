<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 26.09.2014
 * Time: 14:51
 */


namespace App\Model;

/**
 * Class ProductImage
 * @property int $id
 * @property int $product_id
 * @property string $file_name
 * @property string $file_name_big
 * @property string $title
 * @property string $created_on
 * @property string $updated_on
 * @package App\Model
 */
class ProductImage extends BaseModel
{
    public $table = 'tbl_product_images';

    protected $belongs_to = array(
        'product' => array(
            'model' => 'Product',
            'key' => 'product_id'
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