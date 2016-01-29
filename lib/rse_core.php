<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('RSE') ) :

	final class RSE {
		function __construct() {
			/* Do nothing here */
		}

		/**
			 * @method register_post_type
		 */

		static public function rse_initialize()
		{
			flush_rewrite_rules();
		}

		static public function rse_register_post_type() 
		{
			register_post_type(RSE_POST_TYPE, array(
				'label'             => __( RSE_POST_TYPE, 'rse' ),
				'labels'            => array(
					'name'               => _x( 'Events', 'post type general name', 'rse' ),
					'singular_name'      => _x( 'Event', 'post type singular name', 'rse' ),
					'menu_name'          => _x( 'Events', 'admin menu', 'rse' ),
					'name_admin_bar'     => _x( 'Events', 'add new on admin bar', 'rse' ),
					'add_new'            => _x( 'Add New Event', 'rse' ),
					'add_new_item'       => __( 'Add New Event', 'rse' ),
					'new_item'           => __( 'New Event', 'rse' ),
					'edit_item'          => __( 'Edit Event', 'rse' ),
					'view_item'          => __( 'View Event', 'rse' ),
					'all_items'          => __( 'All Events', 'rse' ),
					'search_items'       => __( 'Search Events', 'rse' ),
					'parent_item_colon'  => __( 'Parent Event:', 'rse' ),
					'not_found'          => __( 'No events found.', 'rse' ),
					'not_found_in_trash' => __( 'No events found in Trash.', 'rse' )
				),
				'public'            => true,
				'hierarchical'      => false,
				'has_archive'       => true,
				'menu_icon' => 'dashicons-calendar',
				'capability_type'   => 'post',
				'supports'          => array(
					'title',
					'thumbnail',
					'editor',
					'excerpt'
				),
				'rewrite'			=> array(
					'slug'			=> 'events'
				)
			));
		}

		static public function rse_register_taxonomy() 
		{
			register_taxonomy(RSE_TAXONOMY,	RSE_POST_TYPE, array(
				'rewrite' => array( 'slug' => RSE_TAXONOMY ),
				'hierarchical' => true,
				'labels' => array(
					'name' => _x( 'Event Types', 'taxonomy general name' ),
					'singular_name' => _x( 'Event Types', 'taxonomy singular name' ),
					'search_items' =>  __( 'Search Event Types' ),
					'all_items' => __( 'All Event Types' ),
					'edit_item' => __( 'Edit Event Type' ),
					'update_item' => __( 'Update Event Type' ),
					'add_new_item' => __( 'Add New Event Type' ),
					'new_item_name' => __( 'New Event Type Name' ),
					'menu_name' => __( 'Event Types' ),
				),
				'capabilities' => array(
					'manage__terms' => 'edit_posts',
					'edit_terms' => 'manage_categories',
					'delete_terms' => 'manage_categories',
					'assign_terms' => 'edit_posts'
				)
			));
		}

		static public function rse_enqueue_scripts()
		{
			global $post;
			if( is_admin() && isset($post) && $post->post_type == RSE_POST_TYPE) {
				wp_register_script('datetimepicker', RSE_PLUGIN_URL . 'assets/js/vendor/datetimepicker-master/build/jquery.datetimepicker.full.min.js', array('jquery'), RSE_PLUGIN_VERSION);
				wp_register_script('rse-scripts', RSE_PLUGIN_URL . 'assets/js/build/rse-scripts.js', array('jquery', 'datetimepicker'), RSE_PLUGIN_VERSION);
				wp_enqueue_script('rse-scripts');

				wp_register_style('jquery.datetimepicker', RSE_PLUGIN_URL . 'assets/js/vendor/datetimepicker-master/jquery.datetimepicker.css');
				wp_enqueue_style('jquery.datetimepicker');
				wp_register_style('rse-styles', RSE_PLUGIN_URL . 'assets/css/rse-styles.css');
				wp_enqueue_style('rse-styles');
			}
		}

		// replace tefault text editor with TinyMCE
		static public function rse_create_excerpt_box()
		{
			global $post;
			global $wpdb;
			$row = $wpdb->get_row("SELECT post_excerpt FROM $wpdb->posts WHERE id = $post->ID");
			$excerpt = $row->post_excerpt;
			wp_editor(
				$excerpt, 
				'excerpt',
				array(
					"media_buttons" => false,
					"teeny" => true,
					"quicktags" => false
				)
			);
		}

		static public function rse_replace_post_excerpt()
		{
			remove_meta_box('postexcerpt', RSE_POST_TYPE, 'normal');
			add_meta_box('postexcerpt', __('Excerpt'), 'RSE::rse_create_excerpt_box', RSE_POST_TYPE, 'normal');
		}

		static public function rse_save( $post_id )
		{	
			// Check if our nonce is set.
			if ( ! isset( $_POST['rse_event_meta_box_nonce'] ) )
				return $post_id;
	
			$nonce = $_POST['rse_event_meta_box_nonce'];
	
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'rse_event_meta_box' ) )
				return $post_id;
	
			// If this is an autosave, our form has not been submitted,
					//     so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				return $post_id;
	
			// Check the user's permissions.
			if ( RSE_POST_TYPE == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) )
					return $post_id;
			}
	
			/* OK, its safe for us to save the data now. */
	
			// Sanitize the user input.
			$rse_event_start_date = sanitize_text_field( $_POST['rse_event_start_date'] );
			$rse_event_end_date = sanitize_text_field( $_POST['rse_event_end_date'] );
			$rse_event_external_link = sanitize_text_field( $_POST['rse_event_external_link'] );
			$rse_expiry = sanitize_text_field( $_POST['rse_expiry'] );
			//prd($_POST);
			// Update the meta field.
			update_post_meta( $post_id, '_rse_event_start_date', $rse_event_start_date );
			update_post_meta( $post_id, '_rse_event_end_date', $rse_event_end_date );
			update_post_meta( $post_id, '_rse_event_external_link', $rse_event_external_link );
			if (isset($_POST['rse_event_all_day'])) {
				update_post_meta( $post_id, '_rse_event_all_day', $_POST['rse_event_all_day'] );
			}
			update_post_meta( $post_id, '_rse_expiry', $rse_expiry );

		}

		static public function rse_event_metabox_content( $post )
		{
			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'rse_event_meta_box', 'rse_event_meta_box_nonce' );
			$rse_event_start_date = get_post_meta( $post->ID, '_rse_event_start_date', true );
			$rse_event_end_date = get_post_meta( $post->ID, '_rse_event_end_date', true );
			$rse_event_external_link = get_post_meta( $post->ID, '_rse_event_external_link', true );
			$rse_event_all_day = get_post_meta( $post->ID, '_rse_event_all_day', true );
			$rse_expiry = get_post_meta( $post->ID, '_rse_expiry', true );
			include_once RSE_PLUGIN_DIR . "lib/rse_metabox_template.php" ;
		}
	
		static public function rse_init_meta_box($post_type)
		{
			if ( $post_type === RSE_POST_TYPE) {
				add_meta_box(
					'event_details',
					__( 'Event Details', 'rse' ),
					'RSE::rse_event_metabox_content',
					RSE_POST_TYPE
				);
			}
		}

		static public function rse_clean_old_events()
		{
			global $post;
	
			// DELETE OLD EVENTS, SLATED FOR DELETION
			$eventsArgs = array(
				"post_type" => RSE_POST_TYPE,
				"post_per_page" => -1,
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => '_rse_event_end_date',
						'value' => time(),
						'compare' => '<='
					),
					array(
						'key' => '_rse_expiry',
						'value' => "delete",
						'compare' => '='
					)
				)
			);
	
			$events = get_posts( $eventsArgs );
			
			foreach ( $events as $event ) {
				wp_trash_post( $event->ID );
			}
	
			// DRAFT OLD EVENTS, SLATED FOR DRAFT
			$eventsArgs = array(
				"post_type" => RSE_POST_TYPE,
				"post_per_page" => -1,
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => '_rse_event_end_date',
						'value' => time(),
						'compare' => '<='
					),
					array(
						'key' => '_rse_expiry',
						'value' => "draft",
						'compare' => '='
					)
				)
			);
	
			$events = get_posts( $eventsArgs );
	
			foreach ( $events as $event ) {
				$post = array( 'ID' => $event->ID, 'post_status' => "draft" );
				wp_update_post($post);
			}
		}
		
		static public function rse_single_template($single)
		{
			global $wp_query, $post;
		
			/* Checks for single template by post type */
			if ($post->post_type == RSE_POST_TYPE){
				if(file_exists(RSE_PLUGIN_DIR. 'lib/single-event.php')) {
					return RSE_PLUGIN_DIR . 'lib/single-event.php';
				}
			}
			return $single;
		}

		static public function rse_forumlate_args($args)
		{
			$eventsArgs = array(
				"post_type" => RSE_POST_TYPE,
				"post_per_page" => -1,
				'meta_key' => '_rse_event_end_date',
				'orderby' => 'meta_value',
				'order' => $args["order"],
				'meta_query' => array(
					array(
						'key' => '_rse_event_end_date',
						'value' => time(),
						'compare' => '>=',
						'type' => 'CHAR'
					)
				)
			);
			if (isset($args["type"])) {
				$eventsArgs['tax_query'] = array(
					array(
						'taxonomy' => RSE_TAXONOMY,
						'field' => 'slug',
						'terms' => array( $args["type"] ),
						'operator' => 'IN'
					)
				);
			}
			return $eventsArgs;
		}

		static public function rse_get_events($args = array("order" => "DESC","limit" => 99))
		{
			global $post;
			$eventsArgs = self::rse_forumlate_args($args);
			return new WP_query($eventsArgs);
		}

		static public function rse_init_shortcode($atts)
		{
			global $post;
			$args = array(
				"order" => $atts["order"],
				"type" => $atts["type"],
				"limit" => $atts["limit"]
			);

			$eventsArgs = self::rse_forumlate_args($args);
			ob_start();
			$eventsQuery = new WP_Query($eventsArgs);
			include_once RSE_PLUGIN_DIR . "lib/rse_event_list.php" ;
			return ob_get_clean();
		}
	}

	if( ! function_exists('rse_print_datetime') ) :
		function rse_print_datetime($date) {
			return date('l F j, Y \@ g:ia', $date);
		}
	endif;

	if( ! function_exists('rse_print_date') ) :
		function rse_print_date($date) {
			return date('l F j, Y', $date);
		}
	endif;

	if( ! function_exists('rse_print_time') ) :
		function rse_print_time($date) {
			return date('g:ia', $date);
		}
	endif;

	if( ! function_exists('the_event_start_date') ) :
		function the_event_start_date() {
			global $post;
			$date = get_post_meta( $post->ID, "_rse_event_start_date", true);
			echo rse_print_date($date);
		}
	endif;

	if( ! function_exists('the_event_end_date') ) :
		function the_event_end_date() {
			$date = get_post_meta( $post->ID, "_rse_event_end_date", true);
			echo rse_print_date($date);
		}
	endif;

	if( ! function_exists('rse_event_date') ) :
		function rse_event_date($postID = null, $divider = " | ") {
			global $post;
			if ($postID === null) {
				$postID = $post->ID;
			}

			$startDate = strtotime(get_post_meta( $postID, "_rse_event_start_date", true));
			$endDate = strtotime(get_post_meta( $postID, "_rse_event_end_date", true));

			if (get_post_meta( $postID, "_rse_event_all_day", true)) {
				echo rse_print_date($startDate);
			} else if ( date("j d Y", $startDate) === date("j d Y", $endDate ) ) {
				// same date?
				echo rse_print_date($startDate)  . $divider . rse_print_time($startDate) . ' &ndash; ' . rse_print_time($endDate);
			} else {
				// different dates?
				echo "From: " . rse_print_datetime($startDate) . "<br />";
				echo "To: " . rse_print_datetime($endDate);
			}
		}
	endif;
	if( ! function_exists('rse_event_link') ) :
		function rse_event_url($postID = null) {
			global $post;
			if ($postID === null) {
				$postID = $post->ID;
			}
			$eventLink = get_permalink($postID);
			$manualLink = get_post_meta( $postID, "_rse_event_external_link", true);
			if ( $manualLink ) {
				$eventLink = $manualLink;
			}
			return $eventLink;
		}
	endif;
	if( ! function_exists('echo_rse_event_link') ) :
		function rse_event_link($postID = null, $link_text = "Read more", $style = "inline") {
			global $post;
			if ($postID === null) {
				$postID = $post->ID;
			}
			if ($style === 'button') {
				$linkAttrs = 'class="rse_read_more rse_read_more_button"';
			} else {
				$linkAttrs = 'class="rse_read_more"';
			}
			$eventLink = get_permalink($postID);
			$manualLink = get_post_meta( $postID, "_rse_event_external_link", true);
			if ( $manualLink ) {
				$eventLink = $manualLink;
				$linkAttrs .= ' target="_blank"';
			}
			if ( $manualLink || $post->post_content ) {
				//prd($post->post_content);
				return '<a '.$linkAttrs.' href="'.$eventLink.'">'.$link_text.'</a>';
			}
		}
	endif;
	if( ! function_exists('rse_event_linked_title') ) :
		function rse_event_linked_title($postID = null, $link_text = "Title") {
			global $post;
			if ($postID === null) {
				$postID = $post->ID;
			}
			$linkAttrs = 'class="rse_read_more"';
			$eventLink = get_permalink($postID);
			$manualLink = get_post_meta( $postID, "_rse_event_external_link", true);
			if ( $manualLink ) {
				$eventLink = $manualLink;
				$linkAttrs .= ' target="_blank"';
			}
			if ( $manualLink || $post->post_content ) {
				return '<a '.$linkAttrs.' href="'.$eventLink.'">'.$link_text.'</a>';
			} else {
				return $link_text;
			}
		}
	endif;
endif;