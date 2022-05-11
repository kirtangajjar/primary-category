<?php
/**
 * Plugin Name:       10 Up Primary Category
 * Description:       A plugin to add a primary category to posts
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Kirtan Gajjar
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       10up-primary-category
 *
 * @package           10up_Primary_Category
 */

define( '_10UP_PRIMARY_CATEGORY_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( '_10UP_PRIMARY_CATEGORY_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
const _10UP_PRIMARY_CATEGORY_META_KEY = '_10up_primary_category';

/**
 * Register primary category as a meta field.
 */
add_action(
	'init',
	function() {
		register_post_meta(
			'',
			_10UP_PRIMARY_CATEGORY_META_KEY,
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
);

/**
 * Ensure that only posts having the primary category gets displayed on custom category page.
 */
add_action(
	'pre_get_posts',
	function ( $query ) {
		global $wp, $wp_rewrite;

		$category_name        = $query->get( 'category_name' );
		$category_permastruct = $wp_rewrite->get_category_permastruct();

		if ( ! $category_name ) {
			return;
		}

		$category_permalink = str_replace( '/%category%', '', $category_permastruct );
		$current_url        = home_url( $wp->request );

		/**
		 * We don't want to alter the behaviour of the main category archives page.
		 * However, for other pages, we would like to filter the posts to only show posts with the primary category.
		 */
		if ( $current_url &&
			$category_permalink &&
			false === strpos( $current_url, $category_permalink ) &&
			$query->is_main_query()
		) {
			$query->set(
				'meta_query',
				array(
					'relation' => 'AND', // Use AND for taking result on both condition true.
					array(
						'key'   => _10UP_PRIMARY_CATEGORY_META_KEY,
						'value' => $category_name,
					),
				)
			);
		}
	}
);

/**
 * Modify the permalink of posts to include use the primary category if present.
 */
add_action(
	'post_link_category',
	function ( \WP_Term $cat, array $cats, \WP_Post $post ) {
		$primary_category = get_post_meta( $post->ID, _10UP_PRIMARY_CATEGORY_META_KEY, true );
		if ( ! empty( $primary_category ) ) {
			return get_category_by_slug( $primary_category );
		}
		return $cat;
	},
	1,
	3
);

add_action(
	'admin_enqueue_scripts',
	function () {
		$asset_file_path = _10UP_PRIMARY_CATEGORY_PLUGIN_PATH . '/build/index.asset.php';
		$asset           = is_readable( $asset_file_path ) ? require $asset_file_path : array();

		wp_register_script(
			'10up-primary-category-block-script',
			_10UP_PRIMARY_CATEGORY_PLUGIN_URL . '/build/index.js',
			array_merge( $asset['dependencies'], array( 'react', 'react-dom', 'wp-edit-post' ) ),
			filemtime( _10UP_PRIMARY_CATEGORY_PLUGIN_PATH . '/build/index.js' ),
			true
		);
		wp_enqueue_script( '10up-primary-category-block-script' );
	}
);
