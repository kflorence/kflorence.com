<?php

// Convenience method for printing
function p($str) { print($str); }

// Convenience method for accessing framework classes and methods
function f($class = '')
{
    global $f;

    // Return class reference if given
    if (!empty($class)) {
        if (isset($f->{$class})) return $f->{$class};
    }

    // Otherwise, return framework reference
    else if (isset($f)) return $f;

    // Reference not found
    return false;
}

// Instantiates an instance of Singleton class 'class', calls its onLoad()
// function, then returns the resulting class object.
function singleton($class)
{
    // Make sure we have a valid singleton class
    if (is_singleton($class))
    {
        // Try to instantiate class statically
        try { $obj = call_user_func(array($class, 'getInstance')); }

        // Could not instantiate class
        catch (Exception $e) {
            Error :: fatalError('Failed to initialize class "' . $class . '"', __CLASS__);
        }

        // If we successfully instatiated the class, call it's onload function (if it exists)
        if (isset($obj))
        {
            // Check for onLoad method
            if (method_exists($obj, 'onLoad'))
            {
                // Try to call the onLoad method
                try { $obj->onLoad(); }

                // Could not call onLoad method
                catch (Exception $e) {
                    Error :: fatalError('Method call onLoad() failed for "' . $class . '"', __CLASS__);
                }
            }

            // Return the new class
            return $obj;
        }
    }

    // Class doesn't exist
    return false;
}

// For automatically loading modules/classes that have not been manually included
function __autoload($class)
{
    // First, make class name lowercase
    $class = strtolower($class);

    // Try to load a core class
    if (file_exists($file = BASE_PATH . CORE . $class . '.class.php'))
        require_once($file);

    // Try to load from module folder
    else if (file_exists($file = BASE_PATH . MODULES . $class . '.module.php'))
        require_once($file);

    // Try to load from requests folder
    else if (file_exists($file = BASE_PATH . REQUESTS .
        str_replace('request', '', $class) . '.request.php'))
        require_once($file);

    // Failed to load file
    else Error::warning('Could not autoload class: ' . $class, 'AutoLoader');
}