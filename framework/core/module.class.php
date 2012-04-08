<?php

/**
 *
 * NOTE: Each class that extends this class will get its own unique data array.
 *
 */

// This is the base class that all Modules inherit
class Module
{
    // Stores our overloaded variables
    private $_data = array();

    // Class cannot be initialized directly
    private function __construct() {}

    // Returns the data array
    public function data()
    {
    	return $this->_data;
    }

    // Print the structure of the class in a human readable format
    public function structure()
    {
        $structure = print_r($this->data(), true);

        // Make sure to conceil passwords
        $structure = preg_replace(
            '/(\[password\] =>) ([^ ]+)/ie',
            "'$1 '. preg_replace('/([^ ])/i', '*', '$2') .'\n'",
            $structure);

        // Return output, preformatted
        return '<pre>' . $structure . '</pre>';
    }

    // Default onLoad function for all modules
    public function onLoad()
    {
        if (f('config') && f('config')->{$this})
        {
            foreach(f('config')->{$this} as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    // For grabbing inaccessible variables
    public function __get($key)
    {
        // Make sure this key exists
        if (isset($this->_data[$key])) return $this->_data[$key];

        // Key does not exist
        return false;
    }

    // For setting inaccessible variables
    public function __set($key, $value)
    {
        //print $this->__toString();
        $this->_data[$key] = $value;
    }

    // For checking on the existance of innaccessible variables
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    // For unsetting inaccessible variables
    public function __unset($key)
    {
        unset($this->_data[$key]);
    }

    // Return string representation of this Object
    public function __toString()
    {
        return strtolower(get_class($this));
    }

    // Prevent users from cloning child class if it is a singleton class
    public function __clone()
    {
        // Check for singleton class
        if (method_exists($this, 'getInstance'))
        {
            Error::fatalError('Singleton class: clone is not allowed.', $this);
        }
    }
}