<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 28-11-15
 * Time: 21:24
 */

defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );
?>

<div class="cpl_modal cpl_sort_form">
	<p>
		<?php printf(__('To sort the links on the current page, drag-and-drop them below to their new positions. When finished, click the button labelled "%s".', $textDomain), __('Save', $textDomain)); ?>
	</p>
	<ul>
		<?php
		/**
		 * @var dk\mholt\CustomPageLinks\model\Link[] $links
		 * @var dk\mholt\CustomPageLinks\model\Post $post
		 */
		foreach ($links as $link) {
			?>
			<li data-id="<?php echo $link->getId() ?>">
				<?php echo $link->getTitle() ?>
			</li>
		<?php
		}
		?>
	</ul>
	<div class="cpl_footer">
		<?php submit_button(__('Save', $textDomain), 'primary', 'cpl_sort_confirm', false, [
			'data-post_id' => $post->getPostId()
		]) ?>
		<?php submit_button(__('Cancel', $textDomain), 'secondary', 'cpl_modal_cancel', false) ?>
	</div>
</div>