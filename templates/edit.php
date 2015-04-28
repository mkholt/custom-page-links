<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 28-04-15
 * Time: 21:22
 */

defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );
?>

<div class="cpl_modal">
	<p>
		<?= __(sprintf('Edit the link by updating the fields and cliking the button labelled "%s".', __('Save', $textDomain)), $textDomain) ?>
	</p>
	<?= \dk\mholt\CustomPageLinks\admin\Metabox::editForm('edit', $postId, $link->getId()) ?>
	<div class="cpl_footer">
		<?= get_submit_button(__('Save', $textDomain), ['secondary'], 'cpl_edit_confirm', false, [
			'data-post_id' => $postId,
			'data-link_id' => $link->getId()
		]) ?>
		<?= get_submit_button(__('Cancel', $textDomain), 'secondary', 'cpl_modal_cancel', false) ?>
	</div>
</div>