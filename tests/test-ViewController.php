<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 23-01-16
 * Time: 23:05
 */

namespace dk\mholt\CustomPageLinks\tests;

use dk\mholt\CustomPageLinks\ViewController as BaseViewController;

class ViewController extends \WP_UnitTestCase {
	/**
	 * @expectedException \WPDieException
	 */
	public function testSendJsonEchoesJsonString()
	{
		$this->expectOutputString("[]");
		BaseViewController::sendJson( [ ], 200, false );
	}

	/**
	 * @expectedException \WPDieException
	 */
	public function testSendJsonEchoesJsonObject()
	{
		$jsonInput = [ "hello" => "world" ];
		$this->expectOutputString(json_encode( $jsonInput ));
		BaseViewController::sendJson($jsonInput, 200, false);
	}
} 