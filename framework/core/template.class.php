<?php

// Class Template
class Template extends Module
{
    // Singleton of Template
    private static $_self = null;

    public $css, $javascript;

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

    // Load a chunk (can be called from a page, or loaded seperately by itself)
    public function loadChunk($name, $print = true)
    {
        // Try to load from chunk base directory first
        if (file_exists($file = BASE_PATH . CHUNKS . $name . CHUNK_EXT))
            return $this->_loadFile($file, $print);

        // Next, try to load it from a page folder
        else if (file_exists($file = BASE_PATH . CHUNKS . f('url')->page . '/' . $name . CHUNK_EXT))
            return $this->_loadFile($file, $print);

        // Try to load it from the section folder
        else if (file_exists($file = BASE_PATH . CHUNKS . f('url')->page . '/' . f('url')->section . '/' . $name . CHUNK_EXT))
            return $this->_loadFile($file, $print);

        // Chunk not found
        else
        {
            // Issue our error and return false
            Error::warning('Unable to load "' . $name . CHUNK_EXT . '": file not found.', __CLASS__);

            return false;
        }
    }

    // Load a page
    public function loadPage($name, $print = true)
    {
        // Try to load this page
        if (file_exists($file = BASE_PATH . PAGES . $name . PAGE_EXT))
            return $this->_loadFile($file, $print);

        // Page not found
        else Error::warning('Unable to load "' . $name . CHUNK_EXT . '": file not found.', __CLASS__);
    }

    // Extracts variables and loads a file (and optionally prints it)
    private function _loadFile($file, $print = true)
    {
        // Make sure this file exists
        if (file_exists($file))
        {
            // Extract framework data
            extract(f()->data());

            // Start output buffer
            ob_start();

            // Include our file
            include($file);

            // Clean output buffer and save contents
            $data = ob_get_clean();

            // Print or return data
            return ($print ? print($data) : $data);
        }

        // File does not exist
        else Error::warning('Could not load file: "' . $file . '"');
    }
}