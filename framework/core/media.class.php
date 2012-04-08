<?php

// Static Class Media
class Media extends Module
{
	// Allowed fetchable file types
	public static $allowableTypes = array(
	    'gif', 'jpg', 'png',
		'ico', 'css', 'js',
	    'html', 'php', 'swf'
	);

    // This class cannot be initialized
    private function __construct() {}

    // Attempts to fetch media from a URL
    public static function load($url = '')
    {
        // Use new URL if one is given
        if (!empty($url)) f('url')->set($url);

        // Make sure the URL is set and is a file
        if (isset(f('url')->path) && (f('url')->type == URL :: T_FILE))
        {
            // Build file path relative to the media folder
            $file = BASE_PATH . MEDIA . f('url')->path;

            // Make sure the file exists
            if (file_exists($file))
            {
                // Check for the file extension in our allowed media types
                if (in_array(f('url')->extension, self :: $allowableTypes))
                {
                    // Set content-type based on file extension
                    switch (f('url')->extension)
                    {
                        // Plain text
                        case 'txt':
                        {
                            $type = 'text/plain';
                            break;
                        }

                        // HTML
                        case 'html':
                        {
                            $type = 'text/html';
                            break;
                        }

                        // Javascript
                        case 'js':
                        {
                            $type = 'text/javascript';
                        	break;
                        }

                        // Cascading style sheets
                        case 'css':
                        {
                            $type = 'text/css';
                            break;
                        }

                        // JPEG images
                        case 'jpg':
                        case 'jpeg':
                        {
                            $type = 'image/jpeg';
                            break;
                        }

                        // GIF and ICO images
                        case 'gif':
                        case 'ico':
                        {
                            $type = 'image/gif';
                            break;
                        }

                        // PNG images
                        case 'png':
                        {
                            $type = 'image/png';
                            break;
                        }

                        // Flash files
                        case 'swf':
                        {
                            $type = 'application/x-shockwave-flash';
                            break;
                        }

                        // PHP file
                        case 'php':
                        {
                            require_once($file);
                            exit;
                        }

                        // Unknown extension
                        default:
                        {
                            $type = false;
                            break;
                        }
                    }

                    // Output content-type header
                    if ($type) header('Content-type: ' . $type);

                    // Read in the file
                    readfile($file);
                }

                // Otherwise it's an unallowed file type
                else Error::fatalError(
                    'Poor media request: invalid file extension.', __CLASS__);
            }

            // File not found
            else Error::fatalError(
                'Media not found: "' . $file . '"', __CLASS__);
        }

        // URL is invalid or missing
        else Error::fatalError(
            'URL is invalid or missing: "' . $file . '"', __CLASS__);
    }
}