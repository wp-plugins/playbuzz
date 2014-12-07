(function() {
	tinymce.PluginManager.add( 'playbuzz', function( editor, url ) {

		var shortcode_tag = 'playbuzz-item';
		var popup_name = 'playbuzz-popup';
		var placeholder_class = 'wp-playbuzz';

		// Helper Functions - Get attribute from pattern
		function getAttr( pattern, attr ) {

			n = new RegExp( attr + '=\"([^\"]+)\"', 'g' ).exec( pattern );
			return n ? window.decodeURIComponent( n[1] ) : '';

		};

		// Helper Functions - Placeholder HTML
		function html( cls, data ) {

			var placeholder = url + '/../img/playbuzz-placeholder.jpg';
			data = window.encodeURIComponent( data );
 			return '<img src="' + placeholder + '" class="mceItem ' + cls + '" data-playbuzz-attr="' + data + '" data-mce-resize="false" data-mce-placeholder="1" style="/*width:100%; max-width:100%;*/" />';

		}

		// Helper Functions - Replace the shortcode with an image placeholder
		function replaceShortcodes( content ) {

			// Match [playbuzz-item(attr)]
			return content.replace( /\[playbuzz-item([^\]]*)\]/g, function( all, attr, con ) {
				return html( placeholder_class, attr );
			});

		}

		// Helper Functions - Replace the image placeholder with the shortcode
		function restoreShortcodes( content ) {

			// Match any image tag with our class and replace it with the shortcode's content and attributes
			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {

				// Extract the data in  data-playbuzz-attr="..."  from  <img ...>
				var data = getAttr( image, 'data-playbuzz-attr' );

				// Create the shortcode
				if ( data ) {
					return '<p>[' + shortcode_tag + data + ']</p>';
				}

				return match;

			});

		}

		// Setup the plugin popup
		editor.addCommand( popup_name, function(ui, v) {

			// Set default values
			var item_url   = ''; if (v.item_url)   item_url   = v.item_url;
			var info       = ''; if (v.info)       info       = v.info;
			var shares     = ''; if (v.shares)     shares     = v.shares;
			var comments   = ''; if (v.comments)   comments   = v.comments;
			var recommend  = ''; if (v.recommend)  recommend  = v.recommend;
			var tags       = ''; if (v.tags)       tags       = v.tags;
			var width      = ''; if (v.width)      width      = v.width;
			var height     = ''; if (v.height)     height     = v.height;
			var margin_top = ''; if (v.margin_top) margin_top = v.margin_top;

			// Open popup
			editor.windowManager.open( {
				title: 'Embed playbuzz Playful Content',
				width : 550,
				height : 380,
				body: [
					{
						// add item_url
						type: 'textbox',
						name: 'item_url',
						label: 'Item URL',
						value: item_url,
						tooltip: 'http://www.plyaybuzz.com/...'
					},
					{
						// advanced options separator
						type: 'label',
						name: 'advanced',
						classes: 'playbuzz-more-fields',
						style: 'color: #666; font-style: italic; font-weight: bold;',
						text: "Advanced Options:",
						onclick: function() {
							$( '.mce-playbuzz-more-fields' ).toggleClass( 'mce-fields-hidden' );
						}
					},
					{
						// add info
						type: 'listbox',
						name: 'info',
						label: 'Item Info',
						value: info,
						tooltip: 'Thumbnail, name, description, editor, etc.',
						'values': [
							{ text: 'Show item info', value: 'true'  },
							{ text: 'Hide item info', value: 'false' }
						]
					},
					{
						// add shares
						type: 'listbox',
						name: 'shares',
						label: 'Sharing',
						value: shares,
						tooltip: 'Sharing buttons',
						'values': [
							{ text: 'Show sharing buttons', value: 'true'  },
							{ text: 'Hide sharing buttons', value: 'false' }
						]
					},
					{
						// add comments
						type: 'listbox',
						name: 'comments',
						label: 'Comments',
						value: comments,
						tooltip: 'Facebook comments',
						'values': [
							{ text: 'Show Facebook comments', value: 'true'  },
							{ text: 'Hide Facebook comments', value: 'false' }
						]
					},
					{
						// add recommend
						type: 'listbox',
						name: 'recommend',
						label: 'Recommendations',
						value: recommend,
						tooltip: 'Recommendations for more items',
						'values': [
							{ text: 'Show recommendations', value: 'true'  },
							{ text: 'Hide recommendations', value: 'false' }
						]
					},
					{
						// add width
						type: 'textbox',
						name: 'width',
						label: 'Width',
						value: width,
						text: 'some text',
						tooltip: 'Define custom width in pixels. Default: auto'
					},
					{
						// add height
						type: 'textbox',
						name: 'height',
						label: 'Height',
						value: height,
						tooltip: 'Define custom height in pixels. Default: auto'
					},
					{
						// add margin_top
						type: 'textbox',
						name: 'margin_top',
						label: 'margin-top',
						value: margin_top,
						tooltip: 'Define custom margin-top in pixels. Default: 0px',
					},
				],
				onsubmit: function( e ) { // when the button is clicked return full shortcode
					// start shortcode tag
					var shortcode_str = '[' + shortcode_tag;

					// add "item_url"
					if (typeof e.data.item_url != 'undefined' && e.data.item_url.length )
						shortcode_str += ' url="' + e.data.item_url + '"';

					// add "info"
					if (typeof e.data.info != 'undefined' && e.data.info.length && typeof e.data.info != 'true' )
						shortcode_str += ' info="' + e.data.info + '"';

					// add "shares"
					if (typeof e.data.shares != 'undefined' && e.data.shares.length && typeof e.data.shares != 'true' )
						shortcode_str += ' shares="' + e.data.shares + '"';

					// add "comments"
					if (typeof e.data.comments != 'undefined' && e.data.comments.length && typeof e.data.comments != 'true' )
						shortcode_str += ' comments="' + e.data.comments + '"';

					// add "recommend"
					if (typeof e.data.recommend != 'undefined' && e.data.recommend.length && typeof e.data.recommend != 'true' )
						shortcode_str += ' recommend="' + e.data.recommend + '"';

					// add "tags"
					if (typeof e.data.tags != 'undefined' && e.data.tags.length )
						shortcode_str += ' tags="' + e.data.tags + '"';

					// add "width"
					if (typeof e.data.width != 'undefined' && e.data.width.length && typeof e.data.width != 'auto' )
						shortcode_str += ' width="' + e.data.width + '"';

					// add "height"
					if (typeof e.data.height != 'undefined' && e.data.height.length && typeof e.data.height != 'auto' )
						shortcode_str += ' height="' + e.data.height + '"';

					// add "margin-top"
					if (typeof e.data.margin_top != 'undefined' && e.data.margin_top.length && typeof e.data.margin_top != '0' && typeof e.data.margin_top != '0px' )
						shortcode_str += ' margin-top="' + e.data.margin_top + '"';

					// End shortcode tag
					shortcode_str += ']';

					// Insert shortcode to the editor
					editor.insertContent( shortcode_str );
				},
			});

		});

		// Add button
		editor.addButton( 'playbuzz', {
			icon: 'playbuzz',
			tooltip: 'Playbuzz',
			onclick: function() {
				editor.execCommand( popup_name, '', {
					item_url   : '',
					info       : '',
					shares     : '',
					comments   : '',
					recommend  : '',
					tags       : '',
					width      : '',
					height     : '',
					margin_top : ''
				});
			}
		});

		// Replace the shortcode with an image placeholder
		editor.on( 'BeforeSetContent', function(event) {
			event.content = replaceShortcodes( event.content );
		});

		// Replace the image placeholder with the shortcode
		editor.on( 'GetContent', function(event) {
			event.content = restoreShortcodes( event.content );
		});

		// Open popup by clicking the placeholder image
		editor.on( 'click', function(e) {

			if ( e.target.nodeName == 'IMG' && e.target.className.indexOf( placeholder_class ) > -1 ) {

				var attr = e.target.attributes['data-playbuzz-attr'].value;
				attr = window.decodeURIComponent( attr );

				editor.execCommand( popup_name, '', {
					item_url   : getAttr( attr, 'url' ),
					info       : getAttr( attr, 'info' ),
					shares     : getAttr( attr, 'shares' ),
					comments   : getAttr( attr, 'comments' ),
					recommend  : getAttr( attr, 'recommend' ),
					tags       : getAttr( attr, 'tags' ),
					width      : getAttr( attr, 'width' ),
					height     : getAttr( attr, 'height' ),
					margin_top : getAttr( attr, 'margin-top' )
				});

			}

		});

	});

})();
