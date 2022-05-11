<?php
/**
 * Class PrimaryCategoryTests
 *
 * @package 10up_Primary_Category
 */

/**
 * Sample test case.
 */
class PrimaryCategoryTests extends WP_UnitTestCase {

	private $categories;
	private $post;
	private $primary_category;

	public static function set_up_before_class() {
		parent::set_up_before_class();

		global $wp_rewrite;

		$wp_rewrite->init();
		$wp_rewrite->set_permalink_structure( '/%category%/%postname%/' );

		/**
		 * This was needed as set_category_base does not work in PHPUnit.
		 */
		$wp_rewrite->add_permastruct(
			'category',
			'category/%category%',
			array(
				'with_front'   => true,
				'hierarchical' => true,
				'ep_mask'      => 512,
				'slug'         => 'category',
			)
		);
		$wp_rewrite->flush_rules();
	}

	public function set_up() {
		parent::set_up();

		$this->categories = self::factory()->term->create_many(
			3,
			array(
				'taxonomy' => 'category',
			)
		);

		$this->post = self::factory()->post->create_and_get(
			array(
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			)
		);

		$this->primary_category = get_category( $this->categories[1] );

		wp_set_post_categories( $this->post->ID, $this->categories );
		update_post_meta( $this->post->ID, PRIMARY_CATEGORY_META_KEY, $this->primary_category->slug );
	}

	/**
	 * Tests post permalink structure contains primary category.
	 */
	public function test_permalink_structure() {
		$post_permalink = get_permalink( $this->post );

		$this->assertStringContainsString( $this->primary_category->slug, $post_permalink );
	}

	/**
	 * Tests only primary category posts are being displayed on non-default archive page.
	 */
	public function test_primary_category_filter_works() {
		global $wp;
		/**
		 * Sets request to custom category archive page (checkout set_permalink_structure in set_up_before_class).
		 */
		$wp->request          = $this->primary_category->slug . '/';
		$non_primary_category = get_category( $this->categories[0] );

		$primary_category_posts     = get_posts(
			array(
				'category_name' => $this->primary_category->slug,
			)
		);
		$non_primary_category_posts = get_posts(
			array(
				'category_name' => $non_primary_category->slug,
			)
		);

		$this->assertCount( 1, $primary_category_posts );
		$this->assertCount( 0, $non_primary_category_posts );
	}

	/**
	 * Tests all category posts are being displayed on default archive page.
	 */
	public function test_primary_category_filter_does_not_work() {
		global $wp;
		/**
		 * Sets request to default category archive page (checkout add_permastruct in set_up_before_class).
		 */
		$wp->request          = 'category/' . $this->primary_category->slug . '/';
		$non_primary_category = get_category( $this->categories[0] );

		$primary_category_posts     = get_posts(
			array(
				'category_name' => $this->primary_category->slug,
			)
		);
		$non_primary_category_posts = get_posts(
			array(
				'category_name' => $non_primary_category->slug,
			)
		);

		$this->assertCount( 1, $primary_category_posts );
		$this->assertCount( 1, $non_primary_category_posts );
	}
}
