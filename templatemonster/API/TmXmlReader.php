<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pladmin
 * Date: 8/20/13
 * Time: 11:56 AM
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class XmlReader
 * @package Tm
 */
class TmXmlReader {

    const ZIPPED_FILE_NAME = 't_info.xml';

    protected $_file;
    /** @var  XMLReader */
    protected  $_dom;

    /**
     * @param $filePath
     * @throws ReaderException
     */
    public function __construct($filePath)
    {
        if( !file_exists($filePath)) {
            throw new ReaderException("Source file not found");
        }
        $this->_file = $filePath;

        $this->_dom();
    }

    /**
     * Load source file as XML
     */
    protected function _dom()
    {
        $this->_dom = new XMLReader();
        if(pathinfo($this->_file, PATHINFO_EXTENSION) === 'zip') {
            $this->_dom->open("zip://{$this->_file}#" . self::ZIPPED_FILE_NAME);
        } else {
            $this->_dom->open($this->_file);
        }
    }

    /**
     * Iterate templates list
     */
    public function read($setter)
    {
        set_time_limit(0);
        $items = 0;
        while($this->_dom->read()) {
            if($this->_dom->nodeType == XMLREADER::ELEMENT && $this->_dom->name == 'template') {
                $template = $this->_dom->expand();
                $item = $this->_setItem($template);
                $setter->set($item);
                ++ $items;
            }
        }
        return $items;
    }

    /**
     * Retrieve Template Info from XMLNode
     * @param $template
     * @return Container
     */
    protected function _setItem($template)
    {

        $templateItem = new Container();

        $templateItem->setId            ($this->_value('id', $template, null));
        $templateItem->setTypeName      ($this->_value('type_name', $template, null));
        $templateItem->setPrice         ($this->_value('price', $template, null));
        $templateItem->setExcPrice      ($this->_value('exc_price', $template, null));
        $templateItem->setDownloads     ($this->_value('downloads', $template, null));
        $templateItem->setInsertedDate  ($this->_value('inserted_date', $template, null));
        $templateItem->setUpdateDate    ($this->_value('update_date', $template, null));
        $templateItem->setIsRealSize    ($this->_value('is_real_size', $template, null));
        $templateItem->setKeywords      ($this->_value('keywords', $template, null));
        $templateItem->setLivePreviewUrl($this->_value('live_preview_url', $template, null));
        $templateItem->setSoftwareRequired($this->_value('software_required', $template, null));
        $templateItem->setSources       ($this->_value('sources', $template, null));

        $this->_importCategories($templateItem, $template);
        $this->_importPages($templateItem, $template);
        $this->_importScreenShots($templateItem, $template);
        $this->_importProperties($templateItem, $template);

        return $templateItem;
    }

    protected function _importCategories(Container $templateItem, $template)
    {
        foreach($template->getElementsByTagName('category') as $category) {
            $templateItem->setCategory($this->_value('category_id', $template, 0),$this->_value('category_name', $template, ''));
        }
    }

    /**
     * Set templates Properties
     * @param Container $templateItem
     * @param $template
     */
    protected function _importProperties(Container $templateItem, $template)
    {
        foreach($template->getElementsByTagName('property') as $property) {

            $propertyItem = new Property();

            $propertyItem->setPropertyName($this->_value('propertyName', $property, ''));

            /** @var  DOMElement $value */
            foreach($property->getElementsByTagName('propertyValue') as $value) {
                $propertyItem->setPropertyValue( $value->nodeValue );
            }
            $templateItem->setProperty($propertyItem);
        }
    }

    /**
     * Import Template Screenshots
     * @param Container $templateItem
     * @param $template
     */
    protected function _importScreenShots(Container $templateItem, $template)
    {
        foreach($template->getElementsByTagName('screenshot') as $image) {
            $screen = new Screenshot();

            $screen->setUri($this->_value('uri', $image, ''));
            $templateItem->setScreenshot($screen);
        }
    }

    /**
     * Import Template Pages
     * @param Container $templateItem
     * @param $template
     */
    protected function _importPages(Container $templateItem, $template)
    {
        foreach($template->getElementsByTagName('page') as $page) {

            $pageItem = new Page();

            $pageItem->setName($this->_value('name', $page, null));

            foreach ($page->getElementsByTagName('scr') as $img) {

                $image = new Screenshot();

                $image->setDescription($this->_value('description', $img, ''));
                $image->setHeight($this->_value('height', $img, ''));
                $image->setWidth($this->_value('width', $img, ''));
                $image->setScrTypeId($this->_value('scr_type_id', $img, ''));
                $image->setUri($this->_value('uri', $img, ''));

                $pageItem->setScreenshots($image);
            }
            $templateItem->setPage($pageItem);
        }
    }

    /**
     * Get Field Value By Field Name
     * @param $fieldName
     * @param $xmlNode
     * @param null $default
     * @return null
     */
    protected function _value($fieldName, $xmlNode, $default = null)
    {
        if($xmlNode->getElementsByTagName($fieldName)->length > 0) {
            return $xmlNode->getElementsByTagName($fieldName)->item(0)->nodeValue;
        }
        return $default;
    }



}