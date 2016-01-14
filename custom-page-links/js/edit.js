/** CustomPageLinks meta
 * Author: Morten Holt
 * Created: 2015-01-16
 * Since: 1.2
 * Depends: [ 'jquery' ]
 */

/**
 * Created by morten on 10-01-16.
 */
var cpl_edit = (function($) {
    'use strict';

    var confirm = function(e) {
        e.preventDefault();

        var $btn = $(this),
            $wrapper = $btn.closest('.cpl_edit_form'),
            $href = $wrapper.find("input[name=cpl_href]"),
            $title = $wrapper.find("input[name=cpl_title]"),
            $media = $wrapper.find("input[name=cpl_media]"),
            $target = $wrapper.find("select[name=cpl_target]"),
            data = {
                action: $btn.attr('id'),
                post_id: $btn.data('post_id'),
                link_id: $btn.data('link_id'),
                href: $href.val(),
                title: $title.val(),
                media: $media.val(),
                target: $target.val()
            };

        if (!data.post_id)
        {
            alert(cplMetaboxLang.missingPostId);
            return;
        }

        if (!data.href)
        {
            alert(cplMetaboxLang.hrefRequired);
            return;
        }

        if (!data.title)
        {
            alert(cplMetaboxLang.titleRequired);
            return;
        }

        $.post(ajaxurl, data, function(returnData) {
            if (!returnData.status) {
                alert(cplMetaboxLang.errorOccurredAdding);
                return;
            }

            var $wrapper = $("#cpl_existing"),
                $empty = $wrapper.find(".cpl-no-existing"),
                $link = $wrapper.find(".cpl-link[data-link_id='" + returnData.link.id + "']"),
                $elem = $link.closest('li')
                ;

            $.get(ajaxurl, {
                'action': 'cpl_link_actions',
                'post_id': data.post_id,
                'link_id': returnData.link.id
            }, function(e) {
                var append = false;
                if (!$elem.length) {
                    $elem = $("<li>");
                    append = true;
                }

                $elem.html(returnData.link.html).append(e);

                if (append) {
                    $elem.insertBefore($empty);
                    $empty.addClass('hidden');
                }

                $href.val('');
                $title.val('');
                $media.val('');
                $target.val($target.find('option:first').attr('value'));
            });

            self.parent.tb_remove();
        });
    };

    return {
        'confirm': confirm
    }
})(jQuery);