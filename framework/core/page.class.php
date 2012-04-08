<?php

// Class Page
class Page extends URL
{
    // Default error page
    const ERROR_PAGE = 'error';
    const PAGE_INDEX = 'home';
    const SECTION_INDEX = 'index';

    // Singleton instance of this class
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

    // Override Module onLoad()
    public function onLoad()
    {
        if (f('config') && f('config')->{$this})
        {
            foreach(f('config')->{$this} as $class => $alias) {
                f()->load($class);
            }
        }
    }

    // Attempts to load a page from a URL
    public function load($url = '')
    {
        // If new URL is given, load it
        if (!empty($url)) f('url')->set($url);

        // Make sure the URL is set and points to a valid page
        if (isset(f('url')->path) && (f('url')->type == URL :: T_PAGE))
        {
            // Load the page
            f('template')->loadPage(f('url')->page);
        }

        // Otherwise, load error page
        else f('template')->loadPage(PAGE :: ERROR_PAGE);
    }

    // Return string representation of this Object (overrides parent function)
    public function __toString()
    {
        return strtolower((get_class($this) . ':' . f('url')->page));
    }
}