<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 28-04-15
 * Time: 20:19
 */

namespace dk\mholt\CustomPageLinks;


class ViewController {
	protected $vars;
	protected $view;

	/**
	 * Load the view given by the variable $view and the variables as set in $vars.
	 *
	 * @param string $view
	 * @param array $vars
	 * @param bool $echo
	 *
	 * @return string The parsed view
	 */
	public static function loadView($view, array $vars = [], $echo = true)
	{
		$controller = new ViewController($view, $vars);

		$view = $controller->load();

		if ($echo)
		{
			echo $view;
		}

		return $controller->load();
	}

	public static function sendJson($ret, $status = 200)
	{
		header(sprintf("HTTP/1.1 %s %s", $status, HttpStatus::getStatus($status)));
		header("Content-type: application/json");

		echo json_encode($ret);
		wp_die();
	}

	public function __construct($view = null, array $vars = [])
	{
		$this->vars = $vars;
		$this->view = $view;
	}

	/**
	 * @param string $view
	 *
	 * @return ViewController
	 */
	public function setView($view)
	{
		$this->view = $view;

		return $this;
	}

	/**
	 * Add the variable with the given key to the view.
	 * If the variable has already been added, it will be overwritten.
	 *
	 * @param mixed $key
	 * @param mixed $val
	 *
	 * @return ViewController
	 */
	public function addVar($key, $val)
	{
		$this->vars[$key] = $val;

		return $this;
	}

	/**
	 * Load the view and return it as a string
	 *
	 * @return string
	 */
	public function load()
	{
		$vars = &$this->vars;
		$fileName = sprintf("%s/templates/%s.php", CustomPageLinks::$PLUGIN_PATH, $this->view);
		$post_content = $this->parseViewFile($fileName, $vars);
		if(!$post_content) {
			// if the view data is a string, we'll just output it...
			if( ! $fileName && is_string($vars)) {
				$post_content .= $vars;
			}
			else {
				self::error("Can't find view file <pre>".$fileName."</pre>\r\n", $suppress_esc=TRUE);
			}
		}
		return $post_content;
	}

	private function parseViewFile($file, &$vars) {
		if(!file_exists($file)) {
			return FALSE;
		}
		else {
			if(!defined('CPL_VIEW') ) define('CPL_VIEW',true);
			ob_start();
			echo "<!--// VIEW FILE START: $file //-->\r\n";
			if( ! empty( $vars ) ) {
				if( ! is_array($vars) ) {
					$vars = get_object_vars($vars);
				}
				extract($vars);
			}
			include($file);
			echo "<!--// VIEW FILE END: $file //-->\r\n";
			return ob_get_clean();
		}
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
		$error .= "<p><strong>Backtrace:</strong><br><em>NB: file paths are relative to '".self::$PLUGIN_PATH."'</em></p>";

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