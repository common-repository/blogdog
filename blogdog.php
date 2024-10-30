<?php
/*
Plugin Name: Automated Content for Real Estate
Plugin URI:  https://reblogdog.com
Description: Add automated real estate content to your website.
Version:     9.3.1
Author:      reblogdog
Author URI:  https://reblogdog.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: blogdog

blogdog is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
blogdog is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with blogdog. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! defined( 'BLOGDOG_PLUGIN_VERSION' ) ) {
    define( 'BLOGDOG_PLUGIN_VERSION', '9.3.1' );
}

/**
 * Require if admin.
 */
if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );
} 
	
/**
 * Blogdog Class
 *
 * @since 5.0.0
 */
 
class blogdog {
	
	/**
	 * init blogdog class.
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

		register_activation_hook( __FILE__, array( $this, 'blogdog_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'blogdog_deactivation' ) );

		add_action( 'init', array( $this, 'blogdog_check_version' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'blogdog_scripts' ) );
		add_action( 'init', array( $this, 'blogdog_depricated' ) );
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
		add_action( 'blogdog_content_publish', array( $this, 'blogdog_content_publish'), 10, 1 );
		
	}
	
	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 5.0.0
	 */
	
	public function blogdog_scripts(){
		wp_enqueue_style( 'blogdog_style', plugins_url( '/css/style.css', __FILE__ ), array(), BLOGDOG_PLUGIN_VERSION );
	}
	
	/**
	 * Register Routes
	 *
	 * Register the REST API Route.
	 *
	 * @since 5.6.6
	 * @since 6.0.8 Add check version endpoint.
	 *
	 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 */
	
