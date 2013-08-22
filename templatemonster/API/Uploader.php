<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pladmin
 * Date: 8/20/13
 * Time: 6:20 PM
 * To change this template use File | Settings | File Templates.
 */

class Uploader {

    const BASE_API_PATH         = 'http://www.templatemonster.com/webapi/template_updates.php';
    const BASE_API_FILE_PATH    = 'http://www.templatemonster.com/webapi/xml/t_info.zip';
    const FILE_NAME     = 'catalog';
    const DATE_FROM     = 0;
    const DATE_TO       = 1;

    protected $_dataDir  = '';
    protected $_apiUser;
    protected $_apiKey;
    protected $_dateFrom = '0000-00-00 00:00:00';

    protected $_dateTo   = '0000-00-00 00:00:00';

    public function __construct($_apiUser, $_apiKey)
    {
        $this->_apiUser = $_apiUser;
        $this->_apiKey  = $_apiKey;
        $this->_dateTo  = date('Y-m-d H:i:s');
    }

    public function run($format=self::FILE_FORMAT_ZIP)
    {
        $this->_connect($format);
    }

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

    protected function _buildHTTPQuery()
    {
        $data = array(
            'login'             => $this->_apiUser,
            'webapipassword'    => $this->_apiKey,
            'from'              => $this->_dateFrom,
            'to'                => date('Y-m-d H:i:s'),
        );
        return self::BASE_API_PATH . http_build_query($data);
    }


    protected function _connect()
    {
        set_time_limit(0);

        $fp = fopen ($this->_dataDir . DIRECTORY_SEPARATOR . self::FILE_NAME, 'w+');
        $ch = curl_init(str_replace(" ","%20",$this->_buildHTTPQuery()));
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);

        fclose($fp);
    }
}