<?php

 // TODO: make requests class based instead of string based (do = "class:function")
/**
 * request.php
 * -----------------------------------------------
 *
 * Breaks server requests down into their sub-section pages
 *
 */

class Request extends Module
{
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
            // Check for saved request
            if (Session::exists('_REQ'))
            {
                // Store the request object
                self :: $_self = unserialize(Session::get('_REQ'));

                // Destroy the session
                Session::delete('_REQ');
            }

            // Otherwise, create a new request
            else self :: $_self = new self;
        }

        // Return singleton instance
        return self :: $_self;
    }

    // Called when this class is instatiated
    public function onLoad()
    {
        parent::onLoad();

        // Create arrays
        $this->data = array();
        $this->errors = array();
        $this->elements = array();

        // Default values
        $this->do           = '';
        $this->section      = '';
        $this->action       = '';
        $this->redirect     = true;
        $this->redirectURL  = PAGE_URL;
    }

    // Load a URL request
    public function request($url = REQUEST_URI)
    {
        // Parse and store URL information
        f('url')->set($url);

        // Our next action is based on the URL type
        switch (f('url')->type)
        {
            // User is requesting a media file
            case (URL::T_FILE):
            {
                Media::load();
                break;
            }

            // User is requesting a page
            case (URL::T_PAGE):
            {
                // Load the Page module
                f()->load('page');

                // Process any queries
                $this->query();

                // Load the page
                f('page')->load();
                break;
            }

            // User is requesting a directory
            case (URL::T_DIR):
            {
                // Is this documentation?
                if (f('url')->path == 'doc')
                {
                    echo '<pre>', print_r(f('url')), '</pre>';
                }

                // Error: cannot list directory contents!
                else Error::warning(
                    'Access denied to folder: '. f('url')->path, __CLASS__);
                break;
            }

            // Page, file or directory not found
            case (URL::T_UNKN):
            {
                // Try to load the error page
                if (!f('template')->loadChunk('errors/404'))
                {
                    // Issue a warning
                    Error::warning('404 Error: Page not found.');

                    // Display our warning
                    print(array_pop(Error::get()));
                }
                break;
            }

            // Unrecognized URI type
            default:
            {
                Error::fatalError(
                    'The server request was not recognized.', __CLASS__);
                break;
            }
        }
    }

    // Process a server query
    public function query()
    {
        // Store request based on request method
        switch($_SERVER['REQUEST_METHOD'])
        {
            // GET request
            case 'GET':
            {
                $_REQ =& $_GET;
                break;
            }

            // POST request
            case 'POST':
            {
                $_REQ =& $_POST;
                break;
            }
        }

        // Only process if we have new data
        if (isset($_REQ) && !empty($_REQ))
        {
            // No need to process if we have nothing to do
            if (!isset($_REQ['do']) || empty($_REQ['do'])) return;

            // Store request data in object form
            $this->data = objectify($_REQ);

            // Store request name
            $this->data->do = explode(':', $this->data->do);

            // Check for a valid request
            if (count($this->data->do) == 2)
            {
                // Store the section and action requested
                list($this->section, $this->action) = $this->data->do;

                // Make sure the request section and action are not empty
                if (!empty($this->section) && !empty($this->action))
                {
                    // Include the required class file
                    if (class_exists($this->class = ucfirst($this->section) . 'Request'))
                    {
                        // Process the request and store the response
                        $this->response = call_user_func(array($this->class, 'process'), $this->data);

                        // Save this request if there are errors
                        if (count($this->errors) > 0)
                            $this->save();

                        // Redirect if needed
                        if ($this->redirect)
                            header('Location: ' . $this->redirectURL);
                    }

                    // Class not found
                    else Error::warning('Request not found.', __CLASS__);
                }

                // Invalid request
                else Error::warning('Invalid request.', __CLASS__);
            }

            // Invalid request
            else Error::warning('Invalid request.', __CLASS__);
        }
    }

    // Add an error to the errors list (and optionally mark an element)
    public function error($line, $msg, $id = '')
    {
        // Make sure this error id isn't already on the list
        if (!array_key_exists($line, $this->errors))
        {
            // Add error to the errors list
            $this->errors[$line] = $msg;

            // Add element id to the elements list (if one is given)
            if (!empty($id)) $this->elements[] = $id;
        }

        // Error ID already exists in list
        else Error::warning(
            'Error code "' . $line . '" already exists in error list.', __CLASS__);
    }

    // Whether or not key exists in data
    public function exists($key)
    {
        // Key exists (always use singleton)
        if (array_key_exists($key, $this->data)) return true;

        // Key doesn't exist
        return false;
    }

    // Get request data
    public function get($key)
    {
        // Make sure this key exists (always use singleton)
        if (array_key_exists($key, $this->data)) return $this->data[$key];

        // Key doesn't exist
        return null;
    }

    // Returns whether or not this request matches the string(s) entered
    public function matches()
    {
    	// Grab strings
        $requests = func_get_args();

        // Check to see if the request name matches
        if (in_array($this->do, $requests)) return true;

        // Check to see if the section name matches
        if (in_array($this->section, $requests)) return true;

        // The request doesn't match
        return false;
    }

    // Save this request in a session
    public function save()
    {
        // Serialize request and save to session '_REQ'
        Session::set('_REQ', serialize($this));
    }

    // Set the redirection URL
    public function setRedirectURL($url)
    {
        // Location of redirection
        if (!empty($url)) $this->redirectURL = $url;
    }

    // Set redirection boolean
    public function setRedirect($bool)
    {
    	// Whether or not to redirect
        if (is_bool($bool)) $this->redirect = $bool;
    }
}