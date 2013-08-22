<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pladmin
 * Date: 8/20/13
 * Time: 12:38 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class Property
 * @package Tm\Reader\Container
 */
class Property {
    protected $_propertyName = '';
    protected $_propertyValues = array();

    /**
     * @param string $propertyName
     */
    public function setPropertyName($propertyName)
    {
        $this->_propertyName = $propertyName;
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->_propertyName;
    }

    /**
     * @param array $propertyValues
     */
    public function setPropertyValue($propertyValues)
    {
        $this->_propertyValues[] = $propertyValues;
    }

    /**
     * @return array
     */
    public function getPropertyValues()
    {
        return $this->_propertyValues;
    }
}