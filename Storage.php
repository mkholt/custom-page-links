<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 23-04-15
 * Time: 22:26
 */

namespace dk\mholt\CustomPageLinks;

use dk\mholt\CustomPageLinks\model\Link;

class Storage {
	const META_TAG = 'customPageLinks';

	/**
	 * Get the links associated with the given post
	 *
	 * @param int $id The Post ID
	 *
	 * @return Link[]
	 */
	public static function getLinks($id)
	{
		$meta = get_post_meta($id, self::META_TAG, true);

		return (empty($meta))
			? []
			: $meta
		;
	}

	/**
	 * Add a link to the given post
	 *
	 * @param int $id
	 * @param Link $link
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	public static function addLink($id, Link $link)
	{
		$meta = self::getLinks($id);
		$meta[$link->getId()] = $link;

		return update_post_meta($id, self::META_TAG, $meta);
	}

	/**
	 * Remove the link given by the post and link id
	 *
	 * @param $postId
	 * @param $linkId
	 * @return int|bool True on successful update,
	 *                  false on failure.
	 */
	public static function removeLink($postId, $linkId)
	{
		$meta = self::getLinks($postId);

		if (array_key_exists($linkId, $meta))
		{
			unset($meta[$linkId]);

			return boolval(update_post_meta($postId, self::META_TAG, $meta));
		}

		return false;
	}
} 