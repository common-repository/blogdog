<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Blogdog Admin Class
 *
 * @since 5.0.0
 * @since 5.6.6 Add extends blogdog.
 */
 
class blogdog_admin extends blogdog {

	/**
	 * init Blogdog admin class.
	 *
	 * @since 5.0.0
	 */
	 
	public static function init() {
        $class = __CLASS__;
        new $class;
    }
	
	/**
	 * Constructor
	 *
	 * @since 5.0.0
	 */
	 
	public function __construct() {
		
		/**
		 * Add Blogdog Admin Page
		 *
		 * @since 5.0.0
		 */
		 
		add_action( 'admin_menu', array( $this, 'blogdog_add_pages' ) );
		
		/**
		 * Enqueue Admin Scripts and styles
		 *
		 * @since 5.0.0
		 */
		 
		add_action( 'admin_enqueue_scripts', array( $this, 'blogdog_enqueue_admin_scripts' ) );
		
		/**
		 * Regisister Ajax Process
		 *
		 * @since 5.0.0
		 */
		 
		add_action( 'wp_ajax_blogdog_ajax_process', array( $this, 'blogdog_ajax_process' ) );

		add_action( 'wp_ajax_blogdog_load_admin_locations', array( $this, 'blogdog_load_admin_locations' ) );

		add_action( 'blogdog_admin_data', array( $this, 'blogdog_admin_dasnboard' ) );
		add_action( 'blogdog_admin_data', array( $this, 'blogdog_admin_settings' ) );
		add_action( 'blogdog_admin_data', array( $this, 'blogdog_admin_locations' ) );

		if( class_exists( 'iFound' ) || class_exists( 'ProFoundMLS' ) ) {
			add_action( 'admin_instructions', array( $this, 'add_admin_instructions' ) );
		}

	}
	
	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since 5.0.0
	 * @since 6.0.0 Register scripts rather than enqueue scripts.
	 */
	
	public function blogdog_enqueue_admin_scripts() {
		
		/** Admin UI Styles  */
		wp_register_style( 'jquery-ui-custom', plugins_url( 'css/jquery-ui-custom.css', __FILE__  ), array(), BLOGDOG_PLUGIN_VERSION );
		
		/** 
		 * Font Awesome Styles  
		 *
		 * @since v5.6.6
		 */
		wp_register_style( 'font-awesome', plugins_url( 'font-awesome/css/font-awesome.min.css', __FILE__  ) );
		
		/** Admin styles  */
		wp_register_style( 'blogdog_admin_styles', plugins_url( 'css/style.css', __FILE__ ), array(), BLOGDOG_PLUGIN_VERSION );
		
		/** Enqueue Admin js for ajax */
		wp_register_script( 'blogdog_js', plugins_url( 'js/admin.js', __FILE__ ), array ( 'jquery' ), BLOGDOG_PLUGIN_VERSION, true );
		
		/** Connect script to ajax */
		wp_localize_script( 'blogdog_js', 'blogdog_url', array(
			'ajax_url' 		=> admin_url( 'admin-ajax.php' ),
			'nonce' 		=> wp_create_nonce( 'blogdog_secure_me' )
		));

	}
	
	/**
	 *  Blogdog admin page 
	 *
	 * 	Add admin page tab to main menu
	 *
	 * 	@since 5.0.0
	 * 	@since 5.2.0 Changed to add custom menu icon.
	 */
	 
	public function blogdog_add_pages() {
   		
		/** Add a new submenu under Posts: */
		add_menu_page(
			__( 'Auto Content','blogdog' ), 
			__( 'Auto Content','blogdog' ), 
			'manage_options', 
			'blogdog', 
			array( 
				$this, 
				'blogdog_admin_tabs' 
			),
			plugins_url( 'images/blogdog.png', __FILE__ ),
			4
		);
		
	}
	
	/**
	 * Blogdog admin tabs 
	 *
	 * Add tabs to admin page
	 *
	 * @since 5.2.0
	 * @since 6.0.0 Enqueue Refistered Scripts only on this page.
	 */
	 
	public function blogdog_admin_tabs() {
		
		/** must check that the user has the required capability */
		if ( ! current_user_can( 'manage_options' ) ) {
		  	wp_die( __( 'You do not have sufficient permissions to access this page.') );
		} 

		wp_enqueue_style( 'jquery-ui-custom' );
		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'blogdog_admin_styles' );
		
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-button' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'blogdog_js' ); ?>

