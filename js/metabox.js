/**
 * Created by morten on 22-04-15.
 */
var cpl_meta = (function($) {
    $(function() {
        $("body")
            .on('click', '#cpl_new_link, #cpl_edit_confirm', function(e) {
                var $btn = $(this),
                    $wrapper = $btn.closest('.cpl_edit_form'),
                    data = {
                        action: $btn.attr('id'),
                        post_id: $btn.data('post_id'),
                        link_id: $btn.data('link_id'),
                        href: $wrapper.find("input[name=cpl_href]").val(),
                        title: $wrapper.find("input[name=cpl_title]").val(),
                        target: $wrapper.find("select[name=cpl_target]").val()
                    };

                e.preventDefault();

                if (!data.post_id)
                {
                    return;
                }

                if (!data.href)
                {
                    // TODO : Handle mising href
                    alert('href');
                    return;
                }

                if (!data.title)
                {
                    // TODO : Handle missing title
                    alert('title');
                    return;
                }

                $.post(ajaxurl, data, function(data) {
                    alert(data);
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

                $.post(ajaxurl, data, function(data) {
                    alert(data);
                    self.parent.tb_remove();
                });
            })
            .on('click', '#cpl_modal_cancel', function(e) {
                e.preventDefault();
                self.parent.tb_remove();
            })
            ;
    });
})(jQuery);