<?php

/**
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email
address format and the domain exists.
*/
function is_email($email, $dnscheck = false)
{
    $atIndex        = strrpos($email, "@");
    $emailLength    = strlen($email);

    // Email length is too long
    if ($emailLength > 256)
        return false;

    // No at-sign
    else if ($atIndex === false)
        return false;

    // No local part
    else if ($atIndex === 0)
        return false;

    // No domain part
    else if ($atIndex === $emailLength)
        return false;

    // Validate local and domain parts
    else
    {
        $domain     = substr($email, $atIndex + 1);
        $local      = substr($email, 0, $atIndex);
        $localLen   = strlen($local);
        $domainLen  = strlen($domain);

        // Local part length exceeded
        if ($localLen < 1 || $localLen > 64)
            return false;

        // Domain part length exceeded
        if ($domainLen < 1 || $domainLen > 255)
            return false;

        // local part starts or ends with '.'
        if ($local[0] == '.' || $local[$localLen - 1] == '.')
            return false;

        // local part has two consecutive dots
        if (preg_match('/\\.\\./', $local))
            return false;

        // character not valid in domain part
        if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
            return false;

        // domain part has two consecutive dots
        if (preg_match('/\\.\\./', $domain))
            return false;

        // character not valid in local part unless local part is quoted
        if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local)))
        {
            if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) return false;
        }

        // DNS Check for mail server at domain
        if ($dnscheck)
        {
            // domain not found in DNS
            if (!(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) return false;
        }
    }

    // Valid email
    return true;
}

// Validate a name
function is_name($name)
{
    // Name is valid
    if (preg_match('/^[a-z\s\.]+$/i', $name)) return true;

    // Name is not valid
    return false;
}

// Validates a date
function is_date($date)
{
    $time = strtotime($date);
    $m    = date('m', $time);
    $d    = date('d', $time);
    $y    = date('Y', $time);

    // Validate via checkdate
    return checkdate($m, $d, $y);
}

// Returns true if class is a singleton class, false otherwise
function is_singleton($class)
{
    // First, make sure this class exists
    if (class_exists($class))
    {
        // Check for a static class
        if (!in_array('__construct', get_class_methods($class)))
        {
            // Check for singleton class
            if (method_exists($class, 'getInstance')) return true;
        }
    }

    // Class is not singleton or does not exist
    return false;
}