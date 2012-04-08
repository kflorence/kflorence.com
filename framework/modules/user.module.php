<?php

// The user class stores user information
class User extends Module
{
    // User Field List
    private $_fieldList = array();
    
    // Constructor
    public function __construct()
    {        
        // Store IP address
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Override onload to grab user info
    public function onLoad()
    {        
        // Pass on to parent function
        parent::onLoad();
        
        global $f;
        
        // User isn't logged in
        if (!$this->isLoggedIn())
        {
            // Check for cookies
            if (Cookie::exists('userInfo'))
            {
                // Grab cookie information
                list ($email, $password) = explode(':', Cookie::get('userInfo'));
                
                // Build query
                $f->db->query = "SELECT * FROM {$this->table} WHERE email='$email' LIMIT 1";

                // Query database and fetch row as object
                if ($row = $f->db->fetchRow())
                {
                    // Make sure passwords match
                    if ($row->password == $password)
                    {
                        // Register session
                        Session::set('loggedIn', $row->id);
                    }
                }
            }

            // User is not logged in
            else
            {
                // Do stuff
            }
        }
        
        // If the user is now logged in, update things
        if ($this->isLoggedIn())
        {
            // Update users IP and last_on time
            $this->_update();
            
            // Cache user data
            $this->_cacheData();	
        }
    }

    // Check if user has confirmed their account
    public function hasConfirmed($user)
    {
    	global $f;
        
        // Make sure user is logged in
        if ($this->isLoggedIn())
        {
            // Fetch data by ID
            if (is_numeric($user)) {
                $f->db->query = "SELECT confirmation FROM {$this->table} WHERE id='$user' LIMIT 1";
            }
    
            // Fetch data by username
            else {
                $f->db->query = "SELECT confirmation FROM {$this->table} WHERE username='$user' LIMIT 1";
            }
    
            // If confirm is blank, the user has confirmed their account
            if ($f->db->fetchResult() == '') return true;
        }
        
        return false;
    }

    // Check if a user is logged in
    public function isLoggedIn()
    {
        global $f;
        
        // Check for session 'loggedIn'
    	if (Session::exists('loggedIn')) return true;

        // User is not logged in
        return false;
    }

    // Returns profile data for a user from specified fields (or all fields)
    public function getData($user = null, $fields = '*')
    {
        global $f;

        // If no user is passed in, use current user
        if ($user == null) $user = $this->getID();

        // Make sure we have a user
        if (isset($user))
        {
            // Start the query
            $query = "SELECT ";
            
            // Make sure we have at least one field (or a wildcard)
            if (!empty($fields))
            {
                // A string of fields was passed in (or nothing)
                if (!is_array($fields)) $fields = explode(',', $fields);
                
                // If the first value is a wildcard, they are requesting everything
                if ($fields[0] == '*') $query .= $fields[0];
                
                // Otherwise, they are requesting only specific fields
                else
                {
                    // Validate and build fields
                    for ($i = 0; $i <= count($fields); $i++)
                    {
                    	// Make sure this field is valid
                        if (in_array($fields[$i], $this->_fieldList))
                        {
                        	// Add it to the list
                            $query .= $fields[$i];
                            
                            // Add a comma (if needed)
                            if ($i != count($fields)) $query .= ', ';
                        }
                    }
                }
                
                // Continue building query
                $query .= " FROM {$this->table} WHERE ";
        
            	// Fetch data by ID
                if (is_numeric($user)) $query .= "id='$user'";
        
                // Fetch data by username
                else $query .= "username='$user'";
                
                // Finish the query
                $query .= " LIMIT 1";
        
                // Query database and fetch row as object
                if ($row = $f->db->fetchRow($query))
                {
                    // Store data locally so we don't have to query it again
                    foreach ($row as $field => $value) $this->{$field} = $value;
                    
                    // Return the results
                    return $row;
                }
            }
        }
        // User not found
        return null;
    }
    
    // Returns user ID associated with the current session (or optionally gets ID from username)
    public function getID($user = null)
    {
        global $f;
        
    	// No username passed in
        if ($user == null)
        {   
            // Use current users ID
            if ($this->isLoggedIn())
            {
                // Use cached user Id
                if (isset($this->id)) return $this->id;
                
                // Otherwise, cache the user ID for future use
                else return ($this->id = Session::get('loggedIn'));
            }
            
            // Couldn't find user ID
            else return null;
        }
        
        // Otherwise, look up ID for username
        else if (!empty($user))
        {
            // Query for ID
        	$user = $this->getProfileData($user, 'id');
            
            // Return ID (or null if not found)
            return $user->id;
        }
    }
    
    // Update user data on page load
    private function _update()
    {
    	global $f;

        // Make sure user is logged in
        if ($this->isLoggedIn())
        {
            $f->db->query = "UPDATE {$this->table} SET ip='{$this->ip}', last_on='" . time() . "' ".
                "WHERE id='{$this->getID()}' LIMIT 1";
            
            // Process query, report any failures
            if (!$r = $f->db->query()) Error::warning('Update failed: ' . mysql_error(), __CLASS__);
        }
    }
    
    // Cache user data on load
    private function _cacheData()
    {
        global $f;
        
        // Make sure user is logged in
        if ($this->isLoggedIn())
        {
            // Cache user data and build field list
            $f->db->query = "SELECT * FROM {$this->table} WHERE id='{$this->getID()}' LIMIT 1";
            
            if ($row = $f->db->fetchRow())
            {
                foreach ($row as $field => $value)
                {
                    // Cache user data
                    $this->{$field} = $value;
                    
                    // Add field to fieldList
                    $this->_fieldList[] = $field;
                }
            }
        }
    }
}
