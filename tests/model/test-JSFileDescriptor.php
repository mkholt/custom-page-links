<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 11-01-16
 * Time: 19:47
 */

namespace model;

use dk\mholt\CustomPageLinks\CustomPageLinks;
use \dk\mholt\CustomPageLinks\model\JSFileDescriptor as BaseFileDescriptor;
use org\bovigo\vfs\content\LargeFileContent;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;

class JSFileDescriptor extends \PHPUnit_Framework_TestCase {
	/** @var vfsStreamDirectory */
	private $root;

	/** @var string */
	private $filename;

	/** @var string */
	private $namePart;

	/** @var vfsStreamFile */
	private $file;

	/** @var string */
	private $path;

	public function setUp() {
		$this->root     = vfsStream::setup();
		$this->namePart = uniqid();
		$this->filename = $this->namePart . '.js';
		vfsStream::create([
			"js" => []
		]);
		$subDir = $this->root->getChild("js");

		$this->file     = vfsStream::newFile( $this->filename )->at( $subDir );
		$this->path     = $this->file->url();
	}

	public function testSetsFilename() {
		$fd = new BaseFileDescriptor( $this->path );

		$this->assertEquals( $this->path, $fd->getFilename() );
	}

	public function testSetsName() {
		$fd = new BaseFileDescriptor( $this->path );

		$this->assertEquals( $this->namePart, $fd->getName() );
		$this->assertEquals( '.js', $fd->getExtension() );
	}

	public function testTranslateSource() {
		$mainFile = $this->getMainClassFilename();
		$dir      = dirname( $mainFile );

		$fd       = new BaseFileDescriptor( $dir . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $this->filename );

		$expectedSource = plugins_url( 'js' . DIRECTORY_SEPARATOR . $this->file->getName(), $this->getMainClassFilename() );
		$actualSource   = $fd->getSourcePath();

		$this->assertEquals( $expectedSource, $actualSource );
	}

	public function testPluginRelative() {
		$mainFile = $this->getMainClassFilename();
		$dir      = dirname( $mainFile );

		$fd       = new BaseFileDescriptor( $dir . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $this->filename );
		$expected = 'js' . DIRECTORY_SEPARATOR . $this->filename;
		$actual   = $fd->getPluginRelativePath();

		$this->assertEquals( $expected, $actual );
	}

	public function testParseHandle() {
		$fd       = new BaseFileDescriptor( $this->path );
		$filename = $this->namePart;

		$this->assertEquals( "cpl-${filename}", $fd->getHandle() );
	}

	public function testEnqueues() {
		/**
		 * @var BaseFileDescriptor $fd
		 */
		list( $created, $version, $json, $fd ) = $this->getFileWithMetadata( [ "jquery" ] );

		$fd->enqueue();
		$this->assertTrue( wp_script_is( $fd->getHandle(), 'enqueued' ), "Expected script to be enqueued" );
	}

	public function testParseMetadata() {
		$depends = range( 0, 10 );

		/**
		 * @var BaseFileDescriptor $fd
		 */
		list( $created, $version, $json, $fd ) = $this->getFileWithMetadata( $depends );

		$meta = $fd->getMeta();
		$this->assertNotEmpty( $meta );
		$this->assertTrue( is_array( $meta ) );
		$this->assertEquals( 3, count( $meta ) );
		$this->assertArrayHasKey( 'created', $meta );
		$this->assertEquals( $created, $meta['created'] );
		$this->assertArrayHasKey( 'since', $meta );
		$this->assertEquals( $version, $meta['since'] );
		$this->assertArrayHasKey( 'depends', $meta );
		$this->assertEquals( $json, $meta['depends'] );
		$this->assertEquals( $depends, json_decode( $meta['depends'] ) );
	}

	public function testDependenciesOne() {
		$depends = [ "dependency" ];

		/**
		 * @var BaseFileDescriptor $fd
		 */
		list( $created, $version, $json, $fd ) = $this->getFileWithMetadata( $depends );
		$dependencies = $fd->getDependencies();

		$this->assertNotEmpty( $dependencies, "The file has dependencies" );
		$this->assertTrue( is_array( $dependencies ), "The dependencies should be an array" );
		$this->assertEquals( 1, count( $dependencies ), "There should be one dependency" );
		$this->assertEquals( $depends, $dependencies, "The file depends on " . json_encode( $depends ) );
	}

