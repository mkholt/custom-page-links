<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 12-11-15
 * Time: 21:50
 */

namespace dk\mholt\CustomPageLinks\model;


class Post {
	const META_TAG = 'customPageLinks';

	/**
	 * @var int The ID of the post
	 */
	private $postId;

	/**
	 * @var Link[] The links from this post
	 */
	private $links;

	/**
	 * Construct from a given WP_Post instance
	 *
	 * @param \WP_Post $wpPost
	 *
	 * @return Post
	 */
	public static function createFromPost(\WP_Post $wpPost) {
		return new Post( $wpPost->ID );
	}

	/**
	 * Construct from a given post id
	 *
	 * @param $postId
	 */
	public function __construct($postId) {
		$this->postId = $postId;
	}

	/**
	 * @return int
	 */
	public function getPostId() {
		return $this->postId;
	}

	/**
	 * Get the links associated with the post
	 *
	 * @return Link[]
	 */
	public function getLinks() {
		if ( empty( $this->links ) ) {
			$meta = get_post_meta( $this->getPostId(), self::META_TAG, true );

			$this->setLinks( empty( $meta ) ? [ ] : $meta );
		}

		return $this->links;
	}

	/**
	 * Set the links associated with the post
	 *
	 * @param Link[] $links
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	protected function setLinks(array $links) {
		$this->links = $links;

		return update_post_meta( $this->getPostId(), self::META_TAG, $links );
	}

	/**
	 * Get a specific link associated with the post
	 *
	 * @param int $linkId The ID of the link
	 * @return Link
	 * @throws \Exception if the link doesn't exist
	 */
	public function getLink($linkId) {
		$links = $this->getLinks();

		if ( array_key_exists( $linkId, $links ) ) {
			return $links[ $linkId ];
		}

		throw new \Exception( sprintf( "Invalid link %s for post %s", $linkId, $this->getPostId() ) );
	}

	/**
	 * Add a link to the post
	 *
	 * @param Link $link
	 *
	 * @return bool|int Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	public function addLink(Link $link) {
		$links                   = $this->getLinks();
		$links[ $link->getId() ] = $link;

		return $this->setLinks( $links );
	}

	/**
	 * Remove a link from the post
	 *
	 * @param Link $link The link to remove
	 *
	 * @return bool True on successful update,
	 *              false on failure
	 */
	public function removeLink(Link $link) {
		$links = $this->getLinks();

		if ( array_key_exists( $link->getId(), $links ) ) {
			unset( $links[ $link->getId() ] );

			return boolval( $this->setLinks( $links ) );
		}

		return false;
	}
} 