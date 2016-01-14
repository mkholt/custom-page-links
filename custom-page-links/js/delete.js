/** CustomPageLinks meta
 * Author: Morten Holt
 * Created: 2016-01-10
 * Since: 1.2
 * Depends: [ 'jquery' ]
 */

/**
 * Created by morten on 10-01-16.
 */
var cpl_delete = (function($) {
    'use strict';

    var confirm = function(e) {
        var $btn = $(this),
            data = {
                action: "cpl_remove_link",
                post_id: $btn.data('post_id'),
                link_id: $btn.data('link_id'),
                confirm: true
            };

        e.preventDefault();

        $.post(ajaxurl, data, function (returnData) {
            self.parent.tb_remove();

            if (returnData.status) {
                var $link = $(".cpl-link[data-link_id='" + data.link_id + "']"),
                    $elem = $link.closest('li'),
                    count = $elem.siblings('li').length,
                    $none = $elem.siblings('.cpl-no-existing')
                    ;

                $elem.remove();

                if (count == 1) {
                    $none.removeClass('hidden');
                }
            }
            else {
                alert(cplMetaboxLang.errorOccurredRemoving);
            }
        });
    };

    return {
        'confirm': confirm
    }
})(jQuery);