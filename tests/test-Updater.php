<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 09-01-16
 * Time: 22:11
 */

namespace dk\mholt\CustomPageLinks\tests;

use dk\mholt\CustomPageLinks\model\Link;
use dk\mholt\CustomPageLinks\tests\model\Post;
use dk\mholt\CustomPageLinks\Updater as BaseUpdater;
use dk\mholt\CustomPageLinks\CustomPageLinks as BaseCustomPageLinks;

class Updater extends \WP_UnitTestCase {
	/**
	 * @var \WP_Post[]
	 */
	private $created = [];

	/**
	 * Get a \WP_Post containing a newly created page
	 *
	 * @return \WP_Post
	 */
	private function getPage() {
		// Set up a page for testing
		$page = $this->factory->post->create_and_get([
			"post_type" => "page"
		]);

		$this->created[$page->ID] = $page;

		// Make sure it's created
		$this->assertNotEmpty($page);

		return $page;
	}

	public function tearDown() {
		foreach ($this->created as $page) {
			wp_delete_post($page->ID);
		}
	}

	public function testNoMatchingPages()
	{
		$updater = new BaseUpdater();

		$creationCount = 10;
		$expected = count($this->created) + $creationCount;
		for ($i = 0; $i < $creationCount; $i++) {
			$this->getPage();
		}

		$oldPages = get_pages();
		$this->assertEquals($expected, count($oldPages));
		$serialized = serialize($oldPages);

		$updater->handleUpdate(BaseCustomPageLinks::CURRENT_VERSION);

		$newPages = get_pages();
		$this->assertEquals($expected, count($newPages));

		$this->assertEquals($serialized, serialize($newPages));
	}

	public function testMatchingOneAlreadyUpdated()
	{
		$page = $this->getPage();
		$link = new Link();
		$link->setPostId($page->ID)
			->setUrl("http://example.com")
			->setTitle("Example.com");

		$post = new \dk\mholt\CustomPageLinks\model\Post($page->ID);
		$post->addLink($link);

		$oldPages = get_pages();
		$serialized = serialize($oldPages);

		$updater = new BaseUpdater();
		$updater->handleUpdate(BaseCustomPageLinks::CURRENT_VERSION);

		$newPages = get_pages();
		$this->assertEquals($serialized, serialize($newPages));

		wp_delete_post($page->ID);
		unset($this->created[$page->ID]);
	}

	public function testMatchingOneNullPostId()
	{
		$page = $this->getPage();
		$link = new Link();
		$link->setUrl("http://example.com")
		     ->setTitle("Example.com");

		$post = new \dk\mholt\CustomPageLinks\model\Post($page->ID);
		$post->addLink($link);

		$oldPages = get_pages();
		$serialized = serialize($oldPages);

		$post = new \dk\mholt\CustomPageLinks\model\Post($page->ID);
		$links = $post->getLinks();
		$this->assertNotEmpty($links);

		/**
		 * @var \dk\mholt\CustomPageLinks\model\Link $link
		 */
		$link = reset($links);
		$this->assertNull($link->getPostId());

		$updater = new BaseUpdater();
		$count = $updater->handleUpdate(null);
		$this->assertEquals(1, $count, "One link should be affected");

		$newPages = get_pages();
		$this->assertEquals($serialized, serialize($newPages));

		$post = new \dk\mholt\CustomPageLinks\model\Post($page->ID);
		$links = $post->getLinks();
		$this->assertNotEmpty($links);

		/**
		 * @var \dk\mholt\CustomPageLinks\model\Link $link
		 */
		$link = reset($links);
		$this->assertEquals($page->ID, $link->getPostId(), "The post-id should be updated");

		wp_delete_post($page->ID);
		unset($this->created[$page->ID]);
	}

	// TODO : Figure out how to insert link in database WITHOUT the postId field which was added in 1.1
	/*public function testMatchingOneNoPostId() {
		$page = $this->getPage();

		$status = update_post_meta( $page->ID, \dk\mholt\CustomPageLinks\model\Post::META_TAG, 'a:1:{s:13:"5553bb3562b0a";O:35:"dk\mholt\CustomPageLinks\model\Link":5:{s:39:"dk\mholt\CustomPageLinks\model\Linkid";s:13:"5553bb3562b0a";s:40:"dk\mholt\CustomPageLinks\model\Linkurl";s:18:"http://example.com";s:42:"dk\mholt\CustomPageLinks\model\Linktitle";s:11:"Example.com";s:45:"dk\mholt\CustomPageLinks\model\LinkmediaUrl";N;s:43:"dk\mholt\CustomPageLinks\model\Linktarget";s:6:"_blank";}}' );
		var_dump(unserialize('a:1:{s:13:"5553bb3562b0a";O:35:"dk\mholt\CustomPageLinks\model\Link":5:{s:39:"dk\mholt\CustomPageLinks\model\Linkid";s:13:"5553bb3562b0a";s:40:"dk\mholt\CustomPageLinks\model\Linkurl";s:18:"http://example.com";s:42:"dk\mholt\CustomPageLinks\model\Linktitle";s:11:"Example.com";s:45:"dk\mholt\CustomPageLinks\model\LinkmediaUrl";N;s:43:"dk\mholt\CustomPageLinks\model\Linktarget";s:6:"_blank";}}'));
		var_dump($page->ID, $status, get_post_meta($page->ID, \dk\mholt\CustomPageLinks\model\Post::META_TAG));
		exit;

		$post = new \dk\mholt\CustomPageLinks\model\Post($page->ID);
		$link = $post->getLink("5553bb3562b0a");
		$this->assertNotNull($link);
		$this->assertNull($link->getPostId());
		$this->assertEquals("http://example.com", $link->getUrl());

		$updater = new BaseUpdater();
		$updater->handleUpdate(BaseCustomPageLinks::CURRENT_VERSION);

		$post = new \dk\mholt\CustomPageLinks\model\Post($page->ID);
		$link = $post->getLink("5553bb3562b0a");
		$this->assertNotNull($link);
		$this->assertEquals($page->ID, $link->getPostId());
		$this->assertEquals("http://example.com", $link->getUrl());
		
		wp_delete_post($page->ID);
		unset($this->created[$page->ID]);
	}*/
} 