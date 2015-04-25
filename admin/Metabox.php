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

class Metabox {
	public static function addMetaBox(\WP_Post $post)
	{
		add_thickbox();

		$meta = Storage::getLinks($post->ID);
		$textDomain = CustomPageLinks::TEXT_DOMAIN;

		include(sprintf("%s/templates/metabox.php", CustomPageLinks::$PLUGIN_PATH));
	}

	public static function addAction()
	{
		add_action('wp_ajax_cpl_new_link', [__NAMESPACE__ . '\Metabox', 'addLink']);
		add_action('wp_ajax_cpl_remove_link', [__NAMESPACE__ . '\Metabox', 'removeLink']);
	}

	public static function addLink()
	{
		$link = new Link();
		$link->setUrl($_REQUEST['href']);
		$link->setTitle($_REQUEST['title']);
		$link->setTarget($_REQUEST['target']);

		echo json_encode(["status" => Storage::addLink($_REQUEST['id'], $link)]);
		exit;
	}

	public static function removeLink()
	{
		$postId = $_REQUEST['post_id'];
		$linkId = $_REQUEST['link_id'];

		if (!empty($_REQUEST['confirm'])) {
			echo json_encode( [ "status" => Storage::removeLink($postId, $linkId) ] );
			exit;
		}

		$link = Storage::getLink($postId, $linkId);
		$textDomain = CustomPageLinks::TEXT_DOMAIN;
		include(sprintf("%s/templates/remove.php", CustomPageLinks::$PLUGIN_PATH));
		exit;
	}
} 