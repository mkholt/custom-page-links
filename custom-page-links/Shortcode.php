<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 28-04-15
 * Time: 22:54
 */

namespace dk\mholt\CustomPageLinks;

use dk\mholt\CustomPageLinks\model\Post;

class Shortcode {
	public static function init() {
		add_shortcode( 'cpl', [ __NAMESPACE__ . '\Shortcode', 'printLinks' ] );

		wp_enqueue_style( 'cpl-screen', plugins_url( 'stylesheets/screen.css', __FILE__ ), [], false, 'screen,projection' );
		wp_enqueue_style( 'cpl-print', plugins_url( 'stylesheets/print.css', __FILE__ ), [], false, 'print' );
	}

	public static function printLinks() {
		$post  = new Post( get_the_ID() );
		$links = $post->getLinks();

		$ret = "";
		foreach ( $links as $link ) {
			$ret .= $link->toString();
		}

		return $ret;
	}
} 