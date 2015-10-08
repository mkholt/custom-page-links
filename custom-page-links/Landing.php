<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 12-05-15
 * Time: 11:20
 */

namespace dk\mholt\CustomPageLinks;


use dk\mholt\CustomPageLinks\model\LinkContainer;

class Landing {
	const LANDING_ACTION = 'cpl_visit_link';

	protected static $className;

	public static function __init__() {
		self::$className = __NAMESPACE__ . '\Landing';
	}

	public static function init() {
		add_action( "wp_ajax_" . self::LANDING_ACTION, [ self::$className, "visitLink" ]);
		add_action( "wp_ajax_nopriv_" . self::LANDING_ACTION, [ self::$className, "visitLink" ]);
	}

	public static function visitLink() {
		$link = LinkContainer::get($_REQUEST['post'], $_REQUEST['link']);

		header('HTTP/1.1 '.HttpStatus::HttpTemporaryRedirect);
		header(sprintf('Location: %s', $link->getUrl()));
		wp_die();
	}
} 