		<div id="blogdog_admin" class="wrap">
		
			<h2><?php _e( 'Automated Content', 'blogdog' ) ?></h2>
			
			<div id="tabs">
				 
				<ul><?php

					echo $this->get_admin_tabs(); ?>
					
				</ul><?php

				do_action( 'blogdog_admin_data' ); ?>	
			
			</div>
			
			<?php /** @since 5.3.2 */ ?>
			<?php do_action( 'admin_instructions' ); ?>
			
		</div><?php

	}

	/**
	 * Get admin tabs.
	 *
	 * @return bool
	 */

	public function get_admin_tabs() {

		if ( $tabs = get_option( 'blogdog_tabs' ) ) {
			return $tabs;
		}

		$tabs = $this->blogdog_GET( 'parts/', array( 'tabs' ) );

		update_option( 'blogdog_tabs', $tabs, 'no' );

		return $tabs;

	}

	/**
	 *  Blogdog admin dashboard
	 *
	 * 	Dashboard tab on admin page
	 *
	 * 	@since 5.2.0
	 *  @since 5.6.6 Next upload removed due to wp cron being removed.
	 */
	 
	public function blogdog_admin_dasnboard() {
		
		/** Determine the status of the api */
		if( get_option( 'blogdog_api_active' ) == 'api_active' ) {
			
			$checked = 'checked';
		
		} else {
			
			$checked = '';
			
		}
		
		$upcoming_posts = $this->upcoming_posts(); ?>

		<div id="tabs-1">

			<div class="metabox-holder widget">
				
				<div class="postbox">

					<div class="inside">	 
						
						<div class="blogdog_heading">
							<?php _e( 'API Status: ', 'blogdog' ); ?>
						</div>
						
						<div class="blogdog_options">
							
							<label id="blogdog_activate_api" class="switch">
								<input type="checkbox" id="switch_checkbox" <?php echo $checked; ?>>
								<div class="slider round"></div>
							
							</label>
							
							<i class="fa fa-fw api_message" aria-hidden="true"></i>
							<span id="api_message" class="red"></span>
						
						</div>
					
					</div>
					
					<div class="clear"></div>
					
					<?php if( $upcoming_posts ) { ?>
							
						<div class="inside">		
								
							<div class="blogdog_heading">
								<label><?php _e( 'Upcoming Posts: ' , 'blogdog' ); ?></label>
							</div>
									
							<div class="blogdog_options">
								<?php echo $upcoming_posts; ?>
							</div>
					
						</div>
							
						<div class="clear"></div>
						
					<?php } ?>
							
				</div>
			
			</div>

		</div><?php

	}
	
	/**
	 *  Upcoming Posts
	 *
	 * 	Get a list of upcoming posts
	 *
	 * 	@since 5.6.6
	 *  @return string $output The list of upcoming page links.
	 */
	
	public function upcoming_posts() {

    	$the_query = new WP_Query(array(
			'post_status' 		=> 'future',
			'posts_per_page' 	=> 10,
			'orderby' 			=> 'date',
			'order' 			=> 'ASC'
		));

		if ( $the_query->have_posts() ) {

			$output .= '<ul>';

			while ( $the_query->have_posts() ) {

				$the_query->the_post();
				
				$output .= '<li>';
				$output .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">';
				$output .= get_the_title();
				$output .= '</a>';
				$output .= '</li>';

			}

			$output .= '</ul>';

		}

		wp_reset_postdata();

		return $output;

	}

	/**
	 *  Blogdog admin settings
	 *
	 * 	Settings tab on admin page
	 *
	 * 	@since 5.2.0 
	 */
	
	public function blogdog_admin_settings() {
		
		$settings 	= get_option( 'blogdog_api_settings' );
		$api_key 	= get_option( 'blogdog_api_key' ); ?>

		<div id="tabs-2">

			<form method="post" id="blogdog_api_settings_form">
			
				<div class="metabox-holder">
					
					<div class="postbox">	
				
						<h2 class="hndle">
							<?php _e( 'API Settings' , 'blogdog' ); ?>
						</h2>
								
						<div class="inside">

							<table class="form-table" style="white-space: nowrap">

								<tbody>

									<tr>
									
										<th scope="row"><label for="api_key"><?php _e( 'API Key: ' , 'blogdog' ); ?></label></th>
											
										<td>
											<input type="text" name="api_key" id="api_key" value="<?php echo $api_key; ?>" class="regular-text"/>
										</td>
										
									</tr>

								</tbody>

							</table>
							
						</div>
								
					</div>
				
				</div>
				
				<div class="metabox-holder">
					
					<div class="postbox">
						
						<h2 class="hndle">
							<?php _e( 'User Settings' , 'blogdog' ); ?>
						</h2>
							
						<div class="inside">
							
							<table class="form-table" style="white-space: nowrap">

								<tbody>

									<tr>
									
										<th scope="row"><label for=""><?php _e( 'Post Author: ', 'blogdog' ); ?></label></th>
								
										<td><?php 
								
											$args = array(
												'selected'	=> get_option( 'blogdog_author_id' ),
												'name'     	=> 'author_id',
												'id'     	=> 'blogdog_author_id',
												'class'  	=> 'blogdog_author_id'
											);
											wp_dropdown_users( $args ); ?>
										</td>

									</tr>

									<tr>
								
										<th scope="row"><label for=""><?php _e( 'Agent Type: ', 'blogdog' ); ?></label></th>
								
										<td>
								
											<select id="blogdog_agent_type" name="agent_type">
									
												<option value="Agent" <?php if( $settings['agent_type'] == 'Agent' ) echo 'selected'; ?>>
													<?php _e( 'Agent', 'blogdog' ); ?>
												</option>
										
												<option value="Team" <?php if( $settings['agent_type'] == 'Team' ) echo 'selected'; ?>>
													<?php _e( 'Team', 'blogdog' ); ?>
												</option>
										
												<option value="Broker" <?php if( $settings['agent_type'] == 'Broker' ) echo 'selected'; ?>>
													<?php _e( 'Broker', 'blogdog' ); ?>
												</option>
										
												<option value="Group" <?php if( $settings['agent_type'] == 'Group' ) echo 'selected'; ?>>
													<?php _e( 'Group', 'blogdog' ); ?>
												</option>
										
											</select>
									
										</td>

									</tr>

									<tr>
								
										<th scope="row">
											<label for="">
												<span id="agent_name_type"><?php _e( $settings['agent_type'], 'blogdog' ); ?> </span>
												<?php _e( 'Name: ', 'blogdog' ); ?>
											</label>
										</th>
								
										<td><input type="text" id="agent_name" name="agent_name" value="<?php echo $settings['agent_name']; ?>" class="regular-text"/></td>							
										<td><?php _e( 'This name will appear in blog posts.', 'blogdog' ); ?></td>

									</tr>
											
								</tbody>

							</table>

						</div>
					
					</div>
					
				</div>
				
				<div id="blogdog_api_settings" class="save-changes button button-primary">
					<i class="fa fa-database" aria-hidden="true"></i>
					<?php esc_attr_e( 'Save Changes' ) ?>
				</div>
				
				<i class="fa fa-pulse fa-fw fa-2x response"></i>
			
			</form>

		</div><?php

	}

	/**
	 *  Blogdog admin locations
	 *
	 * 	Locations tab on admin page
	 *
	 * 	@since 5.2.0
	 *  @since 6.0.0 Loads a spinner. Locations will be loaded with ajax. @see blogdog_admin::blogdog_load_admin_locations()
	 */
	
	public function blogdog_admin_locations() { ?>

		<div id="tabs-3"><?php

			echo $this->get_locations_html( 'form' ); ?>
								
		</div><?php

	}

	/**
	 *  Blogdog Load Admin Locations
	 *
	 * 	Loads locations under tab on admin page with ajax.
	 *
	 * 	@since 6.0.0 
	 */
	
	public function blogdog_load_admin_locations() {
		
		check_ajax_referer( 'blogdog_secure_me', 'blogdog_ajax_nonce' );

		$locations = $this->get_locations_html( 'admin' );

		echo json_encode( $locations );

		die();

	}
	
	/**
	 * Process AJAX to activate API
	 *
	 * Process AJAX to make sure set-up is complete before API access is allowed. 
	 *
	 * @since 5.0.1
	 * @since 5.3.0 Added the option blogdog_api_active to prevent cron clear schudlue at plugin update.
	 * @since 5.6.6 Cron scheduling is replaced with REST API.
	 *
	 * @access private
	 * @return array $response Current API set-up status.
	 */
	
	private function blogdog_activate_api() {
		
		$response = array();
		$response['code'] = '#api_message';
			
		if( get_option( 'blogdog_api_active' ) == 'api_active' ) {

			$this->clear_options();

			/** Update option to database. */
			update_option( 'blogdog_api_active', 'api_inactive' );
			
			$response['html'] = 'API Deactivated';
			$response['checked'] = '';
			
		} else {
			
			if( ! get_option( 'blogdog_api_key' ) ) $response['html'] = 'API Key Required';
			else 									$response['html'] = 'API Activated';
			
			if( $response['html'] == 'API Activated' ) {
				
				/** Update option to database. */
				update_option( 'blogdog_api_active', 'api_active' );
				
				$response['checked'] = 'checked';
			
			} else $response['checked'] = '';
			
		}
		
		return $response;
		
	}
	
	/**
	 *  Clean Locations
	 *
	 * 	Sanitize data from the locations ajax.
	 *
	 * 	@since 5.2.0 
	 *
	 * @param array $post Array of location settings.
	 */
	
	public function clean_locations( $post ) {
		
		$clean_locations = array();

   	 	parse_str( $post, $inputs );
		
		foreach( $inputs as $key => $values ) {

			foreach( $values as $section => $value ) {
			
				$locations[$section][$key] = $value;
	
			}

		}

		foreach( $locations as $location ) {

			if( ! empty( $location['city'] ) ) {
				
				$clean_locations[] = array(
					'deactivate'	=> sanitize_text_field( $location['deactivate'] ),
					'type'			=> sanitize_text_field( $location['type'] ),
					'city'			=> sanitize_text_field( $location['city'] ),
					'sub'			=> sanitize_text_field( $location['sub'] ),
					'zip' 			=> $this->sanitize( $location['zip'] ),
					'ptype'			=> sanitize_text_field( $location['ptype'] ),
					'min' 			=> sanitize_text_field( $location['min'] ),
					'max'			=> sanitize_text_field( $location['max'] ),
					'options'		=> $this->sanitize( $location['options'] )
				);
				
			}

		}
		
		$body = array( 'locations' => $clean_locations );

		delete_option( 'blogdog_locations_admin' );

		$this->blogdog_POST( 'update/', array( 'body' => $body ) );

	}
	
	/**
	 * Process AJAX
	 *
	 * Process all AJAX requests. Action depends on request type. Creates city select, zipcode checkboxes
	 * and updates action locations in options database.
	 *
	 * @since 5.0.0
	 *
	 * @param array $_REQUEST {
	 *		Depends on request type.
	 *
	 *		@type string 'ID' The name of the id received.
	 *		@type string 'state' name of selected state.
	 *		@type int 'section' ID of location 	section.
	 * }
	 */
	
	public function blogdog_ajax_process() {
		
		check_ajax_referer( 'blogdog_secure_me', 'blogdog_ajax_nonce' );
		
		$response = array();
		
		/** 
		 * Create zipcode checkboxes
		 *
		 * @since 5.0.0
		 */
		
		if( $_REQUEST['ID'] === 'blogdog_city' ) {

			$response['html'] = $this->blogdog_GET( 'zipcodes/', array( urlencode( $_REQUEST['city'] ), $_REQUEST['section'] ) );

			$response['code'] = '#blogdog_zipcode_' . $_REQUEST['section'];
			
			echo json_encode( $response );
			
			die();
		
		}
		
		/** 
		 * API Activation
		 *
		 * @since 5.0.1
		 */
		 
		elseif( $_REQUEST['ID'] === 'switch_checkbox' ) {
			
			$response = $this->blogdog_activate_api();
			
			echo json_encode( $response );
			
			die();
		
		}
		
		/** 
		 * Insert Api settings.
		 *
		 * @since 5.2.0
		 */
		
		elseif( $_REQUEST['ID'] === 'blogdog_api_settings' ) {
			
   	 		parse_str( $_REQUEST['post'], $post );
			
			$settings = array(
				'agent_name'		=> sanitize_text_field( $post['agent_name'] ),
				'agent_type' 		=> sanitize_text_field( $post['agent_type'] )
			);

			update_option( 'blogdog_api_settings', $settings );

			$api_key = sanitize_text_field( $post['api_key'] );

			update_option( 'blogdog_api_key', $api_key );

			update_option( 'blogdog_author_id', absint( $post['author_id'] ) );

			// Do this last to insure the  database has been already updated.
			$body = array( 'settings' => $settings );

			$this->blogdog_POST( 'update/', array( 'body' => $body ) );
			
			die();
		
		}
		
		/** 
		 * Insert Location settings.
		 *
		 * @since 5.2.0
		 */
		
		elseif( $_REQUEST['ID'] === 'blogdog_locations' ) {
			
			$this->clean_locations(  $_REQUEST['post'] );
			
			echo json_encode( $response );
			
			die();
		
		}
		
		/** 
		 * Add city or subdivision location.
		 *
		 * @since 5.2.0
		 */
		
		elseif( $_REQUEST['ID'] === 'add_city' || $_REQUEST['ID'] === 'add_sub' ) {
			
			$type = ( $_REQUEST['ID'] === 'add_city' ) ? 'city' : 'sub';
			
			$response['html'] = $this->get_locations_html( $type );
			$response['append'] = '#sortable';
			
			echo json_encode( $response );
			
			die();
		
		}
		
		die();
	}

	/**
	 * Get locations HTML.
	 *
	 * @param $type
	 *
	 * @return mixed
	 */

	public function get_locations_html( $type ) {

		if ( $locations_html = get_option( "blogdog_locations_{$type}" ) ) {
			return $locations_html;
		}

		$locations_html = $this->blogdog_GET( 'locations/', array( $type ) );

		update_option( "blogdog_locations_{$type}", $locations_html, 'no' );

		return $locations_html;

	}
	
	/**
	 * Sanitize
	 *
	 * Sanitize text fields inputs in an array.
	 *
	 * @since 5.0.0
	 *
	 * @param array $input Input varies depneding on array.
	 *
	 * @return array|void $new_input Sanitized array.
	 */
	
	public function sanitize( $input ) {
		
		if( empty( $input ) ) {
			return;
		}

		// Initialize the new array that will hold the sanitize values
		$new_input = array();
		
		// Loop through the input and sanitize each of the values			
		foreach ( $input as $key => $val ) {								
			
			$new_input[ $key ] = ( isset( $input[ $key ] ) ) ?					
				sanitize_text_field( $val ) :					
				'';					
		
		}
							
		return $new_input;				
	}

	/**
	 * I Found Agent Extended Admin Instructions
	 *
	 * Add advertisement and instructions in the WP Admin for optimizing this plugin.
	 *
	 * @since 6.0.0
	 */
	
	public function add_admin_instructions() { ?>
		
		<div class="metabox-holder">
				
			<div class="postbox">
	
				<div class="inside">

					<div class="admin-one-half">

						<h2><?php esc_html_e( 'Optimizing Automated Content' ); ?></h2>

						<p><?php esc_html_e( 'Focus on specific areas.' ); ?></p>
						<p><?php esc_html_e( 'Only choose options that exsist in those specific areas.' ); ?></p>
						<p><?php esc_html_e( 'Verify the criteria you choose has listings availavle by searching your website.' ); ?></p>
						<p><?php esc_html_e( 'Do not use broad criteria on the same location tab. Make additional, more specific location tabs.' ); ?></p>
						<p><?php esc_html_e( 'Add social media sharing to your website. (optional but highly recommended)' ); ?></p>

					</div>

					<div class="admin-one-half">

						<video width="100%" poster="https://ifoundagent.com/wp-content/uploads/automated-content/video/poster.png" class="admin-video" controls>
							<source src="https://ifoundagent.com/wp-content/uploads/automated-content/video/IFA-Automated-Content.mp4" type="video/mp4">
							Your browser does not support the video tag.
						</video>

					</div>
									
					<div class="clear"></div>

				</div>
					
			</div>
		
		</div><?php
		
	}
	
}

/**
 * @see blogdog_admin::init()
 *
 */ 
add_action( 'plugins_loaded', array( 'blogdog_admin', 'init' ) );
