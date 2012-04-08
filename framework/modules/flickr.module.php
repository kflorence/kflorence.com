<?php

/**
 * Flickr API wrapper
 *
 * Hits up flicker for requests, returns them as objects,
 * and caches them on file for faster performance
 *
 * @author   Corey Worrell
 * @url      http://coreyworrell.com
 * @version  1.0
 */

class Flickr extends Module
{
    protected static $rest_url = 'http://api.flickr.com/services/rest/';

    protected $params = array();

    /**
     * Constructor function
     * Returns new class object, used to chain methods
     *
     * @param   string   Your Flickr API Key - http://www.flickr.com/services/api/keys/apply/
     * @param   string   Directory to store cache files
     * @param   integer  Number of seconds to cache requests
     * @return  Flickr
     */
    public function factory($api_key, $cache_dir = NULL, $cache_expire = NULL)
    {
        return new Flickr($api_key, $cache_dir, $cache_expire);
    }

    /**
     * Constructor function
     * Sets initial params, cleans out old cache files
     *
     * @param   string   Your Flickr API Key - http://www.flickr.com/services/api/keys/apply/
     * @param   string   Directory to store cache files
     * @param   integer  Number of seconds to cache requests
     * @return  null
     */
    /*
    public function __construct($api_key, $cache_dir = NULL, $cache_expire = NULL)
    {
        $this->params['api_key'] = $api_key;
        $this->params['format'] = 'php_serial';
        $this->cache_dir = empty($cache_dir) ? $this->cache_dir : $cache_dir;
        $this->cache_expire = empty($cache_expire) ? $this->cache_expire : $cache_expire;
        $this->cache();
    }
    */

    public function __construct()
    {
        parent::onLoad();

        $this->params['api_key'] = $this->api_key;
        $this->params['format'] = 'php_serial';
        $this->cache();
    }

    /**
     * Make a request to the Flickr API
     *
     * @param   string   Flickr API method name (w/out 'flickr' prefix), ie.: 'people.getPublicPhotos'
     * @param   array    Parameters for method (key => value)
     * @return  object   Object with results from request
     */
    public function call($method, $params = array())
    {
        $params['method'] = 'flickr.'.$method;
        $params = $params + $this->params;

        return $this->request($params);
    }

    /**
     * Magic set function, allows you to set a parameter for your request
     * ie.: $flickr->photo_id = '449499024';
     *
     * @param   string   Parameter to set
     * @param   mixed    Value to set to
     * @return  null
     *//*
    public function __set($key, $val)
    {
        $this->params[$key] = $val;
    }
    */

    /**
     * Magic get function, returns parameter value
     *
     * @param   string   Parameter to get value of
     * @return  mixed    Value of parameter
     *//*
    public function __get($key)
    {
        if (isset($this->params[$key]))
        {
            return $this->params[$key];
        }
    }
    */

    /**
     * Internal function to make request to FLickr and cache them
     *
     * @param   array   All paramaters for Flickr API request
     * @return  object  Result from Flickr
     */
    protected function request($parameters)
    {
        foreach ($parameters as $key => $val)
        {
            $params[] = urlencode($key).'='.urlencode($val);
        }

        $url = self::$rest_url.'?'.implode('&', $params);

        $url_hash = md5($url);
        $cache_file = $this->cache_dir.$url_hash.'.cache';

        if (file_exists($cache_file) AND time() < (filemtime($cache_file) + $this->cache_expire))
        {
            $response = file_get_contents($cache_file);
        }
        else
        {
            $response = file_get_contents($url);

            $file = fopen($cache_file, 'w');
                    fwrite($file, $response);
                    fclose($file);
        }

        if ($parameters['format'] == 'json') return $response;
        else {
            $response = unserialize($response);
            return self::array_2_object($response);
        }
    }

    /**
     * Internal function to convert a multi-dimensional array to an object
     *
     * @param   array    Array to convert
     * @return  object
     */
    protected static function array_2_object($array)
    {
        if ( ! is_array($array))
        {
            return $array;
        }

        $object = new stdClass;

        if (is_array($array) AND count($array) > 0)
        {
            foreach ($array as $name => $value)
            {
                if (isset($name))
                {
                    $object->$name = self::array_2_object($value);
                }
            }

            return $object;
        }

        return FALSE;
    }

    /**
     * Cleans up and handles business with cache directory
     *
     * @return   null
     */
    protected function cache()
    {
        if ( ! is_dir($this->cache_dir))
        {
            mkdir($this->cache_dir, 0777);
        }

        $dir = dir($this->cache_dir);
        while ($file = $dir->read())
        {
            $file = $this->cache_dir.$file;
            if (substr($file, -6) == '.cache' AND (filemtime($file) + $this->cache_expire) < time())
            {
                unlink($file);
            }
        }
    }

    /**
     * Simple function to nicely display any object, array, or string
     * ie.: echo $flickr->debug($result);
     *
     * @param   mixed   What you want to debug
     * @return  string
     */
    public function debug($obj)
    {
        return '<pre>'.print_r($obj, TRUE).'</pre>';
    }
}