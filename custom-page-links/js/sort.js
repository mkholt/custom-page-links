/** CustomPageLinks meta
 * Author: Morten Holt
 * Created: 2016-01-10
 * Since: 1.2
 * Depends: [ 'jquery' ]
 */

/**
 * Created by morten on 10-01-16.
 */
var cpl_sort = (function($) {
    'use strict';

    var _currentSortOrder = null;

    var confirm = function(e) {
        e.preventDefault();

        var $btn = $(this),
            data = {
                action: "cpl_sort_links",
                post_id: $btn.data('post_id'),
                links: _getCurrentSortOrder()
            };

        $.post(ajaxurl, data, function(returnData) {
            if (!returnData.status) {
                alert(cplMetaboxLang.errorOccurredSorting);
                return;
            }

            var $wrapper = $("#cpl_existing"),
                $empty = $wrapper.find(".cpl-no-existing"),
                $existing = $empty.prevUntil($wrapper)
                ;

            $existing.remove();
            Object.keys(returnData.links).forEach(function(linkId) {
                var link = returnData.links[linkId],
                    actions = returnData.actions[linkId]
                    ;

                var $link = $("<li>").append($(link.html).append(actions));
                $link.insertBefore($empty);
            });

            self.parent.tb_remove();
        });
    };

    var _getCurrentSortOrder = function() {
        return _currentSortOrder || [];
    };

    var _setCurrentSortOrder = function(sortOrder) {
        if (!$.isArray(sortOrder)) {
            throw Error("Current sort order must be an array");
        }

        _currentSortOrder = sortOrder;
    };

    return {
        'confirm': confirm,
        'setCurrentSortOrder': _setCurrentSortOrder,
        'getCurrentSortOrder': _getCurrentSortOrder
    }
})(jQuery);