<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pladmin
 * Date: 8/20/13
 * Time: 12:51 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class Page
 * @package Tm\Reader\Container
 */
class Page {

    protected $_name;
    protected $_screenshots = array();

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param Screenshot $screenshots
     */
    public function setScreenshots(Screenshot $screenshots)
    {
        $this->_screenshots[] = $screenshots;
    }

    /**
     * @return array
     */
    public function getScreenshots()
    {
        return $this->_screenshots;
    }

}