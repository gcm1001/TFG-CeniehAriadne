if (!ThanksRoy) {
    var ThanksRoy = {};
}

(function ($) {

    ThanksRoy.mobileMenu = function() {
        $('.supports.touchevents #primary-nav li ul').each(function() {
            var childMenu = $(this);
            var subnavToggle = $('<button type="button" class="sub-nav-toggle" aria-label="Show subnavigation"></button>');
            subnavToggle.click(function() {
                $(this).parent('.parent').toggleClass('open');
            });
            childMenu.parent().addClass('parent');
            childMenu.addClass('sub-nav').before(subnavToggle);
        });

        $('.menu-button').click( function(e) {
            e.preventDefault();
            $('#primary-nav ul.navigation').toggle();
        });
    };

})(jQuery);
