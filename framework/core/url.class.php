<?php

class URL extends Module
{
	// Constants for URL type
    const T_DIR  = 'directory';
	const T_FILE = 'file';
    const T_PAGE = 'page';
    const T_UNKN = 'unknown';

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

    // Perform any onLoad functions
    public function onLoad()
    {
    	// Store the REQUEST_URI
        $this->set(REQUEST_URI);
    }

    // Returns the path and optionally: filename, extension, array, page and URL type
    public function get($url, $args = array('path'))
    {
        // Start results array
        $results = array();

        // Store URL with query strings removed (default)
        $url = strip_query($url);

        // Loop through arguments and store results
        foreach ($args as $property)
        {
            // Build results array based on properties
            switch ($property)
            {
                // Store URL with query strings removed (default)
                case 'path':
                {
                    // Remove trailing slash (if it has one)
                    if (substr($url, -1) == '/') $url = substr($url, 0, -1);

                    $results[] = $url;
                    break;
                }

                // Store filename (empty string if non-existant)
                case 'filename':
                {
                    $results[] = substr(strrchr($url, '/'), 1);
                    break;
                }

                // Store extension (empty string if non-existant)
                case 'extension':
                {
                    $results[] = substr(strrchr($url, '.'), 1);
                    break;
                }

                // Store URL as array ('index' if non-existant)
                case 'array':
                {
                	$results[] = $this->parse($url);
                    break;
                }

                // Store page name (empty string if non-existant)
                case 'page':
                {
                    list ( , $results[]) = $this->parse($url, true);
                	break;
                }

                // Store URL type
                case 'type':
                {
                    $results[] = $this->type($url);
                }
            }
        }

        // Return as array
        return $results;
    }

	// Stores URL information (returns URL type)
	public function set($url)
	{
        // Get URL information
        list (
            $this->path,
            $this->filename,
            $this->extension,
            $this->array,
            $this->page,
            $this->type
        ) = $this->get($url, array (
            'path',
            'filename',
            'extension',
            'array',
            'page',
            'type'
        ));

        // Set section ('index' if non-existant)
        $this->section = (isset($this->array[1]) ? $this->array[1] : Page::SECTION_INDEX);

        // Sets and returns the URL type
        return $this->type;
	}

    // Returns the URL type, or T_UNKN if URL is non-existant
    public function type($url)
    {
        // Generate page name and extension from URL
        list ($page, $extension) = $this->get($url, array('page', 'extension'));

        // Check for index page
        if ($page == Page::PAGE_INDEX)
            return self::T_PAGE;

        // If directory exists, return T_DIR
        if (is_dir(DOC_ROOT . BASE_DIR . $url))
            return self::T_DIR;

        // If the url has an extension, return T_FILE
        if (!empty($extension))
            return self::T_FILE;

        // if page exists, return T_PAGE
        if (file_exists(BASE_PATH . PAGES . $page . PAGE_EXT))
            return self::T_PAGE;

        // Otherwise, return T_UNKN
        return self::T_UNKN;
    }

    // Parses a URL into an array (and optionally returns page name)
    public function parse($url, $page = false)
    {
        // Remove query string from URL
        $url = strip_query($url);

        // Break the URL into an array (remove BASE_DIR)
        $url_array = explode('/', $url);

        // If the array is empty at this point, we are dealing with the index
        if (count($url_array) == 1 && empty($url_array[0])) $url_array[0] = Page::PAGE_INDEX;

        // Remove leading value if empty
        if (empty($url_array[0])) array_shift($url_array);

        // Remove trailing value if empty
        if (empty($url_array[count($url_array) - 1])) array_pop($url_array);

        // Return URL array and page name
        if ($page) return array($url_array, $url_array[0]);

        // Return URL array only (default)
        return $url_array;
    }

    // Builds a link (with BASE_URL as root) and prints it
    public function build()
    {
        // Grab strings
        $sections = func_get_args();

        // Build the URL and strip empty sections
        $url = str_replace('//', '/', implode('/', $sections));

        // Return built url with or without BASE_DIR appended
        return BASE_DIR . $url;
    }

    // Returns true if the current page is selected
    public function selected($page)
    {
    	if ($page == $this->page) return true;

        // Not selected
        return false;
    }

    // Redirect to given url (works before or after headers have been sent)
    public function redirect($url)
    {
        // If headers have not been sent, redirect with PHP
        if (!headers_sent()) header('Location: ' . $url);

        // Otherwise, redirect with javascript or html
        else
        {
            print('<script type="text/javascript">window.location.href="' . $url . '";</script>');
            print('<noscript><meta http-equiv="refresh" content="0;url=' . $url . '" /></noscript>');
        }

        // Stop script execution
        exit;
    }
}
