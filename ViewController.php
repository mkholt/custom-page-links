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
	 *
	 * @return string The parsed view
	 */
	public static function loadView($view, array $vars = [])
	{
		$controller = new ViewController($view, $vars);
		return $controller->load();
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
				CustomPageLinks::error("Can't find view file <pre>".$fileName."</pre>\r\n", $suppress_esc=TRUE);
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
} 