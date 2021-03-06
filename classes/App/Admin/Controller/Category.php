<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:02
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;

class Category extends CRUDController
{
    public $modelNamePlural = 'Категории';
    public $modelNameSingle = 'Категория';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'categoryID' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                ],
                'name' => [
                    'max_length' => 64,
                    'type' => 'link',
                    'title' => 'Название',
                ],
                'parentCategory.name' => [
                    'is_link' => true,
                    'template' => '/admin/category/%parentCategory.categoryID%',
                    'title' => 'Родительская категория'
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'column_classes' => 'dt-flag-column',
                    'title' => '+'
                ]
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function tuneModelForList()
    {
        //$this->model->with('parentCategory')->where('categoryID', '<>', 1);
    }

    public function fieldFormatter($value, $item = null, array $format = [])
    {
        if ($format['original_field_name'] == 'parentCategory.name' && $value === '0_ROOT') {
            $value = '';
        }
        return parent::fieldFormatter($value, $item, $format);
    }

    protected function getEditFields()
    {
        return [
            'categoryID' => [
                'label' => 'Id'
            ],
            'name' => [
                'type' => 'text',
                'required' => true,
                'label' => 'Название'
            ],
            'hurl' => [
                'label' => 'Alias',
            ],
            'parent' => [
                'label' => 'Родительская категория',
                'type' => 'select',
                'option_list' => [$this, 'getAvailableCategoryOptions']
            ],
            'description' => [
                'type' => 'textarea',
                'label' => 'Описание',
                'class_names' => 'js-editor',
                'row' => 6,
            ],
            'h1' => [
                'label' => 'H1',
            ],
            'enabled' => [
                'type' => 'boolean',
                'label' => 'Включена'
            ],
            'hidden' => [
                'type' => 'boolean',
                'label' => 'Скрыта'
            ],
            'product_priority' => [
                'type' => 'text',
                'label' => 'Приоритет продуктов в списках (меньше значение - выше в списке)',
            ],
            'picture' => [
                'type' => 'image',
                'dir_path' => '/products_pictures/',
                'abs_path' => false,
                'label' => 'Картинка'
            ],

            'meta_title' => [
                'type' => 'textarea',
                'label' => 'Meta Заголовок'
            ],
            'meta_keywords' => [
                'type' => 'textarea',
                'label' => 'Meta Ключевые слова'
            ],
            'meta_desc' => [
                'type' => 'textarea',
                'label' => 'Meta Описание',
            ],
        ];
    }

    public static function getAvailableCategoryOptions($pixie)
    {
        $results = [];
        /** @var \App\Model\Category $categoryModel */
        $categoryModel = $pixie->orm->get('category');
        /** @var \App\Model\Category[] $items */
        $items = $categoryModel->with('parentCategory')
            ->order_by('parentCategory.name', 'asc')->order_by('name', 'asc')->order_by('lpos', 'asc')
            ->find_all();
        foreach ($items as $item) {
            $results[$item->id()] = self::buildRecursiveName($item);
        }
        return $results;
    }

    public static function buildRecursiveName(\App\Model\Category $item = null, $path = "")
    {
        if (!$item || !$item->loaded() || !$item->id()) {
            return $path;
        }

        $parts = [$path];
        $newPart = $item->name;
        if (strtoupper($newPart) == '0_ROOT') {
            $newPart = "";
        }

        array_unshift($parts, $newPart);
        $parts = array_filter($parts);
        $path = implode(' / ', $parts);

        $path = self::buildRecursiveName($item->parentCategory, $path);

        return $path;
    }

    /**
     * @param \App\Model\Category $item
     * @param array $data
     */
    protected function preProcessEdit($item, &$data)
    {
        if ($item->id() == 1) {
            $data['parent'] = 0;
            $data['enabled'] = 1;
            $data['name'] = $item->name;
        }
    }
} 