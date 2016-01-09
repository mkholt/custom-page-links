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

		$updater->method('handleUpdate')
			->willThrowException(new \Exception("This should not be called"));

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
}

class InnerCustomPageLinks extends BaseCustomPageLinks
{
	public static function checkVersion(Updater $updater)
	{
		parent::checkVersion($updater);
	}
}