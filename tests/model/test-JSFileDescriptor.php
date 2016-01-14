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

class JSFileDescriptor extends \PHPUnit_Framework_TestCase {
	public function testSetsFilename() {
		$filename = uniqid();
		$fd       = new BaseFileDescriptor( $filename );

		$this->assertEquals( $filename, $fd->getFilename() );
	}

	public function testExtractsDirectory() {
		$filename = uniqid();
		$fd       = new BaseFileDescriptor( $filename );
		$foundDir = $fd->getBaseDir();
		$expected = CustomPageLinks::$PLUGIN_PATH;

		$this->assertEquals( $expected, $foundDir, "Expected directory to be retrieved from main class" );
	}

	public function testAcceptsDirectory() {
		$filename = uniqid();
		$dir      = uniqid();
		$fd       = new BaseFileDescriptor( $filename, $dir );
		$foundDir = $fd->getBaseDir();

		$this->assertEquals( $dir, $foundDir, "Expected directory to be given in constructor" );
	}

	public function testParseHandle() {
		$uniqid   = uniqid();
		$filename = $uniqid . '.js';
		$fd       = new BaseFileDescriptor( $filename );

		$this->assertEquals( "cpl-${uniqid}", $fd->getHandle() );
	}

	public function testTranslateSource() {
		$filename = uniqid();
		$dir      = CustomPageLinks::$PLUGIN_URL;
		$fd       = new BaseFileDescriptor( $filename, CustomPageLinks::$PLUGIN_PATH );

		$expectedSource = "${dir}/${filename}";
		$this->assertEquals( $expectedSource, $fd->getSourcePath() );
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
		$filename = uniqid() . '.js';
		$root     = vfsStream::setup();
		$file     = vfsStream::newFile( $filename )
		                     ->withContent( "var f = { \"Hello\": \"World\" }" )
		                     ->at( $root );

		$fd = new BaseFileDescriptor( $filename, $root->url() );
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

		$fd = new BaseFileDescriptor( $filename, $root->url() );
		$dependencies = $fd->getDependencies();

		$this->assertNotEmpty( $dependencies, "The file has dependencies" );
		$this->assertTrue( is_array( $dependencies ), "The dependencies should be an array" );
		$this->assertEquals( 1, count( $dependencies ), "There should be one dependency" );
		$this->assertEquals( $depends, $dependencies, "The file depends on " . json_encode( $depends ) );
	}

	public function testUrlResolves()
	{
		$expected = CustomPageLinks::$PLUGIN_URL;
	}

	/**
	 * @param array $depends
	 *
	 * @return array
	 */
	protected function getFileWithMetadata( $depends ) {
		$filename   = "test.js";
		$created    = date( 'c' );
		$version    = CustomPageLinks::CURRENT_VERSION;
		list( $json, $metadata ) = $this->getMetadata( $depends, $created, $version );

		$root = vfsStream::setup();
		$file = vfsStream::newFile( $filename )
		                 ->withContent( LargeFileContent::withMegabytes( 1 ) )
		                 ->at( $root );

		$fp = fopen( $file->url(), 'r+' );
		fwrite( $fp, $metadata );

		$fd = new BaseFileDescriptor( $filename, $root->url() );

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
}