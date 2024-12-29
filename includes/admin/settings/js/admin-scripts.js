/* global jQuery, wp */

jQuery( function( $ ) {
	// File browser.
	$( '.file-browser' ).on( 'click', function( event ) {
		event.preventDefault();

		const self = $( this );

		// Create the media frame.
		const file_frame = wp.media.frames.file_frame = wp.media( {
			title: self.data( 'uploader_title' ),
			button: {
				text: self.data( 'uploader_button_text' ),
			},
			multiple: false
		} );

		file_frame.on( 'select', function() {
			const attachment = file_frame.state().get( 'selection' ).first().toJSON();
			self.prev( '.file-url' ).val( attachment.url ).trigger( 'input' );
		} );

		// Finally, open the modal
		file_frame.open();
	} );

	// Prompt the user when they leave the page without saving the form.
	let formmodified = 0;

	function confirmFormChange() {
		formmodified = 1;
	}

	function confirmExit() {
		if ( formmodified === 1 ) {
			return true;
		}
		return undefined;
	}

	function formNotModified() {
		formmodified = 0;
	}

	// Use on() with input event instead of change()
	$( 'form *' ).on( 'input', confirmFormChange );
	$( 'form select, form input[type="checkbox"], form input[type="radio"]' ).on( 'change', confirmFormChange );

	window.onbeforeunload = confirmExit;

	// Use on() instead of click()
	$( "input[name='submit']" ).on( 'click', formNotModified );
	$( "input[id='search-submit']" ).on( 'click', formNotModified );
	$( "input[id='doaction']" ).on( 'click', formNotModified );
	$( "input[id='doaction2']" ).on( 'click', formNotModified );
	$( "input[name='filter_action']" ).on( 'click', formNotModified );

	// Initialize tabs
	$( "#post-body-content" ).tabs( {
		create: function( event, ui ) {
			$( ui.tab.find( "a" ) ).addClass( "nav-tab-active" );
		},
		activate: function( event, ui ) {
			$( ui.oldTab.find( "a" ) ).removeClass( "nav-tab-active" );
			$( ui.newTab.find( "a" ) ).addClass( "nav-tab-active" );
		}
	} );

	// Initialize ColorPicker.
	$( '.color-field' ).each( function( i, element ) {
		$( element ).wpColorPicker();
	} );
} );
