<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 10-05-15
 * Time: 13:43
 */

namespace dk\mholt\CustomPageLinks;

use dk\mholt\CustomPageLinks\admin\Metabox;
use dk\mholt\CustomPageLinks\model\Post;

class CustomPageLinks
{
	const TEXT_DOMAIN = "custom_page_links";
	const CURRENT_VERSION = "1.1";
	public static $PLUGIN_PATH;
	public static $PLUGIN_URL;

	public static function __init__() {
		self::$PLUGIN_PATH = plugin_dir_path( __FILE__ );
		self::$PLUGIN_URL  = plugins_url( '', __FILE__ );
	}

	private function __construct() {

	}

	protected static function checkVersion(Updater $updater) {
		$options = get_option(self::TEXT_DOMAIN, []);

		$installedVersion = null;
		if (array_key_exists("version", $options))
		{
			$installedVersion = $options["version"];
		}

		if ($installedVersion != self::CURRENT_VERSION) {
			$updater->handleUpdate($installedVersion);

			$options["version"] = self::CURRENT_VERSION;
			update_option(self::TEXT_DOMAIN, $options);
		}
	}

	protected static function loadTranslation() {
		add_action( 'plugins_loaded', function() {
			$plugin_rel_path = plugin_basename( dirname( __FILE__ ) ) . '/languages/';
			load_plugin_textdomain( self::TEXT_DOMAIN, false, $plugin_rel_path );
		});
	}

	public static function initialize() {
		self::checkVersion(new Updater());
		self::loadTranslation();

		Metabox::init();
		Shortcode::init();
		Landing::init();
	}

	/**
	 * @param string $haystack
	 * @param string $needle
	 * @param bool $caseInsensitive
	 *
	 * @link http://stackoverflow.com/a/834355
	 *
	 * @return bool
	 */
	public static function startsWith( $haystack, $needle, $caseInsensitive = false ) {
		if ( $caseInsensitive ) {
			$haystack = strtolower( $haystack );
			$needle   = strtolower( $needle );
		}

		$length = strlen( $needle );

		return ( substr( $haystack, 0, $length ) === $needle );
	}
}