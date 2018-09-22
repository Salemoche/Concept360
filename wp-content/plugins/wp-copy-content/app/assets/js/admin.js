/* global ajaxurl, attachMediaBoxL10n, _wpMediaGridSettings, showNotice */
( function( $ ) {
	var texfield    = $( '#wcc_post_title' ),
		tempID      = 0,
		copybutton  = $( '#wcc_fetch_button' );

	// Post suggestion function.
	$.fn.wpPostSuggest = function( options ) {
		var cache;
		var last;
		var $element = $( this );
		options = options || {};

		// Extend options.
		options = $.extend( {
			source: function( request, response ) {

				// Remove error message if there is any.
				if( $( '#wpcc-notice-element' ).length > 0 ) {
					$( '#wpcc-notice-element' ).remove();
				}

				// Call ajax for fetching post suggestions.
				$.post( ajaxurl, {
					action: 'wpcc_find_posts',
					ps: $element.val(),
					_ajax_nonce: $('#_ajax_nonce').val(),
					dataType: 'json',
				} ).always( function() {
					$element.removeClass( 'ui-autocomplete-loading' ); // UI fails to remove this sometimes?
				} ).done( function( data ) {
					var titleName;
					var titles = [];
					if ( data.success ) {
						// Iterate the loop and set data to suggestions.
						data.data.map((post) => {
							var id = ++tempID;
							titles.push({
								id: id,
								post_id: post.ID,
								name: post.post_title
							});
						});
						cache = titles;
						response( titles );
					} else {
						// Show error message.
						$( '#wpcc-spiner' ).after( '<div id="wpcc-notice-element" class="error">' + data.data + '</div>' );
					}
				} );
			},
			focus: function( event, ui ) {
				$element.attr( 'aria-activedescendant', 'wp-tags-autocomplete-' + ui.item.id );

				// Don't empty the input field when using the arrow keys to
				// highlight items. See api.jqueryui.com/autocomplete/#event-focus
				event.preventDefault();
			},
			select: function( event, ui ) {

				// Set selected title to text box.
				$element.val( ui.item.name );

				// Set post id to hidden field. We are fetching post content using this id only.
				$( '#wcc_post_id' ).val( ui.item.post_id );

				// Show fetch button only after setting title and post id to the desired field.
				$( '#wcc_fetch_button' ).show();

				return false;
			},
			open: function() {
				$element.attr( 'aria-expanded', 'true' );
			},
			close: function() {
				$element.attr( 'aria-expanded', 'false' );
			},
			minLength: 2,
			position: {
				my: 'left top+2',
				at: 'left bottom',
				collision: 'none'
			},
			messages: {
				noResults: window.uiAutocompleteL10n.noResults,
				results: function( number ) {
					if ( number > 1 ) {
						return window.uiAutocompleteL10n.manyResults.replace( '%d', number );
					}
					return window.uiAutocompleteL10n.oneResult;
				}
			}
		}, options );
		$element.on( 'keydown', function() {
			$element.removeAttr( 'aria-activedescendant' );
		} )
		.autocomplete( options )
		.autocomplete( 'instance' )._renderItem = function( ul, item ) {
			return $( '<li role="option" data-post-id="' + item.post_id + '" id="wp-tags-autocomplete-' + item.id + '">' )
				.text( item.name )
				.appendTo( ul );
		};
		$element.attr( {
			'role': 'combobox',
			'aria-autocomplete': 'list',
			'aria-expanded': 'false',
			'aria-owns': $element.autocomplete( 'widget' ).attr( 'id' )
		} )
		.on( 'focus', function() {
			var inputValue = $element.val();
			// Don't trigger a search if the field is empty.
			// Also, avoids screen readers announce `No search results`.
			if ( inputValue ) {
				$element.autocomplete( 'search' );
			}
		} );
	}
	// Copy content function.
	$.fn.wpCopyContent = function( options ) {
		var $element = $( this );
		$element.on( 'click', function( options ) {

			// Get the post if drom hidden field.
			var post_id = $( '#wcc_post_id' ).val();

			// Show spiner.
			$( '#wpcc-spiner' ).attr( 'style', 'visibility:visible' );

			// Remove error message if there is any.
			if( $( '#wpcc-notice-element' ).length > 0 ) {
				$( '#wpcc-notice-element' ).remove();
			}

			// Ajax call for fetching post content.
			$.post( window.ajaxurl, {
				action: 'wpcc_copy_from_post',
				pid: post_id,
				_ajax_nonce: $('#_ajax_nonce').val(),
				dataType: 'json',
			} ).done( function( data ) {
				if( data.success ) {

					// Set content to editor.
					tinyMCE.activeEditor.setContent( data.data );
					$( '#wcc_post_id' ).val( 0 );
					$( '#wcc_fetch_button' ).hide();
				} else {

					// Show error message.
					$( '#wpcc-spiner' ).after( '<div id="wpcc-notice-element" class="error">' + data.data + '</div>' );
				}

				// Remove spiner after process is completed.
				$( '#wpcc-spiner' ).hide();
			} );
		} );
	}

	// Call function suggest posts.
	$( texfield ).wpPostSuggest();

	// Copy the content.
	$( copybutton ).wpCopyContent();

}( jQuery ) );
