<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 25-04-15
 * Time: 21:47
 */

defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );
?>

<div class="cpl_modal">
	<p>
		<?php
		printf(__('Are you sure you want to remove the link to <strong>%s</strong>?'), esc_url($link->getUrl()));
		?>
		<br/>
		<?= __('The action <strong>cannot</strong> be undone.') ?>
	</p>
	<div class="cpl_footer">
		<a href="#" id="cpl_delete_confirm" data-post_id="<?= $postId ?>" data-link_id="<?= $link->getId() ?>">
			<?= __( 'Delete', $textDomain ) ?>
		</a>
		<?= get_submit_button(__('Cancel', $textDomain), 'secondary', 'cpl_modal_cancel', false) ?>
	</div>
</div>