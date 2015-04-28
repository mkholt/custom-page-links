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

	private static function esc_html_recursive( $data = FALSE ) {
		if( ! $data ) return FALSE;

		if( is_array($data) OR is_object($data) ) {
			foreach( $data AS $key => & $value ) {
				$value = self::esc_html_recursive( $value );
			}
		}
		else {
			$data = htmlentities($data,ENT_QUOTES);
		}

		return $data;
	}

	// Error handling function, courtesy of Tina MVC
	public static function error($msg)
	{
		$backtrace = debug_backtrace();
		$baseFolder = ABSPATH;

		$error  = "<h2>Registration Error</h2>\r\n";
		$error .= "<p><strong>{$msg}</strong></p>\r\n";
		$error .= "<p><strong>Backtrace:</strong><br><em>NB: file paths are relative to '".self::esc_html_recursive($baseFolder)."/wp-content/plugins/registration'</em></p>";

		$bt_out  = '';

		foreach( $backtrace AS $i => & $b ) {

			// tiwen at rpgame dot de comment in http://ie2.php.net/manual/en/function.debug-backtrace.php#65433
			if (!isset($b['file'])) $b['file'] = '[PHP Kernel]';
			if (!isset($b['line'])) {
				$b['line'] = 'n/a';
			}
			else {
				$b['line'] = vsprintf('%s',$b['line']);
			}

			$b['function'] = isset($b['function']) ? self::esc_html_recursive( $b['function'] ) : '';
			$b['class'] = isset($b['class'])  ? self::esc_html_recursive( $b['class'] ) : '';
			$b['object'] = isset($b['object']) ? self::esc_html_recursive( $b['object'] ) : '';
			$b['type'] = isset($b['type']) ? self::esc_html_recursive( $b['type'] ) : '';
			$b['file'] = isset($b['file']) ? self::esc_html_recursive(str_replace( $baseFolder, '', $b['file'])) : '';

			if( !empty($b['args']) ) {
				$args = '';
				foreach ($b['args'] as $j => $a) {
					if (!empty($args)) {
						$args .= "<br>";
					}
					$args .= ' - Arg['.vsprintf('%s',$j).']: ('.gettype($a) . ') '
					         .'<span style="white-space: pre">'.self::esc_html_recursive(print_r($a,1)).'</span>';
				}

				$b['args'] = $args;
			}

			$bt_out .= '<strong>['.vsprintf('%s',$i).']: '.$b['file'].' ('.$b['line'].'):</strong><br>';
			$bt_out .= ' - Function: '.$b['function'].'<br>';
			$bt_out .= ' - Class: '.$b['class'].'<br>';
			$bt_out .= ' - Type: '.print_r($b['type'],1).'<br>';
			$bt_out .= ' - Object: '.print_r($b['type'],1).'<br>';
			$bt_out .= $b['args'].'<hr>';
			$bt_out .= "\r\n";
		}

		$error .= '<div style="font-size: 70%;">'.$bt_out."</div>\r\n";

		wp_die( $error );
		exit();
	}
}

if (is_admin()) {
	$cpl = CustomPageLinks::initialize();
	add_action('load-page.php', [$cpl, 'addHooks']);
	add_action('load-page-new.php', [$cpl, 'addHooks']);
}