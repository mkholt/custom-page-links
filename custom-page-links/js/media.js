/** CustomPageLinks meta
 * Author: Morten Holt
 * Created: 2016-01-10
 * Since: 1.2
 * Depends: [ 'jquery' ]
 */

/**
 * Created by morten on 10-01-16.
 */

var cpl_media = (function($) {
    'use strict';

    var pick = function(e) {
        e.preventDefault();

        var $this       = $(this),
            $field      = $this.siblings('input')
            ;

        var image = wp.media({
            title: 'Choose Image',
            multiple: false
        }).open().on('select', function(e) { pickMedia(image, $field); });
    };

    var pickHref = function(e) {
        e.preventDefault();

        var $this   = $(this),
            $field  = $this.siblings('input'),
            $title  = $('#cpl_title_field')
            ;

        var image = wp.media({
            title: 'Pick media',
            multiple: false
        }).open().on('select', function(e) { pickMedia(image, $field, $title); });
    };

    var pickMedia = function(image, $href, $title) {
        // This will return the selected image from the Media Uploader, the result is an object
        // We convert it to JSON to make accessing easier
        var media = image.state().get('selection').first().toJSON();

        // Let's assign the url value to the input field
        $href.val(media.url);

        // If we have a title field, assign the title of the media object there
        $title && $title.val(media.title);
    };

    return {
        'pick': pick,
        'pickHref': pickHref
    }
})(jQuery);