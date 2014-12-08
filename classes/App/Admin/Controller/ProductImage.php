<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 18.09.2014
 * Time: 14:21
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Exception\HttpException;
use App\Exception\NotFoundException;

class ProductImage extends CRUDController
{
    protected function getListFields()
    {
        return [
            'id' => [
                'type' => 'text'
            ],
            'file_name_big' => [
                'label' => 'File Name',
                'max_length' => '255',
                'strip_tags' => true,
            ],

            'file_thumb' => [
                'extra' => true,
                'type' => 'image',
                'dir_path' => '',
                'max_width' => 40,
                'max_height' => 30,
                'is_link' => true,
                'column_classes' => 'dt-picture-column',
                'title' => 'Pic',
                'thumbnail' => 'tiny',
                'image_field' => 'file_name_big'
            ],
            'title' => [
                'max_length' => 40,
                'type' => 'text'
            ],
            'title_full' => [
                'type' => 'text',
                'extra' => true,
                'field' => 'title'
            ],
            'edit' => [
                'extra' => true,
                'type' => 'html',
                'template' => '<a href="#" data-id="%'.$this->model->id_field.'%" '
                    . ' class="js-edit-image">Редактировать</a>',
                'column_classes' => 'edit-action-column'
            ],
            'delete' => [
                'extra' => true,
                'type' => 'html',
                'template' => '<a href="#" data-id="%'.$this->model->id_field.'%" '
                    . ' class="js-delete-image">Удалить</a>',
                'column_classes' => 'delete-action-column'
            ]
        ];
    }

    protected function tuneModelForList()
    {
        if ($productId = $this->request->get('product_id')) {
            $this->model->where('product_id', $productId);
        }
    }

    public function action_save()
    {
        if ($this->request->method != 'POST') {
            throw new HttpException('Method Not Allowed', 405, null, 'Method Not Allowed');
        }
        $data = $this->request->post();
        $imageId = $data['id'];
        unset($data['id']);

        if ($imageId) {
            /** @var \App\Model\ProductImage $prodImage */
            $prodImage = $this->pixie->orm->get('ProductImage', $imageId);
            if (!$prodImage || !$prodImage->loaded()) {
                throw new NotFoundException();
            }

            unset($data['product_id']);
            unset($_FILES['file_name_big']);

        } else {
            $prodImage = $this->pixie->orm->get('ProductImage');
            if (!$data['product_id']) {
                throw new \LogicException('You must provide product id.');
            }

            $product = $this->pixie->orm->get('Product', $data['product_id']);
            if (!$product || !$product->loaded()) {
                throw new NotFoundException('Product with ID=' . $data['product_id'] . ' does not exist.');
            }
        }

        $this->processRequestFiles($prodImage);
        $prodImage->values($prodImage->filterValues($data));
        $prodImage->save();

        $this->jsonResponse(['success' => true, 'image' => $prodImage->as_array(true)]);
    }

    public function processRequestFiles(\App\Model\ProductImage $image)
    {
        $field = 'file_name_big';
        $file = $this->request->uploadedFile($field);

        if (!$file->isLoaded()) {
            return;
        }

        $fileName = $file->generateFileName($this->user->id());
        $dirPath = $this->pixie->root_dir . 'web/products_pictures/';
        $destPath = $dirPath . $fileName;

        $file->move($destPath);
        $image->$field = $fileName;
        $image->file_name = $fileName;
    }

    public function action_delete()
    {
        if ($this->request->method != 'POST') {
            throw new HttpException('Method Not Allowed', 405, null, 'Method Not Allowed');
        }

        $id = $this->request->post('id');

        if (!$id) {
            throw new NotFoundException();
        }

        $image = $this->pixie->orm->get('ProductImage', $id);

        if (!$image || !$image->loaded()) {
            throw new NotFoundException();
        }

        $image->delete();
        $this->jsonResponse(['success' => 1]);
    }
} 