<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 22-04-15
 * Time: 22:00
 */

defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );

$adminUrl = admin_url( 'admin-ajax.php' );
$addArgs = [
	"action"  => "cpl_edit_link",
	"post_id" => $post->ID
];
$addLink = add_query_arg($addArgs, $adminUrl);

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
				<?= \dk\mholt\CustomPageLinks\admin\Metabox::linkActions($post->ID, $link->getId()) ?>
			</li>
		<?php
		}
	}
	?>
	<li class="cpl-no-existing <?= (!empty($meta)) ? 'hidden' : '' ?>">
		<em><?= __( 'No existing links', $textDomain ) ?></em>
	</li>
	<li class="cpl-add-link">
		<div class="cpl-link"></div>
		<div class="cpl-link-actions">
			<a href="<?= $addLink ?>" class="thickbox" title="<?= __( 'Add link', $textDomain ) ?>">
				<span class="dashicons dashicons-plus"></span>
			</a>
		</div>
	</li>
</ul>
