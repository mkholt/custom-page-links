<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 25-04-15
 * Time: 21:47
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="cpl_modal">
	<p>
		<?php
		printf(__('Are you sure you want to remove the link to %s?'), $link->getUrl());
		?>
		<br/>
		<?= __('The action cannot be undone.') ?>
	</p>
	<div class="cpl_footer">
		<?= get_submit_button(__('Delete', $textDomain), 'delete', 'cpl_delete_confirm', false, [
			'data-post_id' => $postId,
			'data-link_id' => $link->getId()
		]) ?>
		<?= get_submit_button(__('Cancel', $textDomain), 'secondary', 'cpl_delete_cancel', false) ?>
	</div>
</div>