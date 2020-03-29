jQuery(document).ready(function($) {
	// Function to add auto suggest.
	$.fn.ataTagsSuggest = function( options ) {

		var cache;
		var last;
		var $element = $( this );

		var taxonomy = $element.attr( 'data-wp-taxonomy' ) || 'category';

		function split( val ) {
			return val.split( /,\s*/ );
		}

		function extractLast( term ) {
			return split( term ).pop();
		}

		$element.on( "keydown", function( event ) {
				// Don't navigate away from the field on tab when selecting an item.
				if ( event.keyCode === $.ui.keyCode.TAB &&
				$( this ).autocomplete( 'instance' ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 2,
				source: function( request, response ) {
					var term;

					if ( last === request.term ) {
						response( cache );
						return;
					}

					term = extractLast( request.term );

					if ( last === request.term ) {
						response( cache );
						return;
					}

					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: ajaxurl,
						data: {
							action: 'ata_tag_search',
							tax: taxonomy,
							q: term
						},
						success: function( data ) {
							cache = data;

							response( data );
						}
					});

					last = request.term;

				},
				search: function() {
					// Custom minLength.
					var term = extractLast( this.value );

					if ( term.length < 2 ) {
						return false;
					}
				},
				focus: function( event, ui ) {
					// Prevent value inserted on focus.
					event.preventDefault();
				},
				select: function( event, ui ) {
					var terms = split( this.value );

					// Remove the last user input.
					terms.pop();

					// Add the selected item.
					terms.push( ui.item.value );

					// Add placeholder to get the comma-and-space at the end.
					terms.push( "" );
					this.value = terms.join( ", " );
					return false;
				}
			});

	};

	$( '.category_autocomplete' ).each( function ( i, element ) {
		$( element ).ataTagsSuggest();
	});

	// Prompt the user when they leave the page without saving the form.
	formmodified=0;

	function confirmFormChange() {
		formmodified=1;
	}

	function confirmExit() {
		if ( formmodified == 1 ) {
			return true;
		}
	}

	$('form *').change( confirmFormChange );

	window.onbeforeunload = confirmExit;

	$( "input[name='submit']" ).click( function() {
		formmodified = 0;
	});

	$( function() {
		$( "#post-body-content" ).tabs({
			create: function( event, ui ) {
				$( ui.tab.find("a") ).addClass( "nav-tab-active" );
			},
			activate: function( event, ui ) {
				$( ui.oldTab.find("a") ).removeClass( "nav-tab-active" );
				$( ui.newTab.find("a") ).addClass( "nav-tab-active" );
			}
		});
	});

	// Initialise CodeMirror.
	$( ".codemirror_html" ).each( function( index, element ) {
		if( $( element ).length ) {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
			editorSettings.codemirror = _.extend(
				{},
				editorSettings.codemirror,
				{
				}
			);
			var editor = wp.codeEditor.initialize( $( element ), editorSettings );
			editor.codemirror.on( 'change', confirmFormChange );
		}
	});

	$( ".codemirror_js" ).each( function( index, element ) {
		if( $( element ).length ) {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
			editorSettings.codemirror = _.extend(
				{},
				editorSettings.codemirror,
				{
					mode: 'javascript',
				}
			);
			var editor = wp.codeEditor.initialize( $( element ), editorSettings );
			editor.codemirror.on( 'change', confirmFormChange );
		}
	});

	$( ".codemirror_css" ).each( function( index, element ) {
		if( $( element ).length ) {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
			editorSettings.codemirror = _.extend(
				{},
				editorSettings.codemirror,
				{
					mode: 'css',
				}
			);
			var editor = wp.codeEditor.initialize( $( element ), editorSettings );
			editor.codemirror.on( 'change', confirmFormChange );
		}
	});

});
