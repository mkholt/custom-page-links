<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 23-04-15
 * Time: 21:43
 */

namespace dk\mholt\CustomPageLinks\model;


class Link {
	private $id;
	private $url;
	private $title;
	private $target;

	private static $targets = [
		"_blank",
		"_self",
		"_parent",
		"_top"
	];

	public function __construct()
	{
		$this->id = uniqid();
	}

	public function getId()
	{
		return $this->id;
	}

	public function setTarget($target)
	{
		$this->target = (self::validTarget($target))
			? $target
			: reset(self::$targets)
		;
	}

	/**
	 * @return string
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * @param boolean $escape
	 * @return string
	 */
	public function getTitle($escape = false) {
		return ($escape)
			? htmlspecialchars($this->title)
			: $this->title
		;
	}

	/**
	 * @param string $title
	 */
	public function setTitle( $title ) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl( $url ) {
		// TODO : Validate URL
		$this->url = $url;
	}

	public static function validTarget($target)
	{
		return in_array($target, self::$targets);
	}

	public static function getTargets()
	{
		return self::$targets;
	}

	/**
	 * @return string
	 */
	public function toString()
	{
		return sprintf("<a href=\"%s\" title=\"%s\" target=\"%s\">%s</a>",
			$this->getUrl(),
			$this->getTitle(true),
			$this->getTarget(),
			$this->getTitle(true)
		);
	}
} 