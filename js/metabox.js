/**
 * Created by morten on 22-04-15.
 */
var cpl_meta = (function($) {
    $(function() {
        $("body")
            .on('click', '#cpl_new_link, #cpl_edit_confirm', function(e) {
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
                    // TODO : I18n support for messages in JS
                    alert('Missing post ID, please try to reload the page.');
                    return;
                }

                if (!data.href)
                {
                    alert('You must enter a URL');
                    return;
                }

                if (!data.title)
                {
                    alert('You must enter a title');
                    return;
                }

                $.post(ajaxurl, data, function(returnData) {
                    if (!returnData.status) {
                        alert('An error occured adding the link');
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

                    if (data.link_id)
                    {
                        self.parent.tb_remove();
                    }
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
                        alert('An error occurred removing the link');
                    }
                });
            })
            .on('click', '#cpl_modal_cancel', function(e) {
                e.preventDefault();
                self.parent.tb_remove();
            })
            .on('click', '.cpl-media-btn', function(e) {
                e.preventDefault();

                var $this       = $(this),
                    $wrapper    = $this.closest('.cpl_edit_form'),
                    $field      = $wrapper.find('[name=cpl_media]');

                var image = wp.media({
                    title: 'Choose Image',
                    multiple: false
                }).open()
                    .on('select', function (e) {
                        // This will return the selected image from the Media Uploader, the result is an object
                        var uploaded_image = image.state().get('selection').first();

                        // We convert uploaded_image to a JSON object to make accessing it easier
                        var image_url = uploaded_image.toJSON().url;

                        // Let's assign the url value to the input field
                        $field.val(image_url);
                    });
            })
            ;
    });
})(jQuery);