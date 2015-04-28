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
	public static function addMetaBox(\WP_Post $post)
	{
		add_thickbox();

		ViewController::loadView('metabox', [
			'post' => $post,
			'meta' => Storage::getLinks($post->ID),
			'textDomain' => CustomPageLinks::TEXT_DOMAIN
		]);
	}

	public static function addAction()
	{
		add_action('wp_ajax_cpl_new_link', [__NAMESPACE__ . '\Metabox', 'addLink']);
		add_action('wp_ajax_cpl_remove_link', [__NAMESPACE__ . '\Metabox', 'removeLink']);
		add_action('wp_ajax_cpl_edit_link', [__NAMESPACE__ . '\Metabox', 'editLink']);
	}

	public static function addLink()
	{
		$link = new Link();
		$link->setUrl($_REQUEST['href']);
		$link->setTitle($_REQUEST['title']);
		$link->setTarget($_REQUEST['target']);

		ViewController::sendJson(["status" => Storage::addLink($_REQUEST['id'], $link)]);
	}

	public static function removeLink()
	{
		$postId = $_REQUEST['post_id'];
		$linkId = $_REQUEST['link_id'];

		if (!empty($_REQUEST['confirm'])) {
			echo json_encode( [ "status" => Storage::removeLink($postId, $linkId) ] );
			exit;
		}

		ViewController::loadView('remove', [
			'link' => Storage::getLink($postId, $linkId),
			'textDomain' => CustomPageLinks::TEXT_DOMAIN
		]);
	}

	public static function editLink()
	{
		$postId = $_REQUEST['post_id'];
		$linkId = $_REQUEST['link_id'];

		if (!empty($_REQUEST['confirm'])) {
			//echo json_encode( [ "status" => Storage::removeLink($postId, $linkId) ] );
			exit;
		}

		ViewController::loadView('edit', [
			'link' => Storage::getLink($postId, $linkId),
			'textDomain' => CustomPageLinks::TEXT_DOMAIN
		]);
		exit;
	}
} 