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
use dk\mholt\CustomPageLinks\model\Post;
use dk\mholt\CustomPageLinks\ViewController;

class Metabox {
	protected static $className;

	public static function __init__() {
		self::$className = __NAMESPACE__ . '\Metabox';
	}

	/**
	 * Initialize the Metabox.
	 * Initialization is only done if the user is currently logged into the administration panel.
	 *
	 * @return bool true, if Metabox was initialized, false otherwise.
	 */
	public static function init() {
		if (!is_admin()) {
			return false;
		}

		self::addAction();

		return true;
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
		add_action( 'wp_ajax_cpl_sort_links', [ self::$className, 'sortLinks' ] );
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
			[ 'jquery' ], CustomPageLinks::CURRENT_VERSION, true );

		wp_enqueue_script( 'cpl-link_btn',
			plugins_url( '../js/link.js', __FILE__ ),
			[ 'jquery' ], CustomPageLinks::CURRENT_VERSION, true );

		wp_enqueue_script( 'cpl-polyfill',
			plugins_url( '../js/polyfill.js', __FILE__ ),
			[ 'jquery' ], CustomPageLinks::CURRENT_VERSION, true );

		wp_localize_script( 'cpl-metabox', 'cplMetaboxLang', [
			'missingPostId'         => __( 'Missing post ID, please try to reload the page.', CustomPageLinks::TEXT_DOMAIN ),
			'hrefRequired'          => __( 'You must enter a URL', CustomPageLinks::TEXT_DOMAIN ),
			'titleRequired'         => __( 'You must enter a title', CustomPageLinks::TEXT_DOMAIN ),
			'errorOccurredAdding'   => __( 'An error occured adding the link', CustomPageLinks::TEXT_DOMAIN ),
			'errorOccurredRemoving' => __( 'An error occurred removing the link', CustomPageLinks::TEXT_DOMAIN ),
			'errorOccurredSorting'  => __( 'An error occurred sorting the links', CustomPageLinks::TEXT_DOMAIN )
		]);
	}

	public static function addMetaBox(\WP_Post $wpPost) {
		add_thickbox();

		$post = Post::createFromPost($wpPost);

		ViewController::loadView( 'metabox',
		[
			'post'       => $wpPost,
			'meta'       => $post->getLinks(),
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

		$post   = new Post( $postId );
		$link   = $post->getLink( $linkId );

		if ( ! empty( $_REQUEST['confirm'] ) ) {
			ViewController::sendJson( [ "status" => $post->removeLink( $link ) ] );
		}

		ViewController::loadView( 'remove',
			[
				'postId'     => $postId,
				'link'       => $link,
				'textDomain' => CustomPageLinks::TEXT_DOMAIN
			] );
		wp_die();
	}

	public static function editLink() {
		self::checkAccess();

		$postId = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : null;
		$linkId = isset( $_REQUEST['link_id'] ) ? $_REQUEST['link_id'] : null;

		if ( $postId != null && $linkId != null ) {
			$post = new Post( $postId );
			$link = $post->getLink( $linkId );
		} else {
			$link = null;
		}

		ViewController::loadView( 'edit',
			[
				'postId'     => $postId,
				'link'       => $link,
				'textDomain' => CustomPageLinks::TEXT_DOMAIN
			] );

		wp_die();
	}

	public static function updateLink() {
		self::checkAccess();

		$postId = $_REQUEST['post_id'];

		if ( empty( $postId ) ) {
			ViewController::sendJson( [ "status" => false ] );
			wp_die();
		}

		$post = new Post( $postId );

		if ( ! empty( $_REQUEST['link_id'] ) ) {
			$linkId = $_REQUEST['link_id'];
			$link   = $post->getLink( $linkId );
		} else {
			$link = new Link();
			$link->setPostId( $post->getPostId() );
		}

		$link->setUrl( $_REQUEST['href'] );
		$link->setTitle( $_REQUEST['title'] );
		$link->setMediaUrl( $_REQUEST['media'] );
		$link->setTarget( $_REQUEST['target'] );

		ViewController::sendJson( [ "status" => $post->addLink( $link ), "link" => $link ] );
	}

	public static function sortLinks() {
		self::checkAccess();

		$post = new Post( $_REQUEST['post_id'] );

		if ( ! empty( $_REQUEST['links'] ) ) {
			$actions = [];
			$links = $post->sortLinks($_REQUEST['links']);
			foreach ($links as $link) {
				$actions[$link->getId()] = self::linkActions( $post->getPostId(), $link->getId() );
			}

			ViewController::sendJson([
				"status" => true,
				"links" => $links,
				"actions" => $actions
			]);
			wp_die();
		}

		ViewController::loadView( 'sort',
			[
				'post'  => $post,
				'links' => $post->getLinks()
			] );

		wp_die();
	}
} 