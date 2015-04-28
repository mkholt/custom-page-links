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
<input type="text" id="cpl_<?= $prefix ?>_href" value="<?= (!empty($link)) ? $link->getUrl() : '' ?>" />
<p>
	<strong><?= __('Title', $textDomain) ?></strong>
</p>
<label for="cpl_<?= $prefix ?>_title" class="screen-reader-text"><?= __('Title', $textDomain) ?></label>
<input type="text" id="cpl_<?= $prefix ?>_title" valuE="<?= (!empty($link)) ? $link->getTitle() : '' ?>" />
<p>
	<strong><?= __('Target', $textDomain) ?></strong>
</p>
<label for="cpl_<?= $prefix ?>_target" class="screen-reader-text"><?= __('Target', $textDomain) ?></label>
<select id="cpl_<?= $prefix ?>_target">
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