<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 28-04-15
 * Time: 22:54
 */

namespace dk\mholt\CustomPageLinks;


class Shortcode {
	public static function addShortcode() {
		add_shortcode( 'cpl', [ __NAMESPACE__ . '\Shortcode', 'printLinks' ] );
	}

	public static function printLinks() {
		$links = Storage::getLinks(get_the_ID());

		$ret = "";
		foreach ($links as $link)
		{
			$ret .= $link->toString() . "<br/>\n";
		}

		return $ret;
	}
} 