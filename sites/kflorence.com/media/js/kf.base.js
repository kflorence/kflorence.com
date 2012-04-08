// -----------------------------------------------------------------------------
// Cufon
// -----------------------------------------------------------------------------

// Replacing headings h1-h3 with Univers Light
Cufon.replace('h1, h2, h3');

//------------------------------------------------------------------------------
// jQuery
//------------------------------------------------------------------------------

jQuery(function($)
{
    // -------------------------------------------------------------------------
    // Global
    // -------------------------------------------------------------------------

    // Assign external links to class 'external'
    $('a[href^="http://"]:not([href*="sere.in"])')
        .addClass("external").click(function() {
        window.open($(this).attr('href'));
        return false;
    });

    // Bind elements to expandCollapse
    $('.details').expandCollapse({ startHidden : true });

    // -------------------------------------------------------------------------
    // Navigation
    // -------------------------------------------------------------------------

    // Me
    $('#subnav-me').expandCollapse({
        updateText        : false,
        updateClass       : false,
        startHidden       : ($('body#me').length ? false : true),
        triggerEvent      : "click",
        triggerElement    : $('#trigger-me'),
        expandDuration    : "fast",
        collapseDuration  : "slow"
    });

    // Work
    $('#subnav-work').expandCollapse({
        updateText        : false,
        updateClass       : false,
        startHidden       : ($('body#work').length ? false : true),
        triggerElement    : $('#trigger-work'),
        expandDuration    : "fast",
        collapseDuration  : "slow"
    });

    // Play
    $('#subnav-play').expandCollapse({
        updateText        : false,
        updateClass       : false,
        startHidden       : ($('body#play').length ? false : true),
        triggerElement    : $('#trigger-play'),
        expandDuration    : "fast",
        collapseDuration  : "slow"
    });

    // -------------------------------------------------------------------------
    // Portfolio
    // -------------------------------------------------------------------------

    // Enable javascript styles
    $('.portfolio').addClass('javascript');

    // Set up scrollable
    $(".portfolio .scrollable").scrollable({
        size : 1,
        clickable : false,

        // adjust item height on item change
        onBeforeSeek : function(targetIndex)
        {
            this.getRoot().animate({
                height: this.getItems().eq(targetIndex).outerHeight()
            }, this.getConf().speed);
        },

        // adjust item height on load
        onReload : function()
        {
            var self = this;
            var item = this.getItems().eq(0);

            // wait for the item to load first
            item.find('img').load(function()
            {
                self.getRoot().animate({
                    height: item.outerHeight()
                }, 0);
            });
        }
    }).circular().navigator();
});