	public function register_route() {
			
		register_rest_route( 
			'blogdog/1.0.0', 
			'/blogdog-push/', 
			array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'blogdog_PUSH' )
			)
		);
		
	}

	/**
	 * Blogdog PUSH.
	 *
	 * Receive Push for article.
	 *
	 * @since 8.0.0
	 *
	 * @param  array  $request WP_REST_Request.
	 * @return array  $success
	 */
	
	public function blogdog_PUSH() {

		self::log( 'push', 'API Push Initiated' );

		/** Check to see if blogdog API is active */
		if( get_option( 'blogdog_api_active' ) === 'api_active' ) {

			self::log( 'push', 'API Active' );

			/** Check to see if plugin is set up */
			if( $api_key = get_option( 'blogdog_api_key' ) ) {

				self::log( 'push', 'API Key Present' );

				/** Check the Blogdog API key matches. */
				if( 0 === strcmp( $_SERVER['HTTP_AUTHENTICATION'], $api_key ) ) {

					self::log( 'push', 'API Key Match' );

					$token = intval( $_POST['token'] );

					/** Check if the token exsists */
					if( is_int( $token ) && strlen( $token ) === 16 ) {

						self::log( 'push', "Token Match" );

						$ID = intval( $_POST['ID'] );

						if( is_int( $ID ) && $ID > 0 ) {

							self::log( 'push', "Valid ID: $ID" );

							if( wp_schedule_single_event( time() + rand( 60, 400 ), 'blogdog_content_publish', array( $token ) ) ) {
								self::log( 'push', "Event Scheduled" );

								return [ 'success' => true ];
							}

						}

					}

				}

			}
			
		}

		return [ 'success' => false ];
		
	}

	public function blogdog_content_publish( $token ) {

		$the_post = $this->blogdog_GET( 'content/', array( $token ) );

		$this->blogdog_publish( $the_post );

	}

	/**
	 * blogdog GET
	 *
	 * Send GET request to blogdog api.
	 *
	 * @since 8.0.0
	 *
	 * @param  array $endpoint The API endpoint.
	 * @return bool  $body     The response body.
	 */
	
	public function blogdog_GET( $endpoint, $params = array() ) {
		
		$response = wp_remote_get( 
			$this->api_url() . $endpoint . join( '/', $params ),
			$this->headers()
		);

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

	/**
	 * blogdog POST
	 *
	 * Send POST request to blogdog api.
	 *
	 * @since 8.0.0
	 *
	 * @param  array $endpoint The API endpoint.
	 * @return bool  $body     The response body.
	 */
	
	public function blogdog_POST( $endpoint, $body = array() ) {
		
		$response = wp_remote_post( 
			$this->api_url() . $endpoint,
			array_merge( $this->headers(), $body )
		);
		
		return json_decode( wp_remote_retrieve_body( $response ) );

	}

	/**
	 * Headers
	 *
	 * An array of HTTP headers for our IDX servers.
	 *
	 * @since 8.0.0
	 *
	 * @return array $headers An array of HTTP headers
	 */

	public function headers() {

		return array(
			'headers'     =>  array(
            	'Accept' 			=> 'application/json',
				'Referer' 			=> site_url(),
				'Authentication'	=> $this->api_key()
        	)
		);

	}

	/**
	 * API Key.
	 *
	 * @return mixed
	 */

	protected function api_key() {
		return get_option( 'blogdog_api_key' );
	}

	/**
	 * API URL.
	 *
	 * @return string
	 */
	private function api_url() {
		return 'https://api.reblogdog.com/wp-json/blogdog_api/1.0.0/';
	}
	
	/**
	 * blogdog publish.
	 *
	 * @since 5.0.0
	 * @access protected
	 *
	 * @param object $the_post {
 	 *     Data used to publish blog post from api response.
     *
     *     @type string 'image' url of Image to be downloaded.
	 *     @type array 'title' {
	 *	 		@type string 'h1' Post title.
	 *	 		@type string 'cat' Post category.
	 *		}
	 *		@type object $the_post['tags'] Mixed array of post tsgs.
     * }
	 */
	
	protected function blogdog_publish( $the_post ){
		
		/**
 		 * Used to set category and tags.
 		 */
		require_once( ABSPATH . 'wp-admin/includes/taxonomy.php' );
		require_once( ABSPATH . 'wp-includes/pluggable.php' );
			
		if ( ! empty( $the_post )) {
	
			$post = array(
				'post_title'    => $the_post->post_title,
  				'post_status'   => 'future',
				'post_date'     => $the_post->post_date,
  				'post_author'   => get_option( 'blogdog_author_id' )
			); 
			
			/** Insert blog post and retrieve post ID */
			$post_id = wp_insert_post( $post );
			
			/** Set post tags */
			foreach( $the_post->post_tags as $tag ) {
				wp_set_post_terms( $post_id, $tag, 'post_tag', true );
			}
			
			/** set post category */							
			wp_create_categories( $the_post->post_category, $post_id );
			
			/** set featured image and retriece image url */
			$image_url = $this->featured( $the_post, $post_id );
			
			$post_content = str_replace( '{image_url}', $image_url, $the_post->post_content );

			/** If Custom Shortcode add it now */
			if( isset( $the_post->post_meta ) ) {

				$data = array();
				$data['query'] = json_decode( json_encode( $the_post->post_meta ), true );

				$post_meta_id = update_post_meta( $post_id, 'save_this_shortcode', $data );

				$post_content = str_replace( '{post_meta_id}', $post_meta_id, $post_content );
				
			} 

			/** Update post with content */	
			$update_post = array(
      			'ID'           => $post_id,
      			'post_content' => $post_content,
  			);
			
			/** update post content */		
			wp_update_post( $update_post );
			
		}

		$this->check_event_schedule( $post_id );
		
		return;
	}
	
	/**
	 * Check Event Schedule
	 *
	 * Check to insure the wp_cron event has been scheduled.
	 *
	 * @since 5.6.9
	 *
	 * @param int $post_id The ID of the post the event will act on.
	 */
		
	public function check_event_schedule( $post_id ) {
		
		if( 
			! wp_next_scheduled( 'publish_future_post', array( $post_id ) )
			&& 
			! wp_next_scheduled( 'future_to_publish', array( $post_id ) )
		) {
			
			/** We schedule the post to publish within a 4 hour window */
			wp_schedule_single_event( 
				( rand ( 0, 10800 ) + time() ), 
				'publish_future_post', 
				array( $post_id ) 
			);
			
		}
		
	}
	
	/**
	 * featured.
	 *
	 * Download and insert image into database. 
	 * Set featured image to post.
	 * Return image url to be used in post content.
	 *
	 * @since 5.0.0
	 * @access private
	 *
	 * @param object $the_post{
	 *		@see blogdog::blogdog_publish()
	 * }
	 * @param string $post_id Post ID for the featured image.
	 *
	 * @return string $image_url The image url of the featured image.
	 */
	
	private function featured( $the_post, $post_id ){
		
		/**
 		 * Require image functions from wp core.
 		 */
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		
		$upload_dir = wp_upload_dir();
		
		/** get remote image data from url provided by api */
		$image_data = file_get_contents( $the_post->post_image );
		
		/** create image name post h2 */
		$basename = sanitize_title( $the_post->post_title );
		$filename   = $basename.'.jpg';

		if( wp_mkdir_p( $upload_dir['path'])) {
    		$file = $upload_dir['path'] . '/' . $filename;
		} else {
   			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		
		/** write the image to image upload dir */
		file_put_contents( $file, $image_data );

		$wp_filetype = wp_check_filetype( $filename, null );
		
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		
		/** Insert featured image data */
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		
		/** get metadata for featured image */
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		
		/** Update metadata */
		wp_update_attachment_metadata( $attach_id, $attach_data );
		
		/** set the featured image for the post */
		set_post_thumbnail( $post_id, $attach_id );
		
		/** return $image_url to be used in post content */
		return wp_get_attachment_url( $attach_id );

	}
	
	/**
	 * blogdog activation.
	 *
	 * On plugin activation we update current version.
	 *
	 * @since 5.0.0
	 * @since 5.2.0 We no longer set cron at activation.
	 * @since 5.6.6 Add Update settings for current version.
	 */
	
	public function blogdog_activation() {
		
		/** Update settings for current version */
		$this->blogdog_update();

		/** Update corrent plugin version */
		update_option( 'blogdog_current_version', BLOGDOG_PLUGIN_VERSION );
		
	}

	/**
	 * Blogdog deactivation.
	 *
	 * @since 5.0.0
	 */

	public function blogdog_deactivation() {
		$this->clear_options();
	}

	/**
	 * Clear options.
	 */

	public function clear_options() {

		$options = [
			'blogdog_locations_form',
			'blogdog_locations_admin',
			'blogdog_locations_city',
			'blogdog_locations_sub',
			'blogdog_tabs'
		];

		foreach ( $options as $option ) {
			if ( get_option( $option ) ) {
				delete_option( $option );
			}
		}

	}
	
	/**
	 * blogdog check version.
	 *
	 * Check to see if current version is stored in database, if no, call blogdog_activation.
	 *
	 * @since 5.0.0
	 */
	
	public function blogdog_check_version() {
		
		if ( BLOGDOG_PLUGIN_VERSION !== get_option( 'blogdog_current_version' ) ) {
			$this->blogdog_deactivation();
			$this->blogdog_activation();
		}
		
	}
	
	/**
	 * Blogdog Update.
	 *
	 * Update settings for current version.
	 *
	 * @since 5.6.6
	 */
	
	public function blogdog_update() {
		
		/** Determine if cron is scheduled */
		if( wp_next_scheduled( 'blogdog_cron' ) ) {

			/** Clear the cron */
			wp_clear_scheduled_hook( 'blogdog_cron' );
			
		}
		
	}
	
	/**
	 * Blogdog Depricated.
	 *
	 * Remove depricated shortcodes.
	 *
	 * @since 5.2.2
	 * @since 6.0.0 Depricate auto_content shortcode.
	 */
	
	public function blogdog_depricated() {
		
		/** Hide shortcodes from RE_blogdog v1.0 */
		add_shortcode( 'reblogdog_cta', '__return_false' );
		add_shortcode( 'auto_content', '__return_false' );
	
	}

	/**
	 * Write logs to file.
	 *
	 * @param $key
	 * @param $data
	 */

	public static function log( $key, $data ) {

		$msg         = self::message( $data );
		$uploads_dir = wp_get_upload_dir();
		$log_path    = trailingslashit( $uploads_dir['path'] ) . 'blogdog-logs';
		$error_file  = trailingslashit( $log_path ) . "{$key}.txt";

		// Create the log file dir if we do not already have one.
		if ( ! file_exists( $log_path ) ) {
			mkdir( trailingslashit( $log_path ), 0755, TRUE );
		}

		if ( ! file_exists( $error_file ) ) {
			fopen( $error_file, 'w' );
		}

		error_log( $msg, 3, $error_file );

	}

	/**
	 * Message for  the log.
	 *
	 * @param $data
	 *
	 * @return string
	 */

	public static function message( $data ) {

		ob_start();

		$date = date( "F j, Y, g:i a" );

		echo "[{$date}] - ";

		if ( is_array( $data ) || is_object( $data ) ) {
			print_r( $data );
		} else {
			echo $data;
		}

		echo "\n";
		echo '__________________________________________________________________________';
		echo "\n";

		return ob_get_clean();

	}

}

/**
 * @see blogdog::init()
 *
 */
add_action( 'plugins_loaded', array( 'blogdog', 'init' ) );
