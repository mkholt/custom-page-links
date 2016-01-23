<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 20-01-16
 * Time: 21:06
 */

namespace dk\mholt\CustomPageLinks\test;

use dk\mholt\CustomPageLinks\Autoload as BaseAutoload;
use dk\mholt\CustomPageLinks\CustomPageLinks;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class Autoload extends \PHPUnit_Framework_TestCase {
	public function testKnowsPluginPath() {
		BaseAutoload::unregister();

		$ial = new InnerAutoload();
		$ial->doRegister();

		$this->assertEquals(CustomPageLinks::$PLUGIN_PATH, $ial->pluginPath);
	}

	public function testRegisters()
	{
		BaseAutoload::unregister();

		BaseAutoload::register();

		$functions = spl_autoload_functions();
		$this->assertContains([InnerAutoload::getInstance(), 'load'], $functions);
	}

	public function testUnregisters()
	{
		BaseAutoload::register();
		$al = InnerAutoload::getInstance();
		$functions = spl_autoload_functions();
		$this->assertContains([$al, 'load'], $functions);

		$status = BaseAutoload::unregister();
		$this->assertTrue($status);
		$functions = spl_autoload_functions();

		$this->assertNotContains([$al, 'load'], $functions);

		$status = BaseAutoload::unregister();
		$this->assertFalse($status);
	}

	public function testCallsInitNoSlash()
	{
		$parent = $this->createTestFile();
		BaseAutoload::unregister();

		$ial = new InnerAutoload();
		$ial->doRegister($parent->url());

		new Test();
		$this->assertTrue(Test::$test);
	}

	public function testCallsInitSlash()
	{
		$parent = $this->createTestFile();
		BaseAutoload::unregister();

		$ial = new InnerAutoload();
		$ial->doRegister($parent->url() . '/');

		new Test();
		$this->assertTrue(Test::$test);
	}

	public function testSkipsOtherNamespace()
	{
		$parent = $this->createTestFile( "ns" );

		$ial = new InnerAutoload();
		$ial->load("\\ns\\Test");

		$path = $parent->url() . DIRECTORY_SEPARATOR . 'ns' . DIRECTORY_SEPARATOR . 'Test.php';
		require_once( $path );
		$this->assertFalse(\ns\Test::$test);
	}

	/**
	 * @return null|vfsStreamDirectory
	 */
	protected function createTestFile($namespace = null) {
		if ( empty( $namespace ) ) {
			$namespace = __NAMESPACE__;
		}
		$root           = vfsStream::setup();
		$namespaceSplit = explode( "\\", $namespace );
		$bottom         = $root;
		$parent         = null;
		foreach ( $namespaceSplit as $dir ) {
			$bottom->addChild( new vfsStreamDirectory( $dir ) );
			$parent = $bottom;
			$bottom = $bottom->getChild( $dir );
		}

		$content = "<?php\nnamespace " . $namespace . ";\nclass Test { public static \$test = false; public static function __init__() { self::\$test = true; } }";
		vfsStream::newFile( 'Test.php' )
		         ->withContent( $content )
		         ->at( $bottom );

		return $parent;
	}
}

class InnerAutoload extends BaseAutoload
{
	public $pluginPath;

	public function __construct()
	{}

	public function doRegister($pluginPath = null)
	{
		parent::register();

		if (!empty($pluginPath))
		{
			parent::$instance->pluginPath = $pluginPath;
		}

		$this->pluginPath = parent::$instance->pluginPath;
	}

	public static function getInstance()
	{
		return parent::$instance;
	}
}