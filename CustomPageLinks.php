<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 10-05-15
 * Time: 13:43
 */

namespace dk\mholt\CustomPageLinks;

use dk\mholt\CustomPageLinks\admin\Metabox;

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

		Metabox::addAction();
	}

	public function addHooks()
	{
		add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
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