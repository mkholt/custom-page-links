/** CustomPageLinks meta
 * Author: Morten Holt
 * Created: 2015-04-22
 * Since: 1.0
 * Depends: [ 'jquery', 'jquery-ui-sortable' ]
 */
var cpl_meta = (function($) {
    'use strict';

    function _cancelModal(e) {
        e.preventDefault();
        self.parent.tb_remove();
    }

    function _init() {
        $("body")
            .on('click', '#cpl_edit_confirm', cpl_edit.confirm)
            .on('click', '#cpl_delete_confirm', cpl_delete.confirm)
            .on('click', '#cpl_sort_confirm', cpl_sort.confirm)
            .on('click', '#cpl_modal_cancel', _cancelModal)
            .on('click', '#cpl_media_pick', cpl_media.pick)
            .on('click', '#cpl_href_pick_media', cpl_media.pickHref)
            ;
    }

    return  {
        init: _init
    };
})(jQuery);

jQuery(document).ready(function($) {
    'use strict';
    cpl_meta.init();
});