	public function testDependenciesMultiple() {
		$depends = range( 0, 10 );

		/**
		 * @var BaseFileDescriptor $fd
		 */
		list( $created, $version, $json, $fd ) = $this->getFileWithMetadata( $depends );

		$dependencies = $fd->getDependencies();

		$this->assertNotEmpty( $dependencies, "The file has dependencies" );
		$this->assertTrue( is_array( $dependencies ), "The dependencies should be an array" );
		$this->assertEquals( count( $depends ), count( $dependencies ), "There should be one dependency" );
		$this->assertEquals( $depends, $dependencies, "The file depends on " . json_encode( $depends ) );
	}

	public function testNoDependencies() {
		/**
		 * @var BaseFileDescriptor $fd
		 */
		list( $created, $version, $json, $fd ) = $this->getFileWithMetadata( null );

		$dependencies = $fd->getDependencies();

		$this->assertEmpty( $dependencies, "The file has no dependencies" );
		$this->assertTrue( is_array( $dependencies ), "The dependencies should be an array" );
		$this->assertEquals( 0, count( $dependencies ), "There should be no dependencies" );
		$this->assertEquals( [], $dependencies, "The file depends on []");
	}

	/**
	 * @expectedException \Exception
	 * @@expectedExceptionMessageRegExp /Unexpected line: (.*), expected opening comment/
	 */
	public function testMissingMeta() {
		$this->file->withContent( "var f = { \"Hello\": \"World\" }" );

		$fd = new BaseFileDescriptor( $this->path );
		$fd->getDependencies();
	}

	public function testHeaderCaseInsensitive() {
		$depends = [ 'jquery' ];
		list( $json, $metadata ) = $this->getMetadata( $depends, date( 'c' ), CustomPageLinks::CURRENT_VERSION );

		$filename = uniqid() . '.js';
		$root     = vfsStream::setup();
		$file     = vfsStream::newFile( $filename )
		                     ->withContent( strtolower($metadata) )
		                     ->at( $root );

		$fd = new BaseFileDescriptor( $file->url(), $root->url() );
		$dependencies = $fd->getDependencies();

		$this->assertNotEmpty( $dependencies, "The file has dependencies" );
		$this->assertTrue( is_array( $dependencies ), "The dependencies should be an array" );
		$this->assertEquals( 1, count( $dependencies ), "There should be one dependency" );
		$this->assertEquals( $depends, $dependencies, "The file depends on " . json_encode( $depends ) );
	}

	/**
	 * @param array $depends
	 *
	 * @return array
	 */
	protected function getFileWithMetadata( $depends ) {
		$created = date( 'c' );
		$version = CustomPageLinks::CURRENT_VERSION;
		list( $json, $metadata ) = $this->getMetadata( $depends, $created, $version );

		$this->file->withContent( LargeFileContent::withMegabytes( 1 ) );

		$fp = fopen( $this->path, 'r+' );
		fwrite( $fp, $metadata );

		$fd = new BaseFileDescriptor( $this->path );

		return array( $created, $version, $json, $fd );
	}

	/**
	 * @param $depends
	 * @param $created
	 * @param $version
	 *
	 * @return array
	 */
	protected function getMetadata( $depends, $created, $version ) {
		$dependsStr = "";
		$json       = json_encode( $depends );
		if ( null !== $depends ) {
			$dependsStr = " * Depends: ${json}\n";
		}
		$metadata = "/** CustomPageLinks Meta\n * Created: ${created}\n * Since: ${version}\n{$dependsStr}*/";

		return array( $json, $metadata );
	}

	/**
	 * @return string
	 */
	protected function getMainClassFilename() {
		$reflector      = new \ReflectionClass( 'dk\mholt\CustomPageLinks\CustomPageLinks' );
		$pluginFilename = $reflector->getFileName();

		return $pluginFilename;
	}
}