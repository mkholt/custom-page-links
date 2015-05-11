<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 11-05-15
 * Time: 10:13
 */
defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );

?>
<div class="cpl-link" data-link_id="<?= $link->getId() ?>">
	<span class="cpl-media">
		<?php
		$mediaUrl = $link->getMediaUrl();
		if (!empty($mediaUrl)) {
			?>
			<img src="<?= $mediaUrl ?>" alt="" />
		<?php
		}
		?>
	</span>
	<a
		href="<?= esc_url($link->getUrl()) ?>"
		title="<?= $link->getTitle(true) ?>"
		target="<?= $link->getTarget() ?>">
		<?= $link->getTitle(true) ?>
	</a>
</div>