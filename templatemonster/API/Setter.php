<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pladmin
 * Date: 8/21/13
 * Time: 6:15 PM
 * To change this template use File | Settings | File Templates.
 */

include_once(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.inc.php');

class Setter {
    /** @var Db  */
    protected $_db;
    protected $_lng = 1;
    protected $_imageSizes = array();

    public function __construct()
    {
        $this->_db = Db::getInstance();
        $this->_lng = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->_cacheImagesSizes();
    }

    /**
     * get images size
     * @throws Exception
     */
    protected function _cacheImagesSizes()
    {
        $result = $this->_db->executeS("
            SELECT name, width, height
            FROM " . $this->tableName('image_type') . "
            WHERE products=1
        ");

        if($result === false) {
            throw new Exception('Images types not found');
        }

        foreach($result as $row) {
            $this->_imageSizes[] = array(
                $row['name'], $row['width'], $row['height']
            );
        }
    }

    /**
     * @param Container $item
     */
    public function set(Container $item)
    {
        if( ! ($rootCategoryId = $this->getCategory(html_entity_decode($item->getTypeName(), ENT_QUOTES)) )) {
            $rootCategoryId = (int)$this->_importCategory($item->getTypeName(), 2, 1);


        }
        foreach($item->getCategories() as $categoryName) {
            if( !($categoryId = $this->getCategory(html_entity_decode($categoryName, ENT_QUOTES))) ) {
                $categoryIds[] = (int) $this->_importCategory($categoryName, $rootCategoryId, 2);
            } else {
                $categoryIds[] = (int) $categoryId;
            }
        }
        /** Product item */
        $this->_importProduct($item, $categoryIds);
    }

    /**
     * Import Product item to Ps
     * @param Container $item
     * @param $categoryIds
     * @return mixed
     */
    protected function _importProduct(Container $item, $categoryIds)
    {
        $productId = $this->getProduct($item->getId());

        $product = new Product((int)$productId);
        $product->active                = 1;
        $product->reference             = 'TM_'.(int) $item->getId();
        $product->price                 = $item->getPrice();
        $product->date_add              = $item->getInsertedDate();
        $product->date_upd              = $item->getUpdateDate();
        $product->description           = $this->_description($item);
        $product->meta_keywords         = $this->_keywords($item->getKeywords());
        $product->id_category_default   = $categoryIds[0];//(int) (isset($categoryIds[0]) ? $categoryIds[0] : 0);
        $product->name[$this->_lng]               = 'Template TM '.(int) $item->getId();
        $product->link_rewrite[$this->_lng]       = Tools::link_rewrite($product->name[$this->_lng]);
        $product->id_manufacturer                 = $this->_importManufacturer($item);

        $product->save();

        $product->updateCategories($categoryIds);

        if (! Validate::isLoadedObject($product) ) {
            return false;
        }

        /** Remove Old Features */
        $product->deleteFeatures();

        $this->_importProductFeature($item, $product->id);
        $this->_importImages($item, $product->id);

        return $product->id;
    }

    /**
     * Validate keywords
     * @param $keywords
     * @return mixed
     */
    protected function _keywords($keywords)
    {
        return str_replace('=', ' ',
            substr(html_entity_decode($keywords, ENT_QUOTES), 0, 255)
        );
    }

    /**
     * Import Template Manufacturer
     * @param Container $item
     * @return int|mixed
     */
    protected function _importManufacturer(Container $item)
    {
        if( ($manufacturerId = $this->getManufacturer($item)) ) {
            return $manufacturerId;
        }

        $manufacturer = new Manufacturer();
        $manufacturer->name = $item->getAuthor();
        $manufacturer->active = 1;
        $manufacturer->add();

        return (int)$manufacturer->id;
    }

    /**
     * @param Container $item
     * @param $productId
     */
    protected function _importProductFeature(Container $item, $productId)
    {
        /** @var Property $property */
        foreach($item->getProperties() as $property) {

            $featureId = $this->_getFeatureId($property->getPropertyName());

            if(! $featureId) {
                $featureId = $this->_importFeature($property, $productId);
            }

            $value = implode(', ', $property->getPropertyValues());
            $valueId = $this->_getFeatureValueId($value);

            if(! $valueId ) {
                $valueId = $this->_importValue($value, $featureId);
            }
            $this->_db->insert('feature_product', array(
                    'id_feature'        => (int)$featureId,
                    'id_product'        => $productId,
                    'id_feature_value'  => (int)$valueId
                )
            );
        }
    }

    /**
     * @param $value
     * @param $featureId
     * @return mixed
     */
    protected function _importValue($value, $featureId)
    {

        $this->_db->insert('feature_value', array(
                'id_feature'        => (int)$featureId,
                'custom'            => 1
            )
        );
        $valueId = $this->_db->Insert_ID();

        $this->_db->insert('feature_value_lang', array(
                'id_feature_value'  => (int) $valueId,
                'id_lang' => $this->_lng,
                'value'   => $value
            )
        );
        return $valueId;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function _getFeatureId($name)
    {
        return $this->_db->getValue("
            SELECT id_feature
            FROM " . $this->tableName('feature_lang') . "
            WHERE name='" . pSQL($name) . "'
        ");
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function _getFeatureValueId($name)
    {
        return $this->_db->getValue("
            SELECT id_feature_value
            FROM " . $this->tableName('feature_value_lang') . "
            WHERE value='" . pSQL($name) . "'
        ");
    }

    /**
     * @param Property $property
     * @return mixed
     */
    protected function _importFeature(Property $property)
    {
        $featureId = $this->_featurePosition();

        $this->_db->insert('feature_lang', array(
                'id_feature' => (int) $featureId,
                'id_lang' => $this->_lng,
                'name'    => $property->getPropertyName()
            )
        );

        $this->_featureShop($featureId);

        return $featureId;
    }

    /**
     * @return mixed
     */
    protected function _featurePosition()
    {
        $this->_db->insert('feature', array(
                'position'    => 0
            )
        );
        return $this->_db->Insert_ID();
    }

    /**
     * @param $featureId
     */
    protected function _featureShop($featureId)
    {
        $this->_db->insert('feature_shop', array(
                'id_feature' => (int)$featureId,
                'id_shop'    => 1
            )
        );
    }

    /**
     * Add category to ps
     * @param $categoryName
     * @param $id_parent
     * @param $level
     */
    protected function _importCategory($categoryName, $id_parent, $level)
    {
        $category = new Category();

        $category->name[$this->_lng]           = html_entity_decode($categoryName, ENT_QUOTES);
        $category->link_rewrite[$this->_lng]   = Tools::link_rewrite($categoryName);
        $category->id_parent            = (int) $id_parent;
        $category->level_depth          = $level;
        $category->add();

        return $category->id;
    }

    protected function getCategory($categoryName)
    {
        return $this->_db->getValue("
			SELECT id_category
			FROM " . $this->tableName('category_lang') . " cl
			WHERE id_lang = 1 AND name = '" . pSQL($categoryName). "'
        ");
    }

    protected function getProduct($productId)
    {
        return $this->_db->getValue("
			SELECT id_product
			FROM " . $this->tableName('product') . " p
			WHERE p.reference = 'TM_" . $productId . "'
        ");
    }

    protected function getManufacturer(Container $item)
    {
        return $this->_db->getValue("
            SELECT id_manufacturer
            FROM " . $this->tableName('manufacturer') . "
            WHERE name ='" . pSQL($item->getAuthor()) . "'
        ");
    }

    /**
     * Get full category Name
     * @param $name
     * @return string
     */
    protected function tableName($name)
    {
        return _DB_PREFIX_ . $name;
    }

    /**
     * Build descr
     * @param Container $item
     * @return string
     */
    protected function _description(Container $item)
    {
        $description = '';

        /** @var Page $page */
        $description .= '<p>Sources: ' . $item->getSources() . '</p>';
        $description .= '<p>Software required: ' . $item->getSoftwareRequired() . '</p>';

        if($item->getLivePreviewUrl()) {
            $description .= '<p>Live preview URL: : <a href="' . $item->getLivePreviewUrl() . '">'. $item->getLivePreviewUrl() . '</a></p>';
        }

        foreach($item->getPages() as $page) {
            $description .= '<h3>' .  $page->getName() . '</h3>';
            /** @var Screenshot $screen */
            foreach($page->getScreenshots() as $screen) {
                $description .= '<p>' .  $screen->getDescription() . '</p>';
                $description .= "<img class='template-descr-img' src='{$screen->getUri()}' alt='{$page->getName()}'/>";
            }
        }
        return $description;
    }

    /**
     * Import images
     * @param Container $item
     * @param $productId
     */
    protected function _importImages(Container $item, $productId)
    {
        if($productId){
            /* Add Pictures */
            $n = 1;
            foreach ($item->getScreenshots() as $screenshot){
                if(substr_count($screenshot->getUri(), 'original-1200') ||
                    substr_count($screenshot->getUri(), 'original_1200')){

                    $uploadImage = new TmUploaderImages($this->_imageSizes);

                    $uploadImage->uri = $screenshot->getUri();
                    $uploadImage->productId = (int)$productId;
                    $uploadImage->position = $n++;
                    $uploadImage->tmAdd();

                    unset($uploadImage);
                }
            }

            foreach($item->getPages() as $pages){
                foreach($pages->getScreenshots() as $screenshot){

                    $uploadImage = new TmUploaderImages($this->_imageSizes);

                    $uploadImage->uri = $screenshot->getUri();
                    $uploadImage->productId = (int)$productId;
                    $uploadImage->position = $n++;
                    $uploadImage->description = $screenshot->getDescription();
                    $uploadImage->tmAdd();

                    unset($uploadImage);
                }
            }
        }
    }
}
