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

	/**
	 * Add a link to the given post
	 *
	 * @param int $postId
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	public function addToPost($postId)
	{
		$meta = LinkContainer::all($postId);
		$meta[$this->getId()] = $this;

		return update_post_meta($postId, LinkContainer::META_TAG, $meta);
	}

	/**
	 * Remove the link given by the post and link id
	 *
	 * @param int $postId
	 * @return bool True on successful update,
	 *              false on failure.
	 */
	public function removeFromPost($postId)
	{
		$meta = LinkContainer::all($postId);

		if (array_key_exists($this->getId(), $meta))
		{
			unset($meta[$this->getId()]);

			return boolval(update_post_meta($postId, LinkContainer::META_TAG, $meta));
		}

		return false;
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
	 *
	 * @return Link
	 */
	public function setUrl( $url ) {
		$this->url = esc_url($url);

		if (empty($this->url))
		{
			throw new \Exception("Invalid url rejected");
		}

		return $this;
	}

	/**
	 * @param string $mediaUrl
	 *
	 * @return Link
	 */
	public function setMediaUrl($mediaUrl)
	{
		$this->mediaUrl = !empty($mediaUrl) ? esc_url($mediaUrl) : null;

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