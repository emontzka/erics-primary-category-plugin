<?php

/**
 * Class Primary_Category_Setup
 *
 * Set up plugin page for user to select category to designate as Primary.
 *
 * @package    Erics_Primary_Category
 * @subpackage Eric's_Primary_Category/includes
 * @author     Eric Montzka <emontzka@sbcglobal.net>
 * @since      1.0.0
 **/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Primary_Category_Setup' ) ) {

	class Primary_Category_Setup {

		/**
		 * Empty constructor
		 */
		public function __construct() {
			/* Leave this empty */
		}

		/**
		 * Registers hooks to initialize plugin.
		 */
		protected static function _add_actions() {
			add_action( 'add_meta_boxes', array( __CLASS__, 'category_metabox' ) );
			add_action( 'save_post', array( __CLASS__, 'save_primary_category' ) );
		}

		/**
		 * Adds metabox to sidebar of post admin.
		 */
		public static function category_metabox() {
			add_meta_box( 'primary_category_box', __( 'Choose Primary Category', 'primary-category' ), array( __CLASS__, 'render_metabox' ), 'post', 'side' );
		}

		/**
		 * Renders html for metabox and
		 *
		 * @param object $post
		 */
		public static function render_metabox( $post ) {
			$the_post_meta   = get_post_meta( $post->ID, 'primary-category' );
			$categories      = wp_get_post_categories( $post->ID );
			$is_none_checked = empty( $the_post_meta ) ? 'checked' : '';

			// Create nonce for metabox
			wp_create_nonce( 'category-metabox' );

			// Don't allow primary category selection if not enough categories and none is currently selected.
			if ( count( $categories ) < 2 && $is_none_checked ) {
				echo 'Post must have two or more categories to select a primary category.';
			} else {
				// Output category checkboxes
				?>
				<div>
					<label for="none">
						  <input <?php echo $is_none_checked; ?> type="radio" id="none" name="primary-category" value="none"
								<?php if ( ! isset( $the_post_meta ) ) {echo 'checked';} ?> />
						  None
					</label>
				</div>
				<?php
				foreach ( $categories as $category ) {
					$cat_object = get_term( $category, 'category', OBJECT );
					$checked    = ( ! empty( $the_post_meta ) && $the_post_meta[0] == $cat_object->term_id ) ? 'checked' : '';
					?>
				<div>
					<label for="<?php echo $cat_object->slug; ?>">
					  <input <?php echo $checked; ?>  type="radio" id="<?php echo $cat_object->slug; ?>" name="primary-category" value="<?php echo $cat_object->term_id; ?>" />
					  <?php echo $cat_object->name; ?>
					</label>
				</div>
					<?php
				}
			}
		}

		/**
		 * Save meta value to database
		 *
		 * @param integer $post_id
		 */
		public static function save_primary_category( $post_id ) {
			$is_nonce_valid = ( isset( $_POST['category-metabox'] ) && wp_verify_nonce( $_POST['category-metabox'] ) ) ? 'true' : 'false';
			if ( ! $is_nonce_valid ) {
				return;
			}
			if ( isset( $_POST['primary-category'] ) && $_POST['primary-category'] !== 'none' ) {
				update_post_meta( $post_id, 'primary-category', $_POST['primary-category'] );
			} else {
				delete_post_meta( $post_id, 'primary-category' );
			}
		}

		/**
		 * Initialize class
		 */
		public static function run() {
			static $run = null;

			if ( null === $run ) {
				$run = new self();
				self::_add_actions();
			}
		}
	}
}
