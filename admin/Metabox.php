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

	public static function addAction()
	{
		add_action('wp_ajax_cpl_new_link', [ self::$className, 'addLink']);
		add_action('wp_ajax_cpl_remove_link', [__NAMESPACE__ . '\Metabox', 'removeLink']);
		add_action('wp_ajax_cpl_edit_link', [__NAMESPACE__ . '\Metabox', 'editLink']);
		add_action('wp_ajax_cpl_edit_confirm', [__NAMESPACE__ . '\Metabox', 'doEditLink']);
	}

	public static function addMetaBox(\WP_Post $post)
	{
		add_thickbox();

		ViewController::loadView('metabox', [
			'post' => $post,
			'meta' => Storage::getLinks($post->ID),
			'textDomain' => CustomPageLinks::TEXT_DOMAIN
		]);
	}

	public static function editForm($prefix, $postId = null, $linkId = null)
	{
		$args = [
			'textDomain' => CustomPageLinks::TEXT_DOMAIN,
			'prefix' => $prefix
		];

		if (!empty($postId) && !empty($linkId))
		{
			$args['postId'] = $postId;
			$args['linkId'] = $linkId;
			$args['link'] = Storage::getLink($postId, $linkId);
		}
		elseif (!empty($postId))
		{
			$args['postId'] = $postId;
			$args['links'] = Storage::getLinks($postId);
		}

		return ViewController::loadView('editForm', $args, false);
	}

	private static function checkAccess()
	{
		current_user_can('edit_others_pages') || wp_die();
	}

	public static function addLink()
	{
		self::checkAccess();

		$link = new Link();
		$link->setUrl($_REQUEST['href']);
		$link->setTitle($_REQUEST['title']);
		$link->setTarget($_REQUEST['target']);

		ViewController::sendJson(["status" => Storage::addLink($_REQUEST['post_id'], $link)]);
	}

	public static function removeLink()
	{
		self::checkAccess();

		$postId = $_REQUEST['post_id'];
		$linkId = $_REQUEST['link_id'];

		if (!empty($_REQUEST['confirm'])) {
			ViewController::sendJson([ "status" => Storage::removeLink($postId, $linkId)]);
		}

		ViewController::loadView('remove', [
			'postId' => $postId,
			'link' => Storage::getLink($postId, $linkId),
			'textDomain' => CustomPageLinks::TEXT_DOMAIN
		]);
		wp_die();
	}

	public static function editLink()
	{
		self::checkAccess();

		$postId = $_REQUEST['post_id'];
		$linkId = $_REQUEST['link_id'];

		ViewController::loadView('edit', [
			'postId' => $postId,
			'link' => Storage::getLink($postId, $linkId),
			'textDomain' => CustomPageLinks::TEXT_DOMAIN
		]);

		wp_die();
	}

	public static function doEditLink()
	{
		self::checkAccess();

		$postId = $_REQUEST['post_id'];
		$linkId = $_REQUEST['link_id'];

		$link = Storage::getLink($postId, $linkId);
		$link->setUrl($_REQUEST['href']);
		$link->setTitle($_REQUEST['title']);
		$link->setTarget($_REQUEST['target']);

		ViewController::sendJson(["status" => Storage::addLink($postId, $link)]);
	}
} 