<?php

class FlickrRequest
{
    // Constructor
    public static function process($data)
    {
        // What are they requesting?
        switch (f('request')->action)
        {
            // Caches filter settings
            case 'getPublicPhotos':
            {
                // Set up flickr requests
                f()->load('Flickr');

                $params = array (
                    'format' => 'json',
                    'user_id'  => '97742267@N00',
                    'extras' => 'url_m,url_sq',
                    'page' => f('request')->data->page,
                    'per_page' => f('request')->data->perpage,
                    'nojsoncallback' => '1'
                );

                print f('flickr')->call('people.getPublicPhotos', $params);

                // Don't redirect
                exit;
            }
        }
    }
}