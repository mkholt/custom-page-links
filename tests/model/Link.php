<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 10-12-15
 * Time: 22:25
 */

namespace dk\mholt\CustomPageLinks\tests\model;

use dk\mholt\CustomPageLinks\model\Link as LinkModel;

class Link extends \PHPUnit_Framework_TestCase {
	public function testGetsIdOnConstruct() {
		$link = new LinkModel();

		$this->assertNotEmpty($link->getId(), "Link ID should not be empty");
	}

	public function testGetSetPostId() {
		$link = new LinkModel();

		$postId = rand();
		$link->setPostId($postId);
		$this->assertEquals($postId, $link->getPostId());
	}

	public function testSetValidTarget() {
		$link = new LinkModel();

		$targets = LinkModel::getTargets();
		$this->assertNotEmpty($targets, "There should be valid targets");

		foreach ($targets as $target) {
			$link->setTarget($target);
			$this->assertEquals($target, $link->getTarget());
		}
	}

	public function testSetInvalidTarget() {
		$link = new LinkModel();

		$targets = LinkModel::getTargets();

		$link->setTarget("ThisIsNotATarget");
		$this->assertEquals($targets[0], $link->getTarget());
	}

	public function testSetTitle() {
		$link = new LinkModel();

		$link->setTitle("This is a title");
		$this->assertEquals("This is a title", $link->getTitle());
	}

	public function testGetTitleEncoded() {
		$link = new LinkModel();

		$link->setTitle("This is a \"Title\"");
		$this->assertEquals("This is a &quot;Title&quot;", $link->getTitle(true));
	}

	public function testSetUrl() {
		$link = new LinkModel();

		$link->setUrl("https://github.com/mkholt/custom-page-links");
		$this->assertEquals("https://github.com/mkholt/custom-page-links", $link->getUrl());
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Invalid url rejected
	 */
	public function testRejectUrl() {
		$link = new LinkModel();
		$link->setUrl("foo://thisA'intNo!()!Url");
	}

	public function testSetMediaUrl() {
		$link = new LinkModel();

		$link->setMediaUrl("/this/is/a/url?foo=bar&bar=foo");
		$this->assertNotEmpty($link->getMediaUrl());
		$this->assertEquals(esc_url("/this/is/a/url?foo=bar&bar=foo"), $link->getMediaUrl());
	}

	public function testRejectMediaUrl() {
		$link = new LinkModel();

		$link->setMediaUrl("foo://thisA'intNo!()!Url");
		$this->assertEmpty($link->getMediaUrl());
	}

	public function testAcceptValidTargets() {
		$targets = LinkModel::getTargets();

		foreach ($targets as $target) {
			$this->assertTrue(LinkModel::validTarget($target));
		}
	}

	public function testRejectInvalidTarget() {
		$this->assertFalse(LinkModel::validTarget("ThisIsNotAValidTarget-".uniqid()));
	}

	public function testJsonEncodesAllFields() {
		$expected = [
			"postId" => uniqid(),
			"url" => "https://github.com/mkholt/custom-page-links",
			"title" => "Custom Page Links",
			"mediaUrl" => "/media/url",
			"target" => LinkModel::getTargets()[0]
		];

		$link = new LinkModel();
		$link->setPostId($expected["postId"])
			->setUrl($expected["url"])
			->setTitle($expected["title"])
			->setMediaUrl($expected["mediaUrl"])
			->setTarget($expected["target"])
		;
		$json = json_encode($link);
		$obj = json_decode($json, true);

		foreach ($expected as $key => $val) {
			$this->assertArrayHasKey($key, $obj);
			$this->assertEquals($val, $obj[$key]);
		}
	}
} 