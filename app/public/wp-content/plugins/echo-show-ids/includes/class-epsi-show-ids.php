<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main class to provide this feature.
 *
 * @copyright   Copyright (C) 2016, Zraly Studio
 */
class EPSI_Show_IDs {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_ID_to_columns_list' ) );
		add_action( 'admin_init', array( $this, 'add_ID_to_action_rows' ) );
	}


	/***************************************************************************************************

										Show ID in Column

	 ***************************************************************************************************/

	/**
	 * Add columns to all relevant lists
	 */
	public function add_ID_to_columns_list() {

		// List of Posts / CPTs
		add_filter( 'manage_post_posts_columns', array( $this, 'add_column_heading' ), 99, 1 );
		add_action( 'manage_post_posts_custom_column', array( $this, 'add_column_value' ), 99, 2 );

		// List of Pages / CPTs
		add_filter( 'manage_page_posts_columns', array( $this, 'add_column_heading' ), 99, 1 );
		add_action( 'manage_page_posts_custom_column', array( $this, 'add_column_value' ), 99, 2 );

		// List of Comments
		add_filter( 'manage_edit-comments_columns', array( $this, 'add_column_heading' ), 99, 1 );
		add_action( 'manage_comments_custom_column', array( $this, 'add_column_value' ), 99, 2 );

		// List of Categories
		add_action( 'manage_edit-link-categories_columns', array( $this, 'add_column_heading' ), 99, 1 );
		add_filter( 'manage_link_categories_custom_column', array( $this, 'add_column_value_new' ), 99, 3 );

		// List of Users
		add_action( 'manage_users_columns', array( $this, 'add_column_heading' ), 99, 1 );
		add_filter( 'manage_users_custom_column', array( $this, 'add_column_value_new' ), 99, 3 );

		// Media List
		add_filter( 'manage_media_columns', array( $this, 'add_column_heading' ), 99, 1 );
		add_action( 'manage_media_custom_column', array( $this, 'add_column_value' ), 99, 2 );

		// List of Custom Taxonomies
		foreach ( get_taxonomies( array( 'public' => true ), 'names') as $custom_taxonomy ) {
			if ( ! empty( $custom_taxonomy ) ) {
				add_action( "manage_edit-" . $custom_taxonomy . "_columns", array( $this, 'add_column_heading' ), 99, 1 );
				add_filter( "manage_" . $custom_taxonomy . "_custom_column", array( $this, 'add_column_value_new' ), 99, 3 );
			}
		}

		// List of Custom Post Types
		foreach (get_post_types( array( 'public' => true, '_builtin' => false), 'names') as $custom_post_type) {
			if ( ! empty( $custom_post_type ) ) {
				add_action( "manage_edit-". $custom_post_type . "_columns", array( $this, 'add_column_heading' ), 99, 1) ;
				add_filter( "manage_". $custom_post_type . "_posts_custom_column", array( $this, 'add_column_value' ), 99, 2 );
			}
		}
	}

	public function add_column_heading( $columns ) {

		// check if we should display the list of IDs
		if ( ! $this->epsi_is_option_on( 'where_to_display_ids', 'epsi-show-column' ) ) {
			return $columns;
		}

		$columns = empty($columns) ? array() : $columns;
		return array_merge( $columns, array( 'epsi_column_id' => 'ID' ) );
	}

	public function add_column_value( $column_name, $post_id ) {
		if ( ! empty( $column_name ) && $column_name == 'epsi_column_id' && $this->epsi_is_option_on( 'where_to_display_ids', 'epsi-show-column' ) ) {
			echo $post_id;
		}
	}

	public function add_column_value_new($value, $column_name, $post_id) {
		return ( $column_name == 'epsi_column_id' && $this->epsi_is_option_on( 'where_to_display_ids', 'epsi-show-column' ) ) ? $post_id : $value;
	}


	/***************************************************************************************************

							Show ID in Action Rows

	 ***************************************************************************************************/

	/**
	 * Add ID 'action' to relevant lists
	 */
	public function add_ID_to_action_rows() {

		add_filter( 'post_row_actions', array( $this, 'add_post_id' ), 10, 2 );

		add_filter( 'page_row_actions', array( $this, 'add_post_id' ), 10, 2 );

		add_filter( 'cat_row_actions', array( $this, 'add_term_id' ), 10, 2 );

		add_filter( 'tag_row_actions', array( $this, 'add_term_id' ), 10, 2 );

		add_filter( 'comment_row_actions', array( $this, 'add_comment_id' ), 10, 2 );

		add_filter( 'media_row_actions', array( $this, 'add_post_id' ), 10, 2 );

		add_filter( 'user_row_actions', array( $this, 'add_post_id' ), 10, 2 );
	}

	public function add_post_id( $actions, $post ) {
		return $this->add_to_row_actions( $actions, $post->ID );
	}

	public function add_term_id( $actions, $category ) {
		return $this->add_to_row_actions( $actions, $category->term_id );
	}

	public function add_comment_id( $actions, $comment ) {
		return $this->add_to_row_actions( $actions, $comment->comment_ID );
	}

	private function add_to_row_actions( $actions, $id ) {

		if ( ! empty( $actions ) && $this->epsi_is_option_on( 'where_to_display_ids', 'epsi-show-inline' ) ) {

			end( $actions );
			$key = key($actions);
			reset( $actions );

			$actions[$key] .=  '<span class="epsi-show-id"> | ID: ' . esc_attr( $id ) . ' </span>';
		}

		return $actions;
	}

	/*******************    OTHER   ********************/

	/**
	 * Get saved settings option.
	 * 
	 * @param $option_name
	 * @param $option_value
	 *
	 * @return bool
	 */
	private function epsi_is_option_on( $option_name, $option_value ) {

		if ( empty( epsi_get_instance()->settings ) ) {
			return false;
		}

		$show_ids_settings = epsi_get_instance()->settings->get_value( $option_name );

		return ! empty( $show_ids_settings[$option_value] );
	}
}