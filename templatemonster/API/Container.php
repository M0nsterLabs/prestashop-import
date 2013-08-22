<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pladmin
 * Date: 8/20/13
 * Time: 12:30 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class Container
 * @package Tm
 */
class Container {

    /**
     * PrestaShop Fields DB definition
     * @var array
     */
    protected $_properties  = array();

    /**
     * Template Pages
     * @var array
     */
    protected $_pages = array();

    protected $screenshots_list = array();

    protected $_id;
    protected $_price;
    protected $exc_price;

    protected $downloads;
    protected $inserted_date;

    protected $update_date;
    protected $width;

    protected $is_real_size;
    protected $type_name;
    protected $live_preview_url;
    protected $keywords;
    protected $categories = array();
    protected $sources;
    protected $software_required;
    protected $basic_colors;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->_price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * @param mixed $basic_colors
     */
    public function setBasicColors($basic_colors)
    {
        $this->basic_colors = $basic_colors;
    }

    /**
     * @return mixed
     */
    public function getBasicColors()
    {
        return $this->basic_colors;
    }

    /**
     * @param mixed $categories
     */
    public function setCategory($categoryId, $categoryName)
    {
        $this->categories[$categoryId] = $categoryName;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $downloads
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;
    }

    /**
     * @return mixed
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * @param mixed $exc_price
     */
    public function setExcPrice($exc_price)
    {
        $this->exc_price = $exc_price;
    }

    /**
     * @return mixed
     */
    public function getExcPrice()
    {
        return $this->exc_price;
    }

    /**
     * @param $inserted_date
     */
    public function setInsertedDate($inserted_date)
    {
        $this->inserted_date = $inserted_date;
    }

    /**
     * @return mixed
     */
    public function getInsertedDate()
    {
        return $this->inserted_date;
    }

    /**
     * @param $is_real_size
     */
    public function setIsRealSize($is_real_size)
    {
        $this->is_real_size = $is_real_size;
    }

    /**
     * @return mixed
     */
    public function getIsRealSize()
    {
        return $this->is_real_size;
    }

    /**
     * @param $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param $live_preview_url
     */
    public function setLivePreviewUrl($live_preview_url)
    {
        $this->live_preview_url = $live_preview_url;
    }

    /**
     * @return mixed
     */
    public function getLivePreviewUrl()
    {
        return $this->live_preview_url;
    }

    /**
     * @param $software_required
     */
    public function setSoftwareRequired($software_required)
    {
        $this->software_required = $software_required;
    }

    /**
     * @return mixed
     */
    public function getSoftwareRequired()
    {
        return $this->software_required;
    }

    /**
     * @param $sources
     */
    public function setSources($sources)
    {
        $this->sources = $sources;
    }

    /**
     * @return mixed
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * @param $type_name
     */
    public function setTypeName($type_name)
    {
        $this->type_name = $type_name;
    }

    /**
     * @return mixed
     */
    public function getTypeName()
    {
        return $this->type_name;
    }

    /**
     * @param $update_date
     */
    public function setUpdateDate($update_date)
    {
        $this->update_date = $update_date;
    }

    /**
     * @return mixed
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * @param $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    public function setProperty(Property $property )
    {
        $this->_properties[] = $property;
    }

    /**
     * @param Page $page
     */
    public function setPage(Page $page )
    {
        $this->_pages[] = $page;
    }

    /**
     * @param Screenshot $screenshot
     */
    public function setScreenshot(Screenshot $screenshot)
    {
        $this->_screenshots[] = $screenshot;
    }

    /**
     * @return array
     */
    public function getScreenshots()
    {
        return $this->_screenshots;
    }

    /**
     * @return array
     */
    public function getPages()
    {
        return $this->_pages;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }


}