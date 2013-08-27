<?php
/**
 * Class TmUploaderImages
 * @author studxxx
 */
class TmUploaderImages
{
    /**
     * Description
     * $description string
     */
    public $description;
    
    /**
     * Path to image
     * $type string - uri
     */
    public $uri;
    
    /**
     * Id product
     * $productId integer
     */
    public $productId;
    
    /**
     * Position image
     * $position integer
     */
    public $position;

    /**
     * System thumbnail image size - default
     * $_imageTypes array
     */
    protected $_imageTypes =array();

    /**
     * System const - path to products images
     * DIR string
     */
    const DIR = _PS_PROD_IMG_DIR_;

    /**
     * Default
     * @param $imageTypes array
     */
    public function __construct($imageTypes = array())
    {
        $this->_imageTypes = $imageTypes;
    }

    /**
     * Upload image into server
     */
    public function tmAdd()
    {
        $fileInfo = pathinfo($this->uri);

        if($fileInfo['extension'] == 'jpg' || $fileInfo['extension'] == 'png'){
            $tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            file_put_contents($tmpName, file_get_contents($this->uri));

            $image = new Image();
            
            $image->id_product = (int)$this->productId;
            if($this->position == 1){
                $image->cover = (int)$this->position;
            }
            $image->position = $this->position;
            $image->image_format = 'jpg';
            $image->add();

            ImageManager::resize($tmpName, $image->getPathForCreation().'.'.$image->image_format);

            $existingImage = self::DIR.$image->getExistingImgPath().'.jpg';

            foreach($this->_imageTypes as $imageType){
                ImageManager::resize(
                    $existingImage,
                    self::DIR.$image->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg',
                    (int)($imageType['width']),
                    (int)($imageType['height'])
                );
            }
        }

        return true;
    }

}