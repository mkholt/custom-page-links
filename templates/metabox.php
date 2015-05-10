<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 22-04-15
 * Time: 22:00
 */

defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );
?>
<p>
	<strong><?= __('Existing', $textDomain) ?></strong>
</p>
<ul id="cpl_existing">
	<?php
	if (!empty($meta)) {
		foreach ( $meta as $link ) {
			/** @var dk\mholt\CustomPageLinks\model\Link $link */
			?>
			<li>
				<?= $link->toString() ?>
				[ <a href="<?= add_query_arg(
					[
						"action"  => "cpl_edit_link",
						"post_id" => $post->ID,
						"link_id" => $link->getId()
					],
					admin_url( 'admin-ajax.php' ) ) ?>" class="thickbox"
				     title="<?= __( 'Edit link', $textDomain ) ?>"><?= __( 'Edit', $textDomain ) ?></a> ]
				[ <a href="<?= add_query_arg(
					[
						"action"  => "cpl_remove_link",
						"post_id" => $post->ID,
						"link_id" => $link->getId()
					],
					admin_url( 'admin-ajax.php' ) ) ?>" class="thickbox"
				     title="<?= __( 'Delete link', $textDomain ) ?>"><?= __( 'Delete', $textDomain ) ?></a> ]
			</li>
		<?php
		}
	}
	?>
	<li class="cpl-no-existing <?= (!empty($meta)) ? 'hidden' : '' ?>">
		<em><?= __( 'No existing links', $textDomain ) ?></em>
	</li>
</ul>

<div class="cpl_edit_form">
	<?= \dk\mholt\CustomPageLinks\admin\Metabox::editForm('new') ?>
	<div class="clear"></div>
	<?= get_submit_button(__('Add', $textDomain), ['secondary', 'large'], 'cpl_new_link', true, [
		'data-post_id' => $post->ID
	]) ?>
</div>
