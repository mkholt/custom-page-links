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
	<strong><?php _e('Existing', $textDomain) ?></strong>
</p>
<ul id="cpl_existing">
	<?php
	if (!empty($meta)) {
		foreach ( $meta as $link ) {
			/** @var dk\mholt\CustomPageLinks\model\Link $link */
			?>
			<li>
				<?php echo $link->toString() ?>
				<?php echo \dk\mholt\CustomPageLinks\admin\Metabox::linkActions($post->ID, $link->getId()) ?>
			</li>
		<?php
		}
	}
	?>
	<li class="cpl-no-existing <?php echo (!empty($meta)) ? 'hidden' : '' ?>">
		<em><?php _e( 'No existing links', $textDomain ) ?></em>
	</li>
	<li class="cpl-add-link">
		<div class="cpl-link"></div>
		<div class="cpl-link-actions">
			<a href="<?php echo $addLink ?>" class="thickbox" title="<?php _e( 'Add link', $textDomain ) ?>">
				<span class="dashicons dashicons-plus"></span>
			</a>
		</div>
	</li>
</ul>
