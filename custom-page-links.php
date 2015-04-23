<?php

/*
Plugin Name: Custom Page Links
Plugin URI: https://github.com/mkholt/custom-page-links
Description: Set a custom list of links on a page or news post. The links can be added as a widget to a sidebar.
Version: 1.0
Author: morten
Author URI: http://t-hawk.com
Textdomain: custom-page-links
License: GPL2
*/

/*  Copyright 2015 Morten Holt (email : thawk@t-hawk.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace dk\mholt\CustomPageLinks;

use dk\mholt\CustomPageLinks\admin\Metabox;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CustomPageLinks
{
	const TEXT_DOMAIN = "custom_page_links";
	public static $PLUGIN_PATH;

	/**
	 * @return CustomPageLinks
	 */
	public static function initialize()
	{
		$cpl = new CustomPageLinks();

		return $cpl;
	}

	private function __construct()
	{
		self::$PLUGIN_PATH =  plugin_dir_path( __FILE__ );

		$this->addHooks();

		add_action('admin_enqueue_scripts', [$this, 'addScripts']);

		require_once('Autoload.php');
		Autoload::register();

		Metabox::addAction();
	}

	public function addHooks()
	{
		add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
	}

	public function addScripts($hook)
	{
		if (!in_array($hook, ['post.php', 'post-new.php'])) {
			return;
		}

		wp_enqueue_script('cpl-metabox', plugins_url('/js/metabox.js', __FILE__),
			['jquery']);
		wp_localize_script('cpl-metabox', 'ajax_object', [
			'ajax_url' => admin_url('admin-ajax.php')
		]);
	}

	public function addMetaBoxes($type)
	{
		$metabox = new Metabox();
		add_meta_box('custom-page-links', __('Custom Page Links', self::TEXT_DOMAIN), [$metabox, 'addMetaBox'], 'page', 'side');
	}
}

if (is_admin()) {
	$cpl = CustomPageLinks::initialize();
	add_action('load-page.php', [$cpl, 'addHooks']);
	add_action('load-page-new.php', [$cpl, 'addHooks']);
}