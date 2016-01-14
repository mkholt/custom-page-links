<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 28-11-15
 * Time: 21:24
 */

defined( 'CPL_VIEW' ) or die( 'Please load this view through the ViewController' );

/**
 * @var dk\mholt\CustomPageLinks\model\Link[] $links
 * @var dk\mholt\CustomPageLinks\model\Post $post
 */
?>

<div class="cpl_modal cpl_sort_form">
	<p>
		<?php printf(__('To sort the links on the current page, drag-and-drop them below to their new positions. When finished, click the button labelled "%s".', $textDomain), __('Save', $textDomain)); ?>
	</p>
	<div class="widgets-holder-wrap">
		<div class="ui-sortable">
			<div class="sidebar-name">
				<h3><?php _e('Links', $textDomain) ?></h3>
			</div>
			<?php
			foreach ($links as $link) {
				?>
				<div id="<?php echo $link->getId() ?>" class="widget cpl-link">
					<div class="widget-top">
						<div class="widget-title">
							<h4>
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
								<span class="in-widget-title"><?php echo $link->getTitle() ?></span>
							</h4>
						</div>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	</div>
	<div class="cpl_footer">
		<?php submit_button(__('Save', $textDomain), 'primary', 'cpl_sort_confirm', false, [
			'data-post_id' => $post->getPostId()
		]) ?>
		<?php submit_button(__('Cancel', $textDomain), 'secondary', 'cpl_modal_cancel', false) ?>
	</div>
	<script>
		(function($) {
			var setSortOrder = function($form) {
				var sortOrder = $form.sortable("toArray");
				cpl_sort.setCurrentSortOrder(sortOrder);
			};

			var $cplSortForm = $(".cpl_sort_form .ui-sortable");
			$cplSortForm
				.sortable({
					items: '.cpl-link',
					axis: 'y',
					update: function() {
						setSortOrder($(this));
					}
				});

			setSortOrder($cplSortForm);
		})(jQuery);
	</script>
</div>