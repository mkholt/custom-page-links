<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 09-01-16
 * Time: 21:10
 */

namespace dk\mholt\CustomPageLinks;

use dk\mholt\CustomPageLinks\model\Post;

class Updater {

	/**
	 * @param $installedVersion
	 *
	 * @return int The number of updated pages
	 */
	public function handleUpdate($installedVersion)
	{
		if ( !empty( $installedVersion ) ) {
			return 0;
		}

		// Upgrade from 1.0 to 1.1
		/** @var \WP_Post[] $pages */
		$pages = get_pages( [
			"meta_key" => Post::META_TAG
		] );
		//$pages = get_pages();

		$count = 0;
		if ( ! empty( $pages ) ) {
			foreach ( $pages as $page ) {
				$post = Post::createFromPost( $page );
				foreach ( $post->getLinks() as $link ) {
					if ($link->getPostId() != $post->getPostId()) {
						$link->setPostId( $post->getPostId() );
						$post->addLink( $link );
						$count ++;
					}
				}
			}
		}

		return $count;
	}
} 