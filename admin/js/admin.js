 /**
 * Provide data to PHP using ajax. PHP will procees data and return a json encoded response.
 * The response will be displayed as html upon successful ajax request.
 *
 * @summary   Process ajax request and display response.
 *
 * @link      /admin/js/admin.js
 * @since     1.0.0
 * @since     5.3.0 Add location heading functions.
 * @since 	  5.6.6 Improve Open and close location tabs functions.
 * @requires  /wp-includes/js/jquery/jquery.js
 */
jQuery( document ).ready( function( $ ) {
  
  	var loaded = false;

	$( '#tabs' ).tabs();
	
	$( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
	
	$( '#blogdog_admin button' ).button();
    $( '#blogdog_admin button' ).click( function( event ) {
      	event.preventDefault();
    } );
	
	/** 
	 * Open and close location tabs 
	 *
	 * @since 5.6.6 Imptove functionality to allow all tabs to be closed or open.
	 */
	$('#locations_wrap').on('click', '.location_heading', function(e) {
		$(this).children('.location-open').toggleClass( 'fa-caret-up fa-caret-down' );
		$(this).parents('.locations-wrap').children('.locations').slideToggle('slow');
	});
	
	/** Delete Location */
	$( '#locations_wrap' ).on( 'click', '.delete-section', function() {
		$( this ).parents( '.locations-wrap' ).slideUp( 'normal', function() { $( this ).remove(); } );
	} );
	
	/** Add locations and communities inputs */
	$( '#locations_wrap' ).on( 'click', '.add_location', function() {
		
		var count = 0;

		var ID = this.id;
		
		if( ID != 'add_community' ) {
			
			count = $( '#loactions_count' ).val();
			
		}
		
		jQuery.ajax ( {
			url : blogdog_url.ajax_url,
			type : 'post', 
			data : {
				action : 'blogdog_ajax_process',
				ID : ID,
				count : count,
				blogdog_ajax_nonce : blogdog_url.nonce,
			},
			success: function( response ) {
				$( response['append'] ).prepend( response['html'] );
				$( response['count_id'] ).val( response['count'] );
			},
			dataType:'json'
		} );
	} );
	
	$( '.save-changes' ).on( 'click', function() {
		
		var ID = this.id;
		var post = $( '#' + ID + '_form').serialize();
		
		jQuery.ajax ( {
			url : blogdog_url.ajax_url,
			type : 'post', 
			data : {
				action : 'blogdog_ajax_process',
				ID : ID,
				post : post,
				blogdog_ajax_nonce : blogdog_url.nonce,
			},
			beforeSend: function() {
				$( '.response' ).addClass( 'fa-spinner' );
			},
			success: function( response ) {
  				$( '.response' ).removeClass( 'fa-spinner' );
			},
			dataType:'json'
		} );
	} );
	
	/** Acitavte api  */
	$( '#switch_checkbox' ).on( 'click', function() {
		
		$('.api_message').addClass('fa-spinner fa-spin');
		
		var ID = this.id;
		
		jQuery.ajax ( {
			url : blogdog_url.ajax_url,
			type : 'post', 
			data : {
				action : 'blogdog_ajax_process',
				ID : ID,
				blogdog_ajax_nonce : blogdog_url.nonce,
			},
			success: function( response ) {
				$('.api_message').removeClass('fa-spinner fa-spin');
				$( response['code'] ).html( response['html'] );
				
				if( response['checked'] == 'checked' ) {
					$( '#switch_checkbox' ).prop( 'checked', true );
				} else {
					$( '#switch_checkbox' ).prop( 'checked', false );
				}
			},
			dataType:'json'
		} );
	} );
	
	/** Update city and zipcode inputs  */
	$( '#blogdog_admin' ).on( 'change', '.blogdog-city', function() {
			
		var ID = this.id;
		var city = $( this ).val();
		var section = $( this ).attr( 'section' );

		$( this ).parents( '.locations-wrap' ).children( '.location_heading' ).children( '.city-text' ).html( city );
		
		jQuery.ajax ( {
			url : blogdog_url.ajax_url,
			type : 'post', 
			data : {
				action : 'blogdog_ajax_process',
				ID : ID,
				city : city,
				section : section,
				blogdog_ajax_nonce : blogdog_url.nonce,
			},
			beforeSend: function() {
				$( '#blogdog_zipcode_' + section ).html( '<i class="far fa-spin fa-spinner"></i>' );
			},
			success: function( response ) {
				$( response['code'] ).each( function() {
					$( this ).html( response['html'] );
				});
			},
			dataType:'json'
		} );
	} );

	/* Update location heading on Ptype select change */
	$( '#locations_wrap'  ).on( 'change', '.blogdog-ptype', function( ) {
		$( this ).parents( '.locations-wrap' ).children( '.location_heading' ).children( '.ptype-text' ).html( $( this ).val() );
	} );
	
	/* Update location heading on subdivision input change */
	$( '#locations_wrap'  ).on( 'input', '.blogdog-sub', function( ) {
		var subdivision = $( this ).val();
		$( this ).parents( '.locations-wrap' ).children( '.location_heading' ).children( '.sub-text' ).html( subdivision );
	} );
	
	
	/** Change agent type for name field label */
	$( '#blogdog_agent_type' ).change( function() {
		var type = $( this ).val();
		$( '#agent_name_type' ).html( type );
	} );
	
	/* 
	 * Update location heading on zipcode change 
	 *
	 * @since 5.4.0
	 */
	$( '#locations_wrap'  ).on( 'change', '.blogdog-zip', function( ) {
		var x = $( this ).parents( '.locations-wrap' ).children( '.location_heading' ).children( '.zip-text' );
		var y = $( this ).val();
		var z = x.html();
		
		if( $( this ).prop( 'checked' ) ) {
			x.append( y + ' ' ).css( 'display', 'none' ).fadeIn( 'slow' );
		} else {
			x.fadeOut();
			x.html( z.replace( y, '' ) ).fadeIn( 'slow' );
		}
		
	} );
	
	$( '#locations_wrap'  ).on( 'change', '.blogdog-deactivate', function( ) {
		if( $( this ).prop( 'checked' ) ) {
			$( this ).parents( '.locations-wrap' ).addClass( 'deactivate' );
		} else {
			$( this ).parents( '.locations-wrap' ).removeClass( 'deactivate' );
		}
	} );
	
	/** Play or pause admin videos*/
	$( '.admin-video' ).click( function(){
		this.paused ? this.play() : this.pause();
	});

	/** Load Locations  */
	$( '#load-locations' ).on( 'click', function() {
		
		if( loaded === true ) return;

		jQuery.ajax ( {
			url : blogdog_url.ajax_url, 
			type : 'post',
			data : {
				action : 'blogdog_load_admin_locations',
				blogdog_ajax_nonce : blogdog_url.nonce,
			},
			success: function( response ) {
				$('#sortable').html( response );
				loaded = true;
			},
			dataType:'json'
		} );
	} );
	
} );