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
     * System thumbnail size image - default
     * $_imageSize array
     */
    protected $_imageSize =array();

    /**
     * Default
     * @param $imageSize array
     */
    public function __construct($imageSize = array())
    {
        if(!empty($imageSize) && is_array($imageSize)){
            $this->_imageSize = $imageSize;
        }
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
            
            $filePath = $image->getPathForCreation().'.'.$image->image_format;

            ImageManager::resize($tmpName, $filePath);

            $file = pathinfo($filePath);
            
            foreach($this->_imageSize as $imgSize){
                $this->tmResizeImage(array(
                    "dir"         => $file['dirname'].DIRECTORY_SEPARATOR,
                    "input_file"  => $filePath,
                    "wb"          => $imgSize[1],
                    "hb"          => $imgSize[2],
                    "prefix"      => "",
                    "suffix"      => '-'.$imgSize[0],
                    "newfilename" => $file['filename'],
                ));
            }
        }

        return true;
    }
    
    /**
     * Resize image
     * @param (string) $dir - path into upload image
     * @param (string) $input_file - file;
     * @param (int) $wb - weight box. int type (300);
     * @param (int) $hb - height box. int type (600);
     * @param (string) $prefix - prefix new image. default - "small_";
     * @param (string) $suffix -  suffix new image. default - null;
     * @param (string) $newfilename - new file name, default - result time()
     */
    public function tmResizeImage($array)
    {
        $result["result"] = TRUE;

        $dir = $array["dir"];
        @mkdir($dir, 0755);
        if ($array["input_file"] != "" && file_exists($array["input_file"]) && !is_dir($array["input_file"])) {
            @$bigImageProperty = getimagesize($array["input_file"]);
            if ($bigImageProperty) {
                $w = $bigImageProperty[0];
                $h = $bigImageProperty[1];
                
                if (($array["wb"] == "" || $array["wb"] <= 0) && ($array["hb"] == "" || $array["hb"] <= 0)) {
                    $array["wb"] = $bigImageProperty[0];
                    $array["hb"] = $bigImageProperty[1];
                }
                
                if ($array["wb"] == "" || $array["wb"] == 0) {
                    $array["wb"] = ceil($w * $array["hb"] / $h);
                } elseif ($array["hb"] == "" || $array["hb"] == 0) {
                    $array["hb"] = ceil($array["wb"] * $h / $w);
                }
                    
                $kw = $w / $array["wb"];
                $kh = $h / $array["hb"];
                
                if ($kw < $kh) {
                    $x = $w / $kh;
                    $y = $array["hb"];
                } else {
                    $x = $array["wb"];
                    $y = $h / $kw;
                }
                if(!$x) {
                    $x = $array["wb"];
                }
                if (!$y) {
                    $y = $array["hb"];
                }

                $dw = 0; 
                $dh = 0;

                if (($array["wb"] > $w) && ($array["ab"] > $h)) {
                    $x = $w;
                    $y = $h;
                }
                
                $tdb_dest = ImageCreateTrueColor($x, $y); 
                
                imagealphablending($tdb_dest, FALSE);
                imagesavealpha($tdb_dest, TRUE);
                $transColour = imagecolorallocatealpha($tdb_dest, 255, 255, 255, 127);
                imagefilledrectangle($tdb_dest, 0, 0, $x, $y, $transColour);

                imagecolorallocate($tdb_dest, 255, 255, 255);
                
                if ($bigImageProperty[2] == 1) {
                    $im = imagecreatefromgif($array["input_file"]);
                } elseif ($bigImageProperty[2] == 2) {
                    $im = imagecreatefromjpeg($array["input_file"]);
                } elseif ($bigImageProperty[2] == 3) {
                    imagealphablending($tdb_dest, TRUE);
                    imagesavealpha($tdb_dest, TRUE);
                    $im = imagecreatefrompng($array["input_file"]);
                }

                if (@!imagecopyresampled($tdb_dest, $im, 0, 0, 0, 0, $x, $y, $w - $dw, $h - $dh)) {
                    $result["result"] = FALSE;
                }
                if ($array["newfilename"] == "") {
                    $array["newfilename"] = time();
                }
                
                if($bigImageProperty[2] == 3 || $bigImageProperty[2] == 1) {
                    $resultFileName = $array["prefix"].$array["newfilename"].$array["suffix"].".png";
                    if (@!Imagepng($tdb_dest, $dir.$resultFileName)) {
                        $result["result"] = FALSE;
                    }
                } else {
                    $resultFileName = $array["prefix"].$array["newfilename"].$array["suffix"].".jpg";
                    $quality = $this->tmQuality ? $this->tmQuality : 100;
                    
                    if (@!Imagejpeg($tdb_dest, $dir.$resultFileName, $quality)) {
                        $result["result"] = FALSE;
                    }
                }
                @ImageDestroy($im);
                @ImageDestroy($tdb_dest);
                    
                $result["image"] = $resultFileName;
            }
        }

        return $result;
    }
}