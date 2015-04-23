/**
 * Created by morten on 22-04-15.
 */
var cpl_meta = (function($) {
    $(function() {
        $("#cpl_new_link").on('click', function(e) {
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
        });

        $("input[type=submit]", "#cpl_existing").on('click', function(e) {
            var $btn = $(this),
                data = {
                    action: "cpl_remove_link",
                    post_id: $btn.data('post_id'),
                    link_id: $btn.data('link_id')
                };

            e.preventDefault();

            $.post(ajax_object.ajax_url, data, function(data) {
                alert(data);
            });
        });
    });
})(jQuery);