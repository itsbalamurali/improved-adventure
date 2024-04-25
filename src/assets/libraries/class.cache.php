<?php


class CacheMemcache
{
    public $iTtl = 0; // Time To Live
    public $bEnabled = false; // Memcache enabled?
    public $oCache;

    public $T_KEY_PREFIX = '/';

    public function __construct()
    {

        global $tconfig;
        $this->T_KEY_PREFIX = $tconfig['tsite_folder'];
        if ('/' === $tconfig['tsite_folder'] || '\\' === $tconfig['tsite_folder']) {
            $this->T_KEY_PREFIX = $_SERVER['SERVER_NAME'];
        }
        $this->T_KEY_PREFIX = md5($this->T_KEY_PREFIX).'_';
        if (class_exists('Memcache')) {
            $this->oCache = new Memcache();
            $this->bEnabled = true;
            if (!$this->oCache->connect('localhost', 11_211)) { // Instead 'localhost' here can be IP
                $this->oCache = null;
                $this->bEnabled = false;
            }
        }
    }

    //    public function CacheMemcache()
    //    {
    //        if (empty(ENABLE_CACHE_QUERIES_DATA) || 'YES' !== strtoupper(ENABLE_CACHE_QUERIES_DATA)) {
    //            return false;
    //        }
    //        if (class_exists('Memcache')) {
    //            $this->oCache = new Memcache();
    //            $this->bEnabled = true;
    //            if (!$this->oCache->connect('localhost', 11_211)) { // Instead 'localhost' here can be IP
    //                $this->oCache = null;
    //                $this->bEnabled = false;
    //            }
    //        }
    //    }

    // get data from cache server
    public function getData($sKey)
    {
        if (empty(ENABLE_CACHE_QUERIES_DATA) || 'YES' !== strtoupper(ENABLE_CACHE_QUERIES_DATA)) {
            return false;
        }
        /*if(!isset($_REQUEST['enablequery']) && strtoupper(ENABLE_CACHE_QUERIES_DATA) == "NO"){
            $vData = array();
            $this->delData($sKey);
            return $vData;
        }*/

        if (empty($this->oCache)) {
            return null;
        }
        $vData = $this->oCache->get($this->T_KEY_PREFIX.$sKey);

        // echo "<pre>";print_r($vData);die;
        return false === $vData ? null : $vData;
    }

    // save data to cache server
    public function setData($sKey, $vData)
    {
        if (empty(ENABLE_CACHE_QUERIES_DATA) || 'YES' !== strtoupper(ENABLE_CACHE_QUERIES_DATA)) {
            return false;
        }

        // Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib).
        if (empty($this->oCache)) {
            return false;
        }
        return $this->oCache->set($this->T_KEY_PREFIX.$sKey, $vData, 0, $this->iTtl);
    }

    // delete data from cache server
    public function delData($sKey)
    {
        if (empty(ENABLE_CACHE_QUERIES_DATA) || 'YES' !== strtoupper(ENABLE_CACHE_QUERIES_DATA)) {
            return false;
        }

        return $this->oCache->delete($this->T_KEY_PREFIX.$sKey);
    }

    public function flushData()
    {
        global $IS_INHOUSE_DOMAINS;
        if (empty(ENABLE_CACHE_QUERIES_DATA) || 'YES' !== strtoupper(ENABLE_CACHE_QUERIES_DATA)) {
            return false;
        }

        return $this->oCache->flush();
    }
}
