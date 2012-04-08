<?php

// The session class handles storing and retrieving session information
class Session extends Module
{
    // Reserved session keys
    private static $_reserved = array('ready');

    // Static class cannot be initialized directly
    private function __construct() {}

    // Return the contents of stored session information in array form
    public static function contents()
    {
        return $_SESSION;
    }

    // Alias for delete() function
    public static function del($key)
    {
    	self::delete($key);
    }

    // Unset a session key
    public static function delete($key)
    {
        // Unset the key
        unset($_SESSION[$key]);
    }

    // Destroy all sessions
    public static function destroy()
    {
        // Unset the session
        unset($_SESSION);

        // Destroy the session
        session_destroy();
    }

    // Check if a session key exists
    public static function exists($key)
    {
        // If this key exists, return true
        if (isset($_SESSION[$key])) return true;

        // Return false
        return false;
    }

    // Get session information
    public static function get($key)
    {
        // Return key if it's set
        if (isset($_SESSION[$key])) return $_SESSION[$key];

        // Otherwise return null
        else return null;
    }

    // Set session information
    public static function set($key, $value)
    {
    	// Make sure they aren't trying to set a reserved word
        if (!in_array($key, self::$_reserved)) $_SESSION[$key] = $value;

        // Otherwise, throw an error
        else Error::warning('Could not set key "' . $key . '" -- it is reserved.', __CLASS__);
    }

    // Start up the session (if needed)
    public static function start()
    {
        // If we have already started sessions, no need to do it again
        if (!isset($_SESSION['ready'])) $_SESSION['ready'] = session_start();

        // Return session_start (true or false)
        return $_SESSION['ready'];
    }
}

// Start sessions on load
Session::start();