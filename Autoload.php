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

	public static function register() {
		$autoloader = new Autoload();
		spl_autoload_register([$autoloader, 'load']);
	}

	private function __construct()
	{
		$this->pluginPath = plugin_dir_path( __FILE__ );
	}

	public function load($cls)
	{
		$cls = ltrim($cls, '\\');
		if(strpos($cls, __NAMESPACE__) !== 0)
			return;

		$filename = str_replace(__NAMESPACE__, '', $cls);
		$filename = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $filename), '/');
		$path = sprintf("%s%s.php", $this->pluginPath, $filename);

		require_once($path);

		if (is_callable([$cls, '__init__']))
		{
			call_user_func([$cls, '__init__']);
		}
	}
} 