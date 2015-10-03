<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 11-05-15
 * Time: 10:13
 */
defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );

$adminUrl = admin_url( 'admin-ajax.php' );
$args = [
	'action' => dk\mholt\CustomPageLinks\Landing::LANDING_ACTION,
	'post' => !empty($postId) ? $postId : get_the_ID(),
	'link' => $link->getId()
];
$href = add_query_arg( $args, $adminUrl );
?>
<div class="cpl-link" data-link_id="<?php echo $link->getId() ?>">
	<span class="cpl-media">
		<?php
		$mediaUrl = $link->getMediaUrl();
		if (!empty($mediaUrl)) {
			?>
			<img src="<?php echo $mediaUrl ?>" alt="" />
		<?php
		}
		?>
	</span>
	<a
		href="<?php echo esc_url( $href ) ?>"
		title="<?php echo $link->getTitle( true ) ?>"
		target="<?php echo $link->getTarget() ?>">
		<?php echo $link->getTitle( true ) ?>
	</a>
</div>