/**
 * Created by morten on 22-04-15.
 */
var cpl_meta = (function($) {
    $(function() {
        $("body")
            .on('click', '#cpl_new_link', function(e) {
                var $btn = $(this),
                    data = {
                        action: "cpl_new_link",
                        id: $btn.data('id'),
                        href: $("#cpl_href").val(),
                        title: $("#cpl_title").val(),
                        target: $("#cpl_target").val()
                    };

                e.preventDefault();

                if (!data.id)
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

                $.post(ajax_object.ajax_url, data, function(data) {
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

                $.post(ajax_object.ajax_url, data, function(data) {
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