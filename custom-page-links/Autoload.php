<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 23-04-15
 * Time: 20:42
 */

namespace dk\mholt\CustomPageLinks;

class Autoload {
	protected $pluginPath;

	/** @var Autoload */
	protected static $instance;

	public static function register() {
		if (null == self::$instance)
		{
			self::$instance = new Autoload();
			spl_autoload_register([self::$instance, 'load']);
		}
	}

	/**
	 * @return bool
	 */
	public static function unregister() {
		if (null == self::$instance)
		{
			return false;
		}

		$status = spl_autoload_unregister([self::$instance, 'load']);
		self::$instance = null;

		return $status;
	}

	protected function __construct()
	{
		$this->pluginPath = plugin_dir_path( __FILE__ );
	}

	public function load($cls)
	{
		$cls = ltrim($cls, '\\');
		if(strpos($cls, __NAMESPACE__) !== 0) {
			return;
		}

		$filename = str_replace(__NAMESPACE__, '', $cls);
		$filename = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $filename), '/');
		$separator = substr($filename, 0, 1) == DIRECTORY_SEPARATOR ? "" : DIRECTORY_SEPARATOR;
		$path = sprintf("%s%s%s.php", $this->pluginPath, $separator, $filename);

		require_once($path);

		if (is_callable([$cls, '__init__']))
		{
			call_user_func([$cls, '__init__']);
		}
	}
} 