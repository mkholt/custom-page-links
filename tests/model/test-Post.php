<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 12-12-15
 * Time: 20:45
 */

namespace dk\mholt\CustomPageLinks\tests\model;

use dk\mholt\CustomPageLinks\model\Link as LinkModel;
use dk\mholt\CustomPageLinks\model\Post as PostModel;

class Post extends \WP_UnitTestCase {
	/**
	 * @var \WP_Post
	 */
	private $page;

	/**
	 * @var \WP_Post[]
	 */
	private $created = [];

	public function setUp() {
		// Set up a page for testing
		if (is_null($this->factory))
		{
			$this->factory = new \WP_UnitTest_Factory;
		}

		$this->page = $this->factory->post->create_and_get([
			"post_type" => "page"
		]);

		$this->created[] = $this->page;

		// Make sure it's created
		$this->assertNotEmpty($this->page);
	}

	public function tearDown() {
		foreach ($this->created as $page) {
			wp_delete_post($page->ID);
		}
	}

	public function testConstructSetId()
	{
		$postId = uniqid();
		$post = new PostModel( $postId );

		$this->assertEquals($postId, $post->getPostId());
	}

	public function testConstructFromPost() {
		$post = PostModel::createFromPost($this->page);

		$this->assertEquals($this->page->ID, $post->getPostId());
	}

	public function testAddLink() {
		$postId = $this->page->ID;

		// Make sure it doesn't have links, but an empty array is returned
		$post = new PostModel( $postId );
		$links = $post->getLinks();
		$this->assertTrue(is_array($links), "Returned should be an array");
		$this->assertEmpty($links, "There should be no links");

		// Create a new link, and add it
		$link = new LinkModel();
		$link->setTitle("Link title")
		     ->setUrl("https://github.com/mkholt/custom-page-links");

		$id = $post->addLink($link);
		$this->assertAdded( $id );

		// Make sure the link is in the link list
		$newLinks = $post->getLinks();
		$this->assertNotEmpty($newLinks);
		$this->assertEquals(1, count($newLinks));
		$this->assertEquals($link, reset($newLinks));

		// Create new post-wrapper, get the link list from the db, and make sure the link is there
		$newPost = new PostModel( $postId );
		$newLinks = $newPost->getLinks();
		$this->assertNotEmpty($newLinks);
		$this->assertEquals(1, count($newLinks));
		$this->assertEquals($link, reset($newLinks));
		$this->assertEquals($link, $newPost->getLink($link->getId()));

		// Create new post-wrapper, make sure we can get the specific link
		$newPost = new PostModel( $postId );
		$this->assertEquals($link, $newPost->getLink($link->getId()));

		// Remove the link
		$this->assertTrue($newPost->removeLink($link));
		$newLinks = $newPost->getLinks();
		$this->assertEmpty($newLinks);
		$this->assertTrue(is_array($newLinks));

		// Create new post-wrapper, make sure there are no links
		$newPost = new PostModel( $postId );
		$newLinks = $newPost->getLinks();
		$this->assertEmpty($newLinks);
		$this->assertTrue(is_array($newLinks));
	}

	public function testSortLinks() {
		$post = PostModel::createFromPost($this->page);

		$link1 = new LinkModel();
		$link2 = new LinkModel();

		$this->assertAdded($post->addLink($link1));
		$this->assertAdded($post->addLink($link2));

		// Make sure the links are in the right order
		$links = $post->getLinks();
		$this->assertEquals(2, count($links));
		$links = array_values($links);
		$this->assertEquals($link1, $links[0]);
		$this->assertEquals($link2, $links[1]);

		// Sort the links
		$post->sortLinks([$link2->getId(), $link1->getId()]);

		// Make sure the links are reversed
		$links = $post->getLinks();
		$this->assertEquals(2, count($links));
		$links = array_values($links);
		$this->assertEquals($link2, $links[0]);
		$this->assertEquals($link1, $links[1]);

		// Make sure the links are reversed, when fetching from DB
		$post = PostModel::createFromPost($this->page);
		$links = $post->getLinks();
		$this->assertEquals(2, count($links));
		$links = array_values($links);
		$this->assertEquals($link2, $links[0]);
		$this->assertEquals($link1, $links[1]);
	}

	public function testRemoveNonExistant() {
		$post = new PostModel($this->page->ID);
		$link = new LinkModel();
		$this->assertFalse($post->removeLink($link));
	}

	/**
	 * @expectedException \Exception
	 */
	public function testGetNotExistant() {
		$post = PostModel::createFromPost($this->page);
		$post->getLink(uniqid());
	}

	/**
	 * @param $id
	 */
	protected function assertAdded( $id ) {
		$this->assertNotEmpty( $id );
		$this->assertTrue( ( is_bool( $id ) && $id ) || is_int( $id ),
			sprintf( "Expecting ID to be an integer, or boolean(true), was %s",
				is_bool( $id )
					? ( "boolean (" . ( $id ? "true" : "false" ) . ")" )
					: get_class( $id ) ) );
	}
} 