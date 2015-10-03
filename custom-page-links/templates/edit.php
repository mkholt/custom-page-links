<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 28-04-15
 * Time: 21:22
 */

defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );
?>

<div class="cpl_modal cpl_edit_form">
	<p>
		<?php _e(sprintf('Edit the link by updating the fields and cliking the button labelled "%s".', __('Save', $textDomain)), $textDomain) ?>
	</p>
	<div>
		<label>
			<span><?php _e( 'URL', $textDomain ) ?></span>
			<input type="text" id="cpl_href_field" name="cpl_href" value="<?php echo (!empty($link)) ? $link->getUrl() : '' ?>" />
			<a href="#" id="cpl_href_pick" title="<?php _e( 'Pick page', $textDomain ) ?>"><span class="dashicons dashicons-admin-page"></span></a>
			<a href="#" id="cpl_href_pick_media" title="<?php _e('Pick media', $textDomain ) ?>"><span class="dashicons dashicons-admin-media"></span></a>
		</label>
	</div>

	<div>
		<label>
			<span><?php _e( 'Title', $textDomain ) ?></span>
			<input type="text" id="cpl_title_field" name="cpl_title" value="<?php echo (!empty($link)) ? $link->getTitle() : '' ?>" />
		</label>
	</div>

	<div>
		<label>
			<span><?php _e( 'Target', $textDomain ) ?></span>
			<select id="cpl_target_field" name="cpl_target">
				<?php
				foreach (\dk\mholt\CustomPageLinks\model\Link::getTargets() as $target)
				{
					?>
					<option value="<?php echo $target ?>" <?php echo (!empty($link) && $link->getTarget() == $target) ? 'selected' : '' ?>><?php echo $target ?></option>
				<?php
				}
				?>
			</select>
		</label>
	</div>

	<div>
		<label>
			<span><?php _e( 'Image', $textDomain ) ?></span>
			<input type="text" id="cpl_media_field" name="cpl_media" value="<?php echo (!empty($link)) ? $link->getMediaUrl() : '' ?>" />
			<a href="#" id="cpl_media_pick" title="<?php _e( 'Choose image', $textDomain ) ?>">
				<span class="dashicons dashicons-format-image"></span>
			</a>
		</label>
	</div>
	<p class="howto"><?php _e('Choose a relevant image, should be no larger than 32x32px', $textDomain) ?></p>

	<div class="clear">&nbsp;</div>

	<div class="cpl_footer">
		<?php submit_button(__('Save', $textDomain), ['primary'], 'cpl_edit_confirm', false, [
			'data-post_id' => $postId,
			'data-link_id' => !empty($link) ? $link->getId() : null
		]) ?>
		<?php submit_button(__('Cancel', $textDomain), 'secondary', 'cpl_modal_cancel', false) ?>
	</div>
</div>