<?php

// Additional utility includes
require_once('captcha.php');
require_once('validaters.php');

// Strips a query string from a URL
function strip_query($string)
{
    return strip_tags(preg_replace('/[?](.*)/', '', $string));
}

// Generate a random character string
function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
{
    // Length of character list
    $chars_length = (strlen($chars) - 1);

    // Start our string
    $string = $chars{rand(0, $chars_length)};

    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string))
    {
        // Grab a random character from our list
        $r = $chars{rand(0, $chars_length)};

        // Make sure the same two characters don't appear next to each other
        if ($r != $string{$i - 1}) $string .=  $r;
    }

    // Return the string
    return $string;
}

// Returns first_name and last_name from fullname
function split_name($fullname)
{
    // If there are more than two values, anything after
    // the first value will be appended to last_name
    return split(' ', $fullname, 2);
}

// Replaces underscores with spaces and makes words uppercase
function pretty_url($url)
{
    return ucwords(str_replace('_', ' ', $url));
}

// Assembles a query string.  If a key/value pair is already in
// the $_GET array, it will be replaced with the passed in value;
// otherwise, it is added to the $_GET array
function query_string($array, $html_safe = true)
{
    // We aren't actually modifying the get array, just building upon it
    $get = $_GET;

    // Update array
    foreach ($array as $key => $value) $get[$key] = $value;

    // Start query string
    $str = '?';

    // Build query string
    foreach ($get as $key => $value) $str .= $key . '=' . $value . '&';

    // Return query string (remove trailing ampersand)
    return ($html_safe ? htmlentities(substr($str, 0, -1)) : substr($str, 0, -1));
}

// Returns an object from an associative array
function objectify($array)
{
    return (object) $array;
}

// Returns the key from an array index (index can be negative)
function index_key($array, $index)
{
    return key(array_slice($array, $index, 1));
}

// Append associative array elements
function array_push_assoc(&$arr)
{
    // Get args
    $args = func_get_args();

    // Loop through args
    foreach ($args as $arg)
    {
        // Push assoc array
        if (is_array($arg))
        {
            // Store key/value pair
            foreach ($arg as $key => $value)
                $arr[$key] = $value;
        }

        // Push value
        else $arr[] = $arg;
    }

    // Return new number of elements in array
    return count($arr);
}

// Joins two or more arrays together recursively; key/value pairs of the first
// array are replaced with key/value pairs from the subsequent arrays.  Any
// key/value pair not present in the first array is added to the final array
function array_join()
{
    // Get array arguments
    $arrays = func_get_args();

    // Define the original array
    $original = array_shift($arrays);

    // Loop through arrays
    foreach ($arrays as $array)
    {
        // Loop through array key/value pairs
        foreach ($array as $key => $value)
        {
            // Value is an array
            if (is_array($value))
            {
                // Traverse the array; replace or add result to original array
                $original[$key] = array_join($original[$key], $array[$key]);
            }

            // Value is not an array
            else
            {
                // Replace or add current value to original array
                $original[$key] = $value;
            }
        }
    }

    // Return the joined array
    return $original;
}

function getlink($link, $text = null)
{
    if ($text) {
        $link = preg_replace("/(?<!<a href=\")(((http|ftp)+(s)?:\/\/)[^<>\s]+)/i", "<a href=\"\\1\" rel=\"nofollow\">$text</a>", $link);
    } else {
        $link = preg_replace("/(?<!<a href=\")(((http|ftp)+(s)?:\/\/)[^<>\s]+)/i", "<a href=\"\\1\" rel=\"nofollow\">\\1</a>", $link);
    }
    return $link;
}

function bbcode($text)
{
    $text = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/', '<a href="$1">$2</a>', $text);
    $text = preg_replace('/\[code\]([\s\S]*?)\[\/code\]/', '</p><p class="code">$1</p><p>', $text);
    $text = preg_replace('/\[img\](.*?)\[\/img\]/', '<img alt="image" src="$1" />', $text);

    return $text;
}