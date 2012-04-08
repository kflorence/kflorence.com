<?php

// Singleton Class Framework
class Framework extends Module
{
    // Singleton
    private static $_self = null;

	// Static class cannot be initialized directly
	private function __construct() {}

    // Returns the single instance of this object (must be called statically)
    public static function getInstance()
    {
        // Store a reference of this class if not initiated
        if (!isset(self :: $_self))
        {
            // Singleton creation
            self :: $_self = new self;
        }

        // Return singleton instance
        return self :: $_self;
    }

    // Attempts to return a cached class, or instantiate it if it is not
    // already stored in cache.
    public function load($class)
    {
        // First, make class name lowercase
        $class = strtolower($class);

        // Return the class instance of it's already cached
        if (isset($this->{$class})) return $this->{$class};

        // Class has not been cached yet
        else
        {
            // Makes sure class is defined (attempts to autoload if it's not)
            if (class_exists($class))
            {
                // Try to instantiate class as a singleton
                if ($this->{$class} = singleton($class))
                {
                    // If successful, return the new object
                    return $this->{$class};
                }

                // Otherwise, this class can be instantiated normally
                else
                {
                    // Try to instantiate class
                    try {
                        $this->{$class} = new $class();
                    }

                    // Could not instantiate class
                    catch (Exception $e) {
                        Error :: fatalError('Failed to initialize class "' . $class . '"', __CLASS__);
                    }
                }
            }

            // Class does not exist
            else
            {
                Error :: warning('Class not found: "' . $class . '"', __CLASS__);
            }
        }

        // Class could not be instantiated
        return false;
    }

    // Starts the server request process
    public function run()
    {
        // Load the configuration file
        $this->load('config');

        // Load startup modules
        foreach ($this->{'config'}->{'startup'} as $class => $alias) {
            $this->load($class);
        }

        // Finally, load the request
        $this->{'request'}->request();
    }
}