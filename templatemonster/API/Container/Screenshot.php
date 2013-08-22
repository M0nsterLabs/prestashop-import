<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pladmin
 * Date: 8/20/13
 * Time: 12:53 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class Screenshot
 * @package Tm\Reader\Container
 */
class Screenshot {

    protected $description;
    protected $uri;
    protected $scr_type_id;
    protected $width;
    protected $height;

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $scr_type_id
     */
    public function setScrTypeId($scr_type_id)
    {
        $this->scr_type_id = $scr_type_id;
    }

    /**
     * @return mixed
     */
    public function getScrTypeId()
    {
        return $this->scr_type_id;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $width
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
}