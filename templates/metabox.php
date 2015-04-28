<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 22-04-15
 * Time: 22:00
 */

defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );
?>
<?php
if (!empty($meta))
{
?>
	<p>
		<strong><?= __('Existing', $textDomain) ?></strong>
	</p>
	<ul id="cpl_existing">
		<?php
		foreach ($meta as $link)
		{
			/** @var dk\mholt\CustomPageLinks\model\Link $link */
			?>
			<li>
				<?= $link->toString() ?>
				[ <a href="<?= add_query_arg(
					[
						"action" => "cpl_edit_link",
						"post_id" => $post->ID,
						"link_id" => $link->getId()
					], admin_url('admin-ajax.php')) ?>" class="thickbox"
					title="<?= __('Edit link', $textDomain) ?>"><?= __('Edit', $textDomain) ?></a> ]
				[ <a href="<?= add_query_arg(
					[
						"action" => "cpl_remove_link",
						"post_id" => $post->ID,
						"link_id" => $link->getId()
					], admin_url('admin-ajax.php')) ?>" class="thickbox"
					title="<?= __('Delete link', $textDomain) ?>"><?= __('Delete', $textDomain) ?></a> ]
			</li>
			<?php
		}
		?>
	</ul>
<?php
}
?>

<p>
	<strong><?= __('URL', $textDomain) ?></strong>
</p>
<label for="cpl_href" class="screen-reader-text"><?= __('URL', $textDomain) ?></label>
<input type="text" id="cpl_href" />
<p>
	<strong><?= __('Title', $textDomain) ?></strong>
</p>
<label for="cpl_title" class="screen-reader-text"><?= __('Title', $textDomain) ?></label>
<input type="text" id="cpl_title" />
<p>
	<strong><?= __('Target', $textDomain) ?></strong>
</p>
<label for="cpl_target" class="screen-reader-text"><?= __('Target', $textDomain) ?></label>
<select id="cpl_target">
	<?php
	foreach (\dk\mholt\CustomPageLinks\model\Link::getTargets() as $target)
	{
		?>
		<option value="<?= $target ?>"><?= $target ?></option>
		<?php
	}
	?>
</select>
<div class="clear"></div>
<?= get_submit_button(__('Add', $textDomain), ['secondary', 'large'], 'cpl_new_link', true, [
	'data-id' => $post->ID
]) ?>
