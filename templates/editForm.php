<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 28-04-15
 * Time: 21:29
 */
defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );
?>
<p>
	<strong><?= __('URL', $textDomain) ?></strong>
</p>
<label for="cpl_<?= $prefix ?>_href" class="screen-reader-text"><?= __('URL', $textDomain) ?></label>
<input type="text" id="cpl_<?= $prefix ?>_href" name="cpl_href" value="<?= (!empty($link)) ? $link->getUrl() : '' ?>" />
<p>
	<strong><?= __('Title', $textDomain) ?></strong>
</p>
<label for="cpl_<?= $prefix ?>_title" class="screen-reader-text"><?= __('Title', $textDomain) ?></label>
<input type="text" id="cpl_<?= $prefix ?>_title" name="cpl_title" value="<?= (!empty($link)) ? $link->getTitle() : '' ?>" />
<p>
	<strong><?= __('Image', $textDomain) ?></strong>
</p>
<label for="cpl_<?= $prefix ?>_media" class="screen-reader-text"><?= __('Image', $textDomain) ?></label>
<input type="text" id="cpl_<?= $prefix ?>_media" name="cpl_media" value="<?= (!empty($link)) ? $link->getMediaUrl() : '' ?>" />
<input type="button" id="cpl_<?= $prefix ?>_media_btn" class="button-secondary cpl-media-btn" value="<?= __('Choose Image', $textDomain) ?>" />
<span class="description"><?= __('Choose a relevant image, should be no larger than 15x15px', $textDomain) ?></span>
<p>
	<strong><?= __('Target', $textDomain) ?></strong>
</p>
<label for="cpl_<?= $prefix ?>_target" class="screen-reader-text"><?= __('Target', $textDomain) ?></label>
<select id="cpl_<?= $prefix ?>_target" name="cpl_target">
	<?php
	foreach (\dk\mholt\CustomPageLinks\model\Link::getTargets() as $target)
	{
		?>
		<option value="<?= $target ?>" <?= (!empty($link) && $link->getTarget() == $target) ? 'selected' : '' ?>>
			<?= $target ?>
		</option>
	<?php
	}
	?>
</select>