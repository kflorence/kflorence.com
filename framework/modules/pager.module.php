<?php

// Static class Pager
class Pager extends Module
{
    // Template properties
    public $template = 'pager';

    // Constructor
    public function __construct() {}

    // Generate pagination from the given limit and range.
    public function generate($num_items, $per_page = 10, $link_range = 2)
    {
        // If we have no base URL, default to the page url
        if (!isset($this->url) || empty($this->url)) {
            $this->url = strip_query(PAGE_URL);
        }

        // Set the total number of items
        $this->num_items = intval($num_items);

        // Set the amount of items to show per page (false = display all)
        $this->per_page = ($per_page ? intval($per_page) : $this->num_items);

        // Set the current page
        $this->cur_page = $this->cur_page();

        // Set the range of page links to display
        $this->link_range = intval($link_range);

        // Set the first item to query
        $this->start_item = (($this->cur_page - 1) * $this->per_page);

        // Store total number of pages
        if ($this->num_items == 0) {
            $this->pages = $this->num_items;
        }

        // Prevent division by zero
        else
        {
            // The number of items divides evenly into the number of pages
            if (($this->num_items % $this->per_page) == 0) {
                $this->pages = ($this->num_items / $this->per_page);
            }

            // Divide and round down, then add an extra page for the remainder
            else $this->pages = (floor($this->num_items / $this->per_page) + 1);
        }

        // Pagination has been generated
        return $this->pages;
    }

    // Returns a link to the first page
    public function first_page()
    {
        return $this->url . query_string(array('page' => '1'));
    }

    // Returns a link to the previous page
    public function previous_page()
    {
        return $this->url . query_string(array('page' =>
            (($this->cur_page > 1) ? $this->cur_page - 1 : 1)
        ));
    }

    // Returns a link to the next page
    public function next_page()
    {
        return $this->url . query_string(array('page' =>
            (($this->cur_page < $this->pages) ? $this->cur_page + 1 : $this->pages)
        ));
    }

    // Returns a link to the last page
    public function last_page()
    {
        return $this->url . query_string(array('page' => $this->pages));
    }

    // Returns the current page
    public function cur_page()
    {
        return (!empty($_GET['page']) ? $_GET['page'] : 1);
    }

    // Controls the output for this class
    public function output($print = true)
    {
        // Pass output to template class
        return f('template')->loadChunk($this->template, $print);
    }
}