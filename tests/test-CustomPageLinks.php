<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 09-01-16
 * Time: 20:59
 */

namespace dk\mholt\CustomPageLinks\tests;

use dk\mholt\CustomPageLinks\CustomPageLinks as BaseCustomPageLinks;
use dk\mholt\CustomPageLinks\Updater;

class CustomPageLinks extends \WP_UnitTestCase
{
	public function testVersionAlreadyUpdated()
	{
		update_option(BaseCustomPageLinks::TEXT_DOMAIN, ["version" => BaseCustomPageLinks::CURRENT_VERSION]);

		$updater = $this->getMockBuilder('dk\mholt\CustomPageLinks\Updater')
			->getMock();

		$updater->expects($this->exactly(0))
			->method('handleUpdate')
			->withAnyParameters();

		InnerCustomPageLinks::checkVersion($updater);
	}

	public function testVersionNotUpdatedUpdaterCalledEmpty()
	{
		delete_option(BaseCustomPageLinks::TEXT_DOMAIN);

		$updater = $this->getMockBuilder('dk\mholt\CustomPageLinks\Updater')
			->getMock();

		$updater->expects($this->exactly(1))
			->method('handleUpdate')
			->with($this->equalTo(null))
			->willReturn(0);

		InnerCustomPageLinks::checkVersion($updater);
	}

	public function testVersionNotUpdatedNoPages()
	{
		delete_option(BaseCustomPageLinks::TEXT_DOMAIN);

		$oldPages = get_pages();
		$this->assertEmpty($oldPages);
		InnerCustomPageLinks::checkVersion(new Updater());

		$newPages = get_pages();
		$this->assertEmpty($newPages);

		$option = get_option(BaseCustomPageLinks::TEXT_DOMAIN);
		$this->assertArrayHasKey('version', $option);
		$this->assertEquals(BaseCustomPageLinks::CURRENT_VERSION, $option['version']);
	}

	public function testStartsWith()
	{
		$this->assertTrue(BaseCustomPageLinks::startsWith("abcdef", "ab"));
		$this->assertFalse(BaseCustomPageLinks::startsWith("abcdef", "AB"));
		$this->assertTrue(BaseCustomPageLinks::startsWith("abcdef", "AB", true));

		$this->assertFalse(BaseCustomPageLinks::startsWith("abcdef", "cd"));
		$this->assertFalse(BaseCustomPageLinks::startsWith("abcdef", "cd", true));
		$this->assertFalse(BaseCustomPageLinks::startsWith("abcdef", "CD", true));

		$this->assertFalse(BaseCustomPageLinks::startsWith("abcdef", "ef"));
		$this->assertFalse(BaseCustomPageLinks::startsWith("abcdef", "ef", true));
		$this->assertFalse(BaseCustomPageLinks::startsWith("abcdef", "EF", true));

		$this->assertTrue(BaseCustomPageLinks::startsWith("abcdef", ""));
		$this->assertTrue(BaseCustomPageLinks::startsWith("abcdef", "", true));

		$this->assertFalse(BaseCustomPageLinks::startsWith("", "abcdef"));
		$this->assertFalse(BaseCustomPageLinks::startsWith("", "abcdef", true));
		$this->assertFalse(BaseCustomPageLinks::startsWith("", "ABCDEF", true));

		$this->assertFalse(BaseCustomPageLinks::startsWith("ABCDEF", "ab"));
		$this->assertTrue(BaseCustomPageLinks::startsWith("ABCDEF", "ab", true));
	}
}

class InnerCustomPageLinks extends BaseCustomPageLinks
{
	public static function checkVersion(Updater $updater)
	{
		parent::checkVersion($updater);
	}
}