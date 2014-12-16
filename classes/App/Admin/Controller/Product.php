<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:02
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Admin\FieldFormatter;
use App\Exception\NotFoundException;
use App\Model\Category;
use App\Model\Option;
use App\Model\OptionValue;
use App\Model\ProductOptionValue;
use App\Model\Role;
use App\Pixie;

class Product extends CRUDController
{
    public $modelNamePlural = 'Products';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'productID' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                    'data_type' => 'integer',
                ],
                'name' => [
                    'max_length' => 64,
                    'type' => 'link'
                ],
                'category.name' => [
                    'title' => 'Category',
                    'type' => 'link',
                    'template' => '/admin/category/%category.categoryID%',
                    'width' => 150
                ],
                'Price' => [
                    'value_prefix' => '$',
                    'data_type' => 'integer',
                ],
                'picture' => [
                    'type' => 'image',
                    'dir_path' => '/products_pictures/',
                    'max_width' => 40,
                    'max_height' => 30,
                    'is_link' => true,
                    'column_classes' => 'dt-picture-column',
                    'title' => 'Pic',
                    'thumbnail' => 'tiny'
                ]
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function tuneModelForList()
    {
        $this->model->with('category');
    }

    protected function getEditFields()
    {
         $fields = [
            'productID' => [
                'label' => 'Id'
            ],
            'hurl' => [
                'label' => 'Алиас',
                'type' => 'text',
                'required' => true,
                'class_names' => 'js-alias-field js-suggest-source',
            ],
            'name' => [
                'label' => 'Наименование',
                'required' => true,
                'class_names' => 'js-suggest-source'
            ],
            'categoryID' => [
                'label' => 'Категория',
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\Category::getAvailableCategoryOptions',
                'required' => true
            ],
            'description' => [
                'label' => 'Описание',
                'type' => 'textarea',
                'class_names' => 'js-editor',
            ],
            'brief_description' => [
                'label' => 'Краткое описание',
                'type' => 'textarea',
                'class_names' => 'js-editor',
            ],
            'Price' => [
                'label' => 'Цена (руб)'
            ],
            'product_code' => [
                'label' => 'Код продукта',
            ],
            'picture' => [
                'label' => 'Изображение',
                'type' => 'image',
                'dir_path' => '/products_pictures/'
            ],
            'big_picture' => [
                'label' => 'Большое изображение',
                'type' => 'image',
                'dir_path' => '/products_pictures/'
            ],
            'meta_title' => [
                'type' => 'textarea',
            ],
            'meta_keywords' => [
                'type' => 'textarea',
            ],
            'meta_desc' => [
                'type' => 'textarea',
                'label' => 'Meta Description'
            ],
            'in_stock' => [
                'label' => 'В наличии',
                'type' => 'boolean'
            ],
            'enabled' => [
                'label' => 'Включен',
                'type' => 'boolean'
            ]
        ];

        return $fields;
    }

    public function action_edit()
    {
        $id = $this->request->param('id');
        $options = $this->getAllProductOptionsWithValuesArray();
        $allCategories = self::getCategoryOptions($this->pixie);

        if ($this->request->method == 'POST') {
            $product = null;
            if ($id) {
                /** @var \App\Model\Product $product */
                $product = $this->pixie->orm->get($this->model->model_name, $id);
            }

            if (!$product || !$product->loaded()) {
                throw new NotFoundException();
            }

            $data = $this->request->post();
            $this->processRequestFilesForItem($product, $data);
            $product->values($product->filterValues($data));
            $product->save();

            $requestProductCats = $data['additional_categories'] ?: [];
            $productCats = array_intersect_key($allCategories, array_flip($requestProductCats));

            /** @var Category $cat */
            foreach ($this->pixie->orm->get('category')->find_all() as $cat) {
                if (array_key_exists($cat->id(), $productCats)) {
                    $product->add('categories', $cat);
                } else {
                    $product->remove('categories', $cat);
                }
            }

            if ($product->loaded()) {
                $this->redirect('/admin/' . strtolower($product->model_name) . '/edit/'.$product->id());
                return;
            }

        } else {

            if (!$id) {
                throw new NotFoundException();
            }

            $product = $this->pixie->orm->get($this->model->model_name, $id);
            if (!$product->loaded()) {
                throw new NotFoundException();
            }
        }

        $editFields = $this->prepareEditFields();
        $this->view->pageTitle = $this->modelName.' &laquo;'.htmlspecialchars(trim($product->name)).'&raquo;';
        $this->view->pageHeader = $this->view->pageTitle;
        $this->view->modelName = $this->model->model_name;
        $this->view->item = $product;
        $this->view->editFields = $editFields;
        $this->view->formatter = new FieldFormatter($product, $editFields);
        $this->view->formatter->setPixie($this->pixie);

        $this->view->options = $options;
        $this->view->subview = 'product/edit';
        $this->view->fieldFormatter = $this->getProductOptionsFormatter();
        $this->view->categories = $allCategories;
        $this->view->productCats = $this->getProductCategoriesOptions($product);
    }

    public function action_new()
    {
        /** @var \App\Model\Product $product */
        $product = $this->pixie->orm->get($this->model->model_name);
        $allCategories = self::getCategoryOptions($this->pixie);

        if ($this->request->method == 'POST') {
            $data = $this->request->post();
            $product->values($product->filterValues($data));
            $product->save();

            if ($product->loaded()) {
                $this->processRequestFilesForItem($product, $data);
                $product->save();

                $requestProductCats = $data['additional_categories'] ?: [];
                $productCats = array_intersect_key($allCategories, array_flip($requestProductCats));

                /** @var Category $cat */
                foreach ($this->pixie->orm->get('category')->find_all() as $cat) {
                    if (array_key_exists($cat->id(), $productCats)) {
                        $product->add('categories', $cat);
                    }
                }

                $this->redirect('/admin/' . strtolower($product->model_name) . '/edit/'.$product->id());
                return;
            }
        }

        $editFields = $this->prepareEditFields();
        $this->view->pageTitle = 'Add new ' . $this->modelNameSingle;
        $this->view->pageHeader = $this->view->pageTitle;
        $this->view->modelName = $this->model->model_name;
        $this->view->item = $product;
        $this->view->editFields = $editFields;
        $this->view->formatter = new FieldFormatter($product, $editFields);
        $this->view->formatter->setPixie($this->pixie);
        $this->view->categories = $allCategories;
        $this->view->subview = 'product/edit';
        $this->view->productCats = [];
    }

    /**
     * @return array
     */
    private function getAllProductOptionsWithValuesArray()
    {
        $result = [];
        /** @var Option[] $options */
        $options = $this->pixie->orm->get('option')->order_by('sort_order', 'asc')->find_all()->as_array();
        foreach ($options as $option) {
            $res = ['name' => $option->name, 'variants' => []];

            /** @var OptionValue $variant */
            foreach ($option->variants->order_by('sort_order', 'asc')->find_all()->as_array() as $variant) {
                $res['variants'][$variant->id()] = $variant->name;
            }
            $result[$option->id()] = $res;
        }

        return $result;
    }

    /**
     * @param \App\Model\Product $product
     * @return array
     */
    protected function getProductOptionsWithValuesArray(\App\Model\Product $product)
    {
        $result = [];
        $productOptions = $product->productOptions
            ->with('optionVariant.parentOption')
            ->order_by('optionVariant_parentOption.name', 'asc')
            ->order_by('optionVariant.name', 'asc')
            ->find_all()->as_array();

        /** @var ProductOptionValue[] $productOptions */
        foreach ($productOptions as $option) {
            $variant = $option->optionVariant;
            $parentOpt = $variant->parentOption;
            if (!$result[$parentOpt->id()]) {
                $result[$parentOpt->id()] = [
                    'option' => $parentOpt,
                    'variants' => [],
                    'productOptions' => []
                ];
            }
            $result[$parentOpt->id()]['productOptions'][$option->id()] = $option;
            $result[$parentOpt->id()]['variants'][$option->id()] = $option->optionVariant;
        }

        return $result;
    }

    /**
     * @return FieldFormatter
     */
    protected function getProductOptionsFormatter()
    {
        $opts = $this->getOptionsArray();
        /** @var Option $option */
        $option = $this->pixie->orm->get('Option', key($opts));
        $optVals = $option->getValuesForOption();
        return new FieldFormatter($this->pixie->orm->get('OptionValue'), [
            'optionID' => [
                'type' => 'select',
                'label' => 'Option',
                'option_list' => $opts,
                'value' => key($opts),
                'required' => true
            ],
            'variantID' => [
                'type' => 'select',
                'label' => 'Variant',
                'option_list' => $optVals,
                'value' => key($optVals),
                'required' => true
            ]
        ]);
    }

    public function getOptionsArray()
    {
        $result = [];
        $options = $this->pixie->orm->get('Option')->order_by('name', 'asc')->find_all()->as_array();
        /** @var Option[] $options */
        foreach ($options as $opt) {
            $result[$opt->id()] = $opt->name;
        }

        return $result;
    }

    public function fieldFormatter($value, $item = null, array $format = [])
    {
        if ($format['original_field_name'] == 'category.name' && $value == '0_ROOT') {
            $value = '';
        }

        return parent::fieldFormatter($value, $item, $format);
    }

    public static function getCategoryOptions(Pixie $pixie)
    {
        $results = [];
        /** @var Category $categoryModel */
        $categoryModel = $pixie->orm->get('category');
        /** @var Category[] $cats */
        $cats = $categoryModel->order_by('name', 'asc')->where('parent', '>', '0')->find_All();
        foreach ($cats as $cat) {
            $results[$cat->id()] = $cat->name;
        }
        return $results;
    }

    public function getProductCategoriesOptions(\App\Model\Product $product)
    {
        $result = [];
        /** @var Category[] $categories */
        $categories = $product->categories->find_all()->as_array();
        foreach ($categories as $category) {
            $result[$category->id()] = $category->name;
        }
        return $result;
    }
}