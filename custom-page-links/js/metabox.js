/**
 * Created by morten on 22-04-15.
 */
var cpl_meta = (function($) {
    'use strict';

    var _currentSortOrder = null;

    function getCurrentSortOrder() {
        return _currentSortOrder || [];
    }

    function setCurrentSortOrder(sortOrder) {
        if (!$.isArray(sortOrder)) {
            throw Error("Current sort order must be an array");
        }

        _currentSortOrder = sortOrder;
    }

    function _init() {
        $("body")
            .on('click', '#cpl_edit_confirm', function(e) {
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
            })
            .on('click', '#cpl_delete_confirm', function(e) {
                var $btn = $(this),
                    data = {
                        action: "cpl_remove_link",
                        post_id: $btn.data('post_id'),
                        link_id: $btn.data('link_id'),
                        confirm: true
                    };

                e.preventDefault();

                $.post(ajaxurl, data, function(returnData) {
                    self.parent.tb_remove();

                    if (returnData.status) {
                        var $link = $(".cpl-link[data-link_id='" + data.link_id + "']"),
                            $elem = $link.closest('li'),
                            count = $elem.siblings('li').length,
                            $none = $elem.siblings('.cpl-no-existing')
                        ;

                        $elem.remove();

                        if (count == 1)
                        {
                            $none.removeClass('hidden');
                        }
                    }
                    else {
                        alert(cplMetaboxLang.errorOccurredRemoving);
                    }
                });
            })
            .on('click', '#cpl_sort_confirm', function(e) {
                e.preventDefault();

                var $btn = $(this),
                    data = {
                        action: "cpl_sort_links",
                        post_id: $btn.data('post_id'),
                        links: getCurrentSortOrder()
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
            })
            .on('click', '#cpl_modal_cancel', function(e) {
                e.preventDefault();
                self.parent.tb_remove();
            })
            .on('click', '#cpl_media_pick', function(e) {
                e.preventDefault();

                var $this       = $(this),
                    $field      = $this.siblings('input')
                    ;

                var image = wp.media({
                    title: 'Choose Image',
                    multiple: false
                }).open().on('select', function(e) { pickMedia(image, $field); });
            })
            .on('click', '#cpl_href_pick_media', function(e) {
                e.preventDefault();

                var $this   = $(this),
                    $field  = $this.siblings('input'),
                    $title  = $('#cpl_title_field')
                ;

                var image = wp.media({
                    title: 'Pick media',
                    multiple: false
                }).open().on('select', function(e) { pickMedia(image, $field, $title); });
            })
            ;
    }

    function pickMedia(image, $href, $title) {
        // This will return the selected image from the Media Uploader, the result is an object
        // We convert it to JSON to make accessing easier
        var media = image.state().get('selection').first().toJSON();

        // Let's assign the url value to the input field
        $href.val(media.url);

        // If we have a title field, assign the title of the media object there
        $title && $title.val(media.title);
    }

    return  {
        init: _init,
        setCurrentSortOrder: setCurrentSortOrder,
        getCurrentSortOrder: getCurrentSortOrder
    };
})(jQuery);

jQuery(document).ready(function($) {
    'use strict';
    cpl_meta.init();
});