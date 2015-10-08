<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 23-04-15
 * Time: 22:26
 */

namespace dk\mholt\CustomPageLinks\model;

class LinkContainer {
	const META_TAG = 'customPageLinks';

	/**
	 * Get the links associated with the given post
	 *
	 * @param int $postId The Post ID
	 *
	 * @return Link[]
	 */
	public static function all($postId)
	{
		$meta = get_post_meta($postId, self::META_TAG, true);

		return (empty($meta))
			? []
			: $meta
		;
	}

	/**
	 * @param int $postId
	 * @param string $linkId
	 *
	 * @throws \Exception
	 *
	 * @return Link
	 */
	public static function get($postId, $linkId)
	{
		$links = self::all($postId);

		if (array_key_exists($linkId, $links))
		{
			return $links[$linkId];
		}

		throw new \Exception(sprintf("Invalid link %s for post %s", $linkId, $postId));
	}
} 