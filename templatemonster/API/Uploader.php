<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pladmin
 * Date: 8/20/13
 * Time: 6:20 PM
 * To change this template use File | Settings | File Templates.
 */

class Uploader {

    /** api path used for catalog update */
    const BASE_API_PATH         = 'http://www.templatemonster.com/webapi/template_updates.php';
    /** api path for retrieve all catalog */
    const BASE_API_FILE_PATH    = 'http://www.templatemonster.com/webapi/xml/t_info.zip';
    const FILE_NAME             = 'catalog';
    const DATE_FROM             = 0;
    const DATE_TO               = 1;

    const FILE_FORMAT_ZIP       = '.zip';
    const FILE_FORMAT_XML       = '.xml';

    protected $_dataDir  = '';
    protected $_format    = '';
    /** @var  string User partner login */
    protected $_apiUser;
    /** @var  string User partner password */
    protected $_apiKey;
    protected $_dateFrom = '0000-00-00 00:00:00';

    protected $_dateTo   = '0000-00-00 00:00:00';

    public function __construct($_apiUser, $_apiKey)
    {
        $this->_apiUser = $_apiUser;
        $this->_apiKey  = $_apiKey;
        $this->_dateTo  = date('Y-m-d H:i:s');
    }

    /**
     * Run upload data process
     * Executive action
     * @param string $format
     */
    public function run($format=self::FILE_FORMAT_ZIP)
    {
        ini_set('max_execution_time', 10000);
        $this->_format = $format;
        $this->_connect();
    }

    /**
     * Set data directory
     * @param $dataDir
     * @throws UploaderException
     */
    public  function setDataDir($dataDir)
    {
        if(!file_exists($dataDir) && !is_dir($dataDir)) {
            throw new UploaderException('Upload directory not exists ' . $dataDir);
        } elseif ( !is_writeable($dataDir) ) {
            throw new UploaderException('Upload directory not writable ' . $dataDir);
        } else {
            $this->_dataDir = $dataDir;
        }
    }

    /**
     * Set date of last catalog update
     * @param $date
     * @param int $dateType
     * @throws Exception
     */
    public function setDate($date, $dateType = self::DATE_FROM)
    {
        if($date && DateTime::createFromFormat('Y-m-d H:i:s', $date) !== false ) {
            if($dateType === self::DATE_FROM) {
                $this->_dateFrom = $date;
            } else {
                $this->_dateTo = $date;
            }
        } else {
           throw new Exception("Invalid parameter DATE ");
        }
    }

    /**
     * Return array of api params
     * Array of get params
     * @return array
     */
    protected function getRequestParams()
    {
        return array(
            'login'             => $this->_apiUser,
            'webapipassword'    => $this->_apiKey,
            'from'              => $this->_dateFrom,
            'to'                => date('Y-m-d H:i:s'),
        );
    }

    /**
     * get request by format
     * @param $format
     * @return string
     */
    protected function _buildHTTPQuery($format)
    {
        if( $format === self::FILE_FORMAT_ZIP ) {
            return self::BASE_API_FILE_PATH;
        }
        $data = $this->getRequestParams();
        return self::BASE_API_PATH . '?' . http_build_query($data);
    }

    /**
     * Sync data to file
     * @throws Exception
     */
    protected function _connect()
    {
        set_time_limit(0);

        try {
            $fp = fopen($this->_getPath(), 'w+');
            $ch = curl_init(str_replace(" ","%20",$this->_buildHTTPQuery($this->_format)));

            curl_setopt_array($ch, array(
                CURLOPT_TIMEOUT         => 300,
                CURLOPT_FILE            => $fp,
                CURLOPT_FOLLOWLOCATION  => true,
            ));

            curl_exec($ch);

            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            if($contentType != 'application/zip' && $contentType != 'text/xml' )  {
                throw new UploaderException('Unauthorized usage or invalid content type');
            }
            /** close connection */
            curl_close($ch);
            /** close file */
            fclose($fp);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return file path of imported file
     * @return string
     * @throws UploaderException
     */
    public function getImportedFile()
    {
        if(file_exists($this->_getPath())) {
            return $this->_getPath();
        }
        throw new UploaderException("Uploaded file ({$this->_getPath()}) not exists. Try run Import again");
    }

    protected function _getPath()
    {
        return $this->_dataDir . DIRECTORY_SEPARATOR . self::FILE_NAME . $this->_format;
    }
}