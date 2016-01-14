/** CustomPageLinks meta
 * Author: Dake Sattler
 * Created: 2015-09-26
 * Since: 1.1
 * Depends: [ 'jquery' ]
 */

/**
 * Script is courtesy of Dake Sattler
 * http://wordpress.stackexchange.com/a/135843
 */

var _link_sideload = false; //used to track whether or not the link dialogue actually existed on this page, ie was wp_editor invoked.

var link_btn = (function($){
    'use strict';
    var _link_sideload = false; //used to track whether or not the link dialogue actually existed on this page, ie was wp_editor invoked.

    var _link_val_container_id = '#cpl_href_field';
    var _link_title_container_id = '#cpl_title_field';
    var _link_target_container_id = '#cpl_target_field';

    /* PRIVATE METHODS
     -------------------------------------------------------------- */
    //add event listeners
    function _init() {
        $('body').on('click', '#cpl_href_pick', function(event) {
            _addLinkListeners();
            _link_sideload = false;

            var link_val_container = $(_link_val_container_id);

            if ( typeof wpActiveEditor != 'undefined') {
                wpLink.open();
                wpLink.textarea = $(link_val_container);
            } else {
                window.wpActiveEditor = true;
                _link_sideload = true;
                wpLink.open();
                wpLink.textarea = $(link_val_container);
            }
            return false;
        });
    }

    /* LINK EDITOR EVENT HACKS
     -------------------------------------------------------------- */
    function _addLinkListeners() {
        $('body')
            .on('click', '#wp-link-submit', function(event) {
                var link_val_container = $(_link_val_container_id),
                    link_title_container = $(_link_title_container_id),
                    link_target_container = $(_link_target_container_id),
                    linkAtts = wpLink.getAttrs();

                link_val_container.val(linkAtts.href);
                link_title_container.val(linkAtts.title);
                if (linkAtts.target != "") {
                    link_target_container.val(linkAtts.target);
                }

                _removeLinkListeners();
                return false;
            })
            .on('click', '#wp-link-cancel', function(event) {
                _removeLinkListeners();
                return false;
            })
        ;
    }

    function _removeLinkListeners() {
        if(_link_sideload){
            if ( typeof wpActiveEditor != 'undefined') {
                wpActiveEditor = undefined;
            }
        }

        wpLink.close();
        wpLink.textarea = $('html');//focus on document

        $('body').off('click', '#wp-link-submit')
            .off('click', '#wp-link-cancel');
    }

    /* PUBLIC ACCESSOR METHODS
     -------------------------------------------------------------- */
    return {
        init: _init
    };

})(jQuery);


// Initialise
jQuery(document).ready(function($){
    'use strict';
    link_btn.init();
});