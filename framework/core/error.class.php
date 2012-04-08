<?php

// Relays error messages to the user
class Error extends Module
{
    // Error list
    public static $errors = array();

    // This class cannot be initialized
    private function __construct() {}

	// Page execution ceases
	public static function fatalError($error, $class = false)
	{
        if (ENVIRONMENT == 'development')
        {
    		// If we are handling an exception, grab it's message
            if ($error instanceof Exception) $error = $error->getMessage();

            // Print message and exit
            die(($class ? '[<em>' . $class . '</em>] ' : '') .
                '<strong>Fatal error:</strong> ' . $error . '<br />');
        }
	}

	// Page execution may continue
	public static function warning($error, $class = false)
	{
        if (ENVIRONMENT == 'development')
        {
            // If we are handling an exception, grab it's message
            if ($error instanceof Exception) $error = $error->getMessage();

            // Add it to the errors list
    		self::$errors[] = ($class ? $class : 'anonymous') . ':::' . $error;
        }
	}

    // Return the errors list
    public static function get()
    {
    	return self::$errors;
    }
}