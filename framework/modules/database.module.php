<?php

// The database class handles connections and queries to the MySQL database
class Database extends Module
{
    // Database connection link
    private $_link = null;

    // Singleton of Database
    private static $_self = null;

    // This class cannot be initialized
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

    // Called when this class is instatiated
    public function onLoad()
    {
        parent::onLoad();

        // Connect to the database
        $this->connect($this->server, $this->username, $this->password, $this->database);
    }

    // Connect to a database
    public function connect($server, $username, $password, $database)
    {
        global $f;

        // If we are already connected, return
        if (isset($this->_link)) return;

        // Establish our connection link to the database, or throw an error if we could not connect.
        if (!$this->_link = mysql_connect($server, $username, $password)) {
            Error :: warning('Could not connect to MySQL server at "' . $server . '."', __CLASS__);
        }

        // Select our database, throw an error if it does not exist.
        if (!mysql_select_db($database)) {
            Error :: warning('Could not select database "' . $database . '."', __CLASS__);
        }
    }

    // Queries the database and returns the resulting resource handler
    public function query($q = '')
    {
        // No query passed in
        if (empty($q))
        {
            // Use preset query if present
            if (isset($this->query)) $q = $this->query;

            // We have no query to use
            else
            {
                // Complain and return false
                $this->_error('Cannot query: query string not set.');
                return false;
            }
        }

        // Return the result
        return @($this->result = mysql_query($q));
    }

    // Fetch the result of a query resource as an object (default)
    public function fetch($r, $t = 'object')
    {
        // Return based on type
        switch ($t)
        {
            // Fetch as associative array
            case 'assoc':
            {
                return @mysql_fetch_assoc($r);
                break;
            }

            // Fetch as an enumerated array
            case 'row':
            {
                return @mysql_fetch_row($r);
                break;
            }

            // Fetch as object (default)
            case 'object':
            {
                return @mysql_fetch_object($r);
                break;
            }

            // Unrecognized fetch type
            default:
            {
                $this->_error('Unrecognized fetch type: "' . $t . '".');
                break;
            }
        }
    }

    // Fetch the result of a query resource (defaults to first result)
    public function result($r, $index = 0)
    {
        return @mysql_result($r, $index);
    }

    // Returns an array of all rows returned by a query result (default: stdClass object)
    public function fetchAll($r, $t = 'object')
    {
        // Initalize empty rows array
        $rows = array();

        // Fetch all rows for this query resource
        while ($row = $this->fetch($r, $t)) $rows[] = $row;

        // Return rows array
        return $rows;
    }

    // Returns an array of items from all rows returned by a query result
    // Remember that the item type must match the result type (object, assoc, row)
    // And also that it is case sensitive, so it's best to use the row type
    public function fetchItems($r, $i, $t = 'object')
    {
        // Initalize empty items array
        $items = array();

        // Fetch all rows for this query resource
        while ($row = $this->fetch($r, $t)) $items[] = $row[$i];

        // Return items array
        return $items;
    }

    // Returns a row from a query result
    public function fetchRow($q = '', $t = 'object')
    {
        return $this->fetch($this->query($q), $t);
    }

    // Returns all rows from a query result
    public function fetchRows($q = '', $t = 'object')
    {
        return $this->fetchAll($this->query($q), $t);
    }

    // Queries the database and returns a result (defaults to first result)
    public function fetchResult($q = '', $index = 0)
    {
        return $this->result($this->query($q), $index);
    }

    // Whether or not a query would produce results
    public function fetchHasResults($q = '')
    {
        return $this->hasResults($this->query($q));
    }

    // Returns the number of results returned
    public function numResults($r)
    {
        return @mysql_num_rows($r);
    }

    // Whether or not the query resource has results
    public function hasResults($r)
    {
        return ($this->numResults($r) > 0 ? true : false);
    }

    // Returns the ID of the last INSERT query
    public function lastInsertID()
    {
        return @mysql_insert_id();
    }

    // Returns an array of field names for the specified table
    public function listFields($table)
    {
        return $this->fetchItems($this->query("SHOW FIELDS FROM $table"), 0, 'row');
    }

    // Makes strings safe to enter into the database
    public function escapeString($s)
    {
        return @mysql_real_escape_string(str_replace('`', '\`', stripslashes($s)));
    }

    // Invalid query given (or query not present)
    private function _error($msg)
    {
        // Issue a warning
        Error :: warning($msg, __CLASS__);
    }
}