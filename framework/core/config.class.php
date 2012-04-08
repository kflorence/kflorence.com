<?php

// Static class Config
class Config extends Module
{
    // Parse the configuration file(s)
    public function __construct()
    {
        // Make sure default config file exists
        if (file_exists($file = BASE_PATH . FRAMEWORK_CONFIG))
        {
            // Store config settings
            foreach (parse_ini_file($file, true) as $key => $value) {
                $this->{strtolower($key)} = $value;
            }

            // Check for custom config file
            if (file_exists($file = BASE_PATH . SITE_CONFIG))
            {
                // Override framework config settings with the site config
                foreach (parse_ini_file($file, true) as $key => $value)
                {
                    $key = strtolower($key);

                    // If the key is an array, use array_join
                    if (is_array($key)) {
                        $this->{$key} = array_join($this->{$key}, $key);
                    }

                    // Otherwise, overwrite (or add) key
                    else $this->{$key} = $value;
                }
            }
        }

        // Could not load config
        else
        {
            Error::fatalError(
                'Framework config not found: "' .
                BASE_PATH . FRAMEWORK_CONFIG . '"', __CLASS__);
        }
    }
}