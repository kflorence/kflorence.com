/*******************************************************************************
 * flickrGallery - jQuery Tools Plugin
 *******************************************************************************
 *
 * Copyright (c) 2009 Kyle Florence (http://kyleflorence.com)
 * Licensed under the GPL (GPL-LICENSE.txt) license.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @author Kyle Florence
 *  @version 0.1
 */
jQuery(function($)
{
    // flickrGallery options
    var defaults = {
        url : '/kflorence.com/?do=flickr:getPublicPhotos'
    }

    // flickrGallery jQuery extension
    $.fn.flickrGallery = function(o)
    {
        return this.each(function() {
            new $.flickrGallery(this, o);
        });
    };

    // flickrGallery class
    $.flickrGallery = function(e, o)
    {
        // reference for inner functions
        var self = this;

        // get options
        this.options = $.extend({}, defaults, o || {});

        // get scrollable api
        this.scrollable = $(e).scrollable();

        // get api options
        this.scrollable.options = this.scrollable.getConf();

        // bind navigational functions
        $('.prevItem').bind('click', function() {self.prevItem()});
        $('.nextItem').bind('click', function() {self.nextItem()});
        $('.prevPage').bind('click', function() {self.prevPage()});
        $('.nextPage').bind('click', function() {self.nextPage()});

        // initialize
        this.initialize();
    }

    // setup flickrGallery as extendable class object
    $.flickrGallery.fn = $.flickrGallery.prototype = {flickrGallery:'0.1'};
    $.flickrGallery.fn.extend = $.flickrGallery.extend = $.extend;

    // flickrGallery functions
    $.flickrGallery.fn.extend(
    {
        // Setup flickrGallery
        initialize: function()
        {
            // Class reference for inner functions
            var self = this;

            // Grab the image wrapper
            var wrap = $('#image_wrap');

            // Grab the current image
            var wrap_img = wrap.find('img');

            // Wait for the image to load...
            wrap_img.load(function()
            {
                // Assign width/height to the image wrapper
                wrap.css({
                    width: wrap_img.width(),
                    height: wrap_img.height()
                });
            });

            // Request flickr JSON for items
            $.getJSON(this.options.url,
            {
                page : 1,
                perpage : this.itemsPerPage()
            },

            // Handle the response
            function(data)
            {
                // add the first page to the list
                $.each(data.photos.photo, function(i, photo) {
                    self.itemAdd(photo);
                });

                // store photos data
                self.totalPages = data.photos.pages;
                self.totalItems = data.photos.total;

                // set current item, current page and number of loaded pages
                self.currentItem = self.currentPage = self.loadedPages = 1;

                // select first item
                self.scrollable.click(0);

                // load the items for the next page
                self.nextPage();
            });
        },

        // return the size of pages from scrollable API
        itemsPerPage: function()
        {
            return this.scrollable.options.size;
        },

        // prev click handler
        prevItem: function()
        {
            // set target item
            var targetItem = (this.currentItem - 1);

            // make sure target exists
            if (targetItem > 0)
            {
                // get target item
                var item = $('#t_' + targetItem);

                // get active item class
                var active = this.scrollable.options.activeClass;

                // scroll to the previous item
                this.scrollable.prev();

                // disable all active items
                this.scrollable.getItems().removeClass(active);

                // activate current list item
                item.parent().addClass(active);

                // load target item
                this.itemChange(targetItem, item.attr('rel'));
            }
        },

        // prev click handler
        nextItem: function()
        {
            // set target item and target page
            var targetItem = (this.currentItem + 1);
            var targetPage = (Math.floor(targetItem / this.itemsPerPage()));

            // make sure target exists
            if (targetItem <= this.totalItems)
            {
                // If we are going onto a new page, load items for next page
                if (targetPage > (this.loadedPages - 1)) this.nextPage();

                // get target item
                var item = $('#t_' + targetItem);

                // get active item class
                var active = this.scrollable.options.activeClass;

                // scroll to the next item
                this.scrollable.next();

                // disable all active items
                this.scrollable.getItems().removeClass(active);

                // activate current list item
                item.parent().addClass(active);

                // load target item
                this.itemChange(targetItem, item.attr('rel'));
            }
        },

        // prevPage click handler
        prevPage: function()
        {
            // reset current page (not less than zero)
            this.currentPage = ((this.currentPage - 1) < 0 ? 0 : this.currentPage - 1);
        },

        // nextPage click handler
        nextPage: function()
        {
            // request the items one page ahead
            this.itemRequest((this.currentPage + 1), this.itemsPerPage());
        },

        // Make a flickr request for items
        itemRequest: function(page, perpage)
        {
            // Set current page to page
            this.currentPage = page;

            // Make sure this is a valid request
            if ((page > this.loadedPages) && (page <= this.totalPages))
            {
                // Class reference for inner functions
                var self = this;

                // Request flickr JSON for items
                $.getJSON(this.options.url,
                {
                    page : page,
                    perpage : perpage
                },

                // Handle the response
                function(data)
                {
                    // add photos to the list
                    $.each(data.photos.photo, function(i, photo) {
                        self.itemAdd(photo);
                    });

                    // Set current to page
                    self.loadedPages = page;
                });
            }
        },

        // Add an item to our scrollable list
        itemAdd: function (photo)
        {
            // Make sure we don't already have this item
            if (!$('#' + photo.id).length)
            {
                // Class reference for inner functions
                var self = this;

                // Get scrollable items wrapper
                var wrap = this.scrollable.getItemWrap();

                // build our list item
                var li = $('<li/>').fadeTo(0, 0).attr({
                    id : photo.id,
                    class : 'item'
                }).bind('click', function() {self.itemClick(this)});

                // append our list item
                wrap.append(li);

                // build empty span (for active item styles)
                var span = $('<span/>');

                // append span to list item
                li.append(span);

                // build our image
                var img = $(new Image()).load(function()
                {
                    // append the image to our list item, then fade in
                    li.append(img).fadeTo('slow', 1);

                    // rebuild the list
                    self.scrollable.reload();

                // begin loading, set attributes
                }).attr({
                    id      : 't_' + self.scrollable.getSize(),
                    alt     : photo.title,
                    title   : photo.title,
                    src     : photo.url_sq,
                    rel     : photo.url_m
                });
            }
        },

        // What to do when an item is clicked
        itemClick: function(e)
        {
            // Get element as jquery object
            var ele = $(e).find('img');
            var targetItem = ele.attr('id').replace('t_', '');

            // Change to clicked item
            this.itemChange(targetItem, ele.attr('rel'));
        },

        // change items
        itemChange: function(id, src)
        {
            // Grab image wrapper
            var wrap = $('#image_wrap');

            // Grab reference to old image
            var oldimg = wrap.find('img');

            // Create new image
            var newimg = $(new Image()).attr({src:src})
                .css({display:'none'});

            // Make sure the new image has loaded
            newimg.load(function()
            {
                // Fade out the old image, adjust wrapper, fade in new image
                oldimg.fadeOut('fast', function()
                {
                    // Delete the old image and append the new one
                    wrap.empty().append(newimg);

                    // Adjust width/height of wrapper, then fade in image
                    wrap.animate({
                        width: newimg.width(),
                        height: newimg.height()
                    }, 'slow', 'swing', function() {
                        newimg.fadeIn('medium');
                    });
                });
            });

            // finally, set our new current item
            this.currentItem = parseInt(id);
        }
    });

    // -------------------------------------------------------------------------
    // Gallery
    // -------------------------------------------------------------------------

    // Enable javascript styles
    $('.gallery').addClass('javascript');

    // Overwrite anchors for prev/next
    $('.gallery .prevItem').removeAttr('href');
    $('.gallery .nextItem').removeAttr('href');

    // Set up scrollable
    $(".gallery .scrollable").scrollable({size:6}).flickrGallery();
});