<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 27.08.2014
 * Time: 15:40
 */


namespace App\DataImport;


use App\Model\Category;
use App\Model\ProductImage;
use App\Pixie;

/**
 * Imports categories and products from directory.
 * @package App\DataImport
 */
class EvolveScaterProductImporter
{
    protected $pixie;

    /**
     * @var null|Category
     */
    protected $rootCategory = null;

    protected $result = [];

    protected $lastCategoryLevel;

    public function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    /**
     * Main import function. Clears DB and then imports categories and products from path.
     * @param array $data Data fetched from http://evolveskateboardsusa.com
     * @param int $categoryLevels Number of category levels.
     * @return array Debug and statistics information.
     */
    public function import(array $data, $categoryLevels = 1)
    {
        $this->lastCategoryLevel = $categoryLevels - 1;

        $this->result = [];
        $this->clearDatabase();
        $this->importProducts($this->getRoot(), $data, 'all');
        $this->importCategory($this->getRoot(), $data);

        return $this->result;
    }

    /**
     * Imports category in certain level.
     * @param Category $parent
     * @param $data
     * @param int $depth
     */
    public function importCategory(Category $parent, array $data, $depth = 0)
    {
        foreach ($data['categories'] as $alias => $catInfo) {
            if ($alias == 'all') {
                continue;
            }

            $category = $this->createCategory([
                'name' => $catInfo['title'],
                'hurl' => $alias,
                'enabled' => 1,
                'parent' => $parent->id()
            ], $parent);
            $this->result['category_num']++;

            if ($depth >= $this->lastCategoryLevel) {
                $this->importProducts($category, $data, $alias);
            }
        }
    }

    /**
     * Imports all products from a directory.
     * @param Category $category
     * @param $data
     * @param $categoryAlias
     */
    public function importProducts(Category $category, $data, $categoryAlias)
    {
        foreach ($data['products'] as $alias => $productInfo) {
            if ($categoryAlias != $productInfo['category']) {
                continue;
            }
            $this->importProduct($alias, $category, $productInfo);
        }
    }

    /**
     * Imports single product from its directory.
     * Directory must contain a text file.
     * @param $name
     * @param Category $category
     * @param $data
     */
    public function importProduct($name, Category $category, $data)
    {
        $product = $this->pixie->orm->get('Product');
        $product->values([
            'name' => $data['name'] ?: $name,
            'categoryID' => $category->categoryID,
            'hurl' => $name,
            'description' => $data['description'],
            'big_picture' => $data['big_images'][0]['filename'],
            'picture' => $data['small_images'][0]['filename'],
            'show_in_root' => (int)$data['show_in_root'],
            'Price' => sprintf(preg_replace('/[,\s]/', '', $data['price']))
        ]);

        $product->save();

        foreach ($data['small_images'] as $key => $image) {
            /** @var ProductImage $productImage */
            $productImage = $this->pixie->orm->get('ProductImage');
            $productImage->file_name = $image['filename'];
            $productImage->file_name_big = $data['big_images'][$key]['filename'];
            $productImage->product_id = $product->id();
            $productImage->title = $image[1];
            $productImage->save();
        }

        $this->result['product_num']++;
    }

    public static function create(Pixie $pixie)
    {
        return new self($pixie);
    }

    /**
     * Get or create root category.
     * @return null|Category
     */
    protected function getRoot()
    {
        if (!$this->rootCategory) {
            $this->rootCategory = $this->createCategory([
                'name' => '0_ROOT',
                'enabled' => 1,
                'sort_order' => 100
            ]);
            $this->rootCategory->refresh();
            $this->rootCategory->parent = 0;
            $this->rootCategory->save();
        }

        return $this->rootCategory;
    }

    protected function createCategory(array $values = [], Category $parent = null)
    {
        /** @var Category $category */
        $category = $this->pixie->orm->get('Category');
        $category->values($values);
        $category->nested->prepare_append($parent);
        $category->save();
        return $category;
    }

    protected function clearDatabase()
    {
        $this->pixie->db->query('delete')->table('tbl_order_items')->execute();
        $this->pixie->db->query('delete')->table('tbl_order_address')->execute();
        $this->pixie->db->query('delete')->table('tbl_orders')->execute();
        $this->pixie->db->query('delete')->table('tbl_products')->execute();
        $this->pixie->db->query('delete')->table('tbl_categories')->execute();
        $this->pixie->db->get()->execute("alter table tbl_order_items AUTO_INCREMENT = 1;");
        $this->pixie->db->get()->execute("alter table tbl_order_address AUTO_INCREMENT = 1;");
        $this->pixie->db->get()->execute("alter table tbl_orders AUTO_INCREMENT = 1;");
        $this->pixie->db->get()->execute("alter table tbl_products AUTO_INCREMENT = 1;");
        $this->pixie->db->get()->execute("alter table tbl_categories AUTO_INCREMENT = 1;");
    }
}