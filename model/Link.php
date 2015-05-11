<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 23-04-15
 * Time: 21:43
 */

namespace dk\mholt\CustomPageLinks\model;


use dk\mholt\CustomPageLinks\ViewController;

class Link implements \JsonSerializable {
	private $id;
	private $url;
	private $title;
	private $mediaUrl;
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

		return $this;
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

	/**
	 * @param string $mediaUrl
	 *
	 * @return Link
	 */
	public function setMediaUrl($mediaUrl)
	{
		$this->mediaUrl = $mediaUrl;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMediaUrl()
	{
		return $this->mediaUrl;
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
	public function toString() {
		return ViewController::loadView('link', ['link' => $this], false);
	}

	function jsonSerialize() {
		return [
			'id'        => $this->getId(),
			'url'       => $this->getUrl(),
			'title'     => $this->getTitle(),
			'mediaUrl'  => $this->getMediaUrl(),
			'target'    => $this->getTarget(),
			'html'      => $this->toString()
		];
	}


} 