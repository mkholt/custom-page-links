<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 22-04-15
 * Time: 22:51
 */

namespace dk\mholt\CustomPageLinks\admin;

use dk\mholt\CustomPageLinks\CustomPageLinks;
use dk\mholt\CustomPageLinks\model\Link;
use dk\mholt\CustomPageLinks\Storage;
use dk\mholt\CustomPageLinks\ViewController;

class Metabox {
	protected static $className;

	public static function __init__() {
		self::$className = __NAMESPACE__ . '\Metabox';
	}

	public static function init() {
		self::addAction();
	}

	public static function addAction() {
		$func = function() {
			add_action( 'add_meta_boxes', [ self::$className, 'addMetaBoxes' ] );
		};
		add_action('load-page.php', $func);
		add_action('load-page-new.php', $func);


		add_action( 'wp_ajax_cpl_remove_link', [ self::$className, 'removeLink' ] );
		add_action( 'wp_ajax_cpl_edit_link', [ self::$className, 'editLink' ] );
		add_action( 'wp_ajax_cpl_edit_confirm', [ self::$className, 'updateLink' ] );
		add_action( 'wp_ajax_cpl_link_actions', [ self::$className, 'getLinkActions' ] );
		add_action( 'admin_enqueue_scripts', [ self::$className, 'addScripts' ] );
	}

	public function addMetaBoxes($type) {
		add_meta_box( 'custom-page-links',
			__( 'Custom Page Links', CustomPageLinks::TEXT_DOMAIN ),
			[ self::$className, 'addMetaBox' ],
			'page',
			'side' );
	}

	public static function addScripts($hook) {
		if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ] ) ) {
			return;
		}

		wp_enqueue_script( 'wp-link' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_media();
		wp_enqueue_script( 'cpl-metabox',
			plugins_url( '../js/metabox.js', __FILE__ ),
			[ 'jquery' ] );
		wp_enqueue_script( 'cpl-link_btn',
			plugins_url( '../js/link.js', __FILE__ ),
			[ 'jquery' ], '1.0', true);

		wp_localize_script( 'cpl-metabox', 'cplMetaboxLang', [
			'missingPostId'         => __( 'Missing post ID, please try to reload the page.', CustomPageLinks::TEXT_DOMAIN ),
			'hrefRequired'          => __( 'You must enter a URL', CustomPageLinks::TEXT_DOMAIN ),
			'titleRequired'         => __( 'You must enter a title', CustomPageLinks::TEXT_DOMAIN ),
			'errorOccurredAdding'   => __( 'An error occured adding the link', CustomPageLinks::TEXT_DOMAIN ),
			'errorOccurredRemoving' => __( 'An error occurred removing the link', CustomPageLinks::TEXT_DOMAIN )
		]);
	}

	public static function addMetaBox(\WP_Post $post) {
		add_thickbox();

		ViewController::loadView( 'metabox',
		[
			'post'       => $post,
			'meta'       => Storage::getLinks( $post->ID ),
			'textDomain' => CustomPageLinks::TEXT_DOMAIN
		] );
	}

	public static function getLinkActions()
	{
		self::checkAccess();

		$postId = $_REQUEST['post_id'];
		$linkId = $_REQUEST['link_id'];

		echo self::linkActions($postId, $linkId);
		wp_die();
	}

	public static function linkActions($postId, $linkId) {
		$args = [
			"textDomain" => CustomPageLinks::TEXT_DOMAIN,
			"postId"     => $postId,
			"linkId"     => $linkId
		];

		return ViewController::loadView( 'linkActions', $args, false );
	}

	private static function checkAccess() {
		current_user_can( 'edit_others_pages' ) || wp_die();
	}

	public static function removeLink() {
		self::checkAccess();

		$postId = $_REQUEST['post_id'];
		$linkId = $_REQUEST['link_id'];

		if ( ! empty( $_REQUEST['confirm'] ) ) {
			ViewController::sendJson( [ "status" => Storage::removeLink( $postId, $linkId ) ] );
		}

		ViewController::loadView( 'remove',
		[
			'postId'     => $postId,
			'link'       => Storage::getLink( $postId, $linkId ),
			'textDomain' => CustomPageLinks::TEXT_DOMAIN
		] );
		wp_die();
	}

	public static function editLink() {
		self::checkAccess();

		$postId = isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : null;
		$linkId = isset($_REQUEST['link_id']) ? $_REQUEST['link_id'] : null;

		ViewController::loadView( 'edit',
		[
			'postId'     => $postId,
			'link'       => ($postId != null && $linkId != null) ? Storage::getLink( $postId, $linkId ) : null,
			'textDomain' => CustomPageLinks::TEXT_DOMAIN
		] );

		wp_die();
	}

	public static function updateLink() {
		self::checkAccess();

		$postId = $_REQUEST['post_id'];

		if (!empty($_REQUEST['link_id'])) {
			$linkId = $_REQUEST['link_id'];
			$link   = Storage::getLink( $postId, $linkId );
		}
		else {
			$link   = new Link();
		}

		$link->setUrl( $_REQUEST['href'] );
		$link->setTitle( $_REQUEST['title'] );
		$link->setMediaUrl( $_REQUEST['media'] );
		$link->setTarget( $_REQUEST['target'] );

		ViewController::sendJson( [ "status" => Storage::addLink( $postId, $link ), "link" => $link ] );
	}
} 