(function() {

	tinymce.PluginManager.add( 'playbuzz', function( editor, url ) {

		// Get attribute from pattern
		function get_attr( pattern, attr ) {

			n = new RegExp( attr + '=\"([^\"]+)\"', 'g' ).exec( pattern );
			return n ? window.decodeURIComponent( n[1] ) : '';

		};

		// Return formatted date
		function item_date( published_at ) {

			var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			    publish_date = new Date( published_at ),
			    published = months[publish_date.getMonth()] + ' ' + publish_date.getDate() + ', ' + publish_date.getFullYear();
			return published;

		}

		// Return item type
		function item_type( type ) {

			switch( type ) {
				case "TestYourself"   : name = "Personality Quiz"; break;
				case "List"           : name = "List";             break;
				case "multipleChoice" : name = "Trivia";           break;
				case "Poll"           : name = "Poll";             break;
				case "RankList"       : name = "Ranked List";      break;
				case "Gallery"        : name = "Gallery Quiz";     break;
				default               : name = "";                 break;
			}
			return name;

		}

		// Popup pagination
		function playbuzz_pagination( total_pages, current_page, type ) {

			// Current Page
			current_page = ( isNaN( current_page ) ) ? parseInt( current_page ) : current_page ;
			current_page = ( current_page < 1 ) ? 1 : current_page ;

			// Start page
			var start_page=current_page-2;
			if ( start_page<=0 ) start_page=1;

			// End_page
			var end_page=current_page+2;
			if ( end_page>=total_pages ) end_page=total_pages;

			// Container
			results = "<div class='playbuzz_item_pagination " + type +"' data-function='" + type +"'>";

			// Prev
			if ( current_page == 1 )
				results += "	<a class='disabled_pagination playbuzz_prev'></a>";
			else
				results += "	<a class='enabled_pagination playbuzz_prev' onclick='" + type + "(" + (current_page-1) + ")'></a>";

			// Pages
			for (page = start_page; page <= end_page; ++page) {
				current = ( (page == current_page) ? " playbuzz_current" : "" );
				results += "	<a class='enabled_pagination" + current + "' onclick='" + type + "(" + page + ")'>" + page + "</a>";
			}

			// Next
			if ( current_page == total_pages )
				results += "	<a class='disabled_pagination playbuzz_next'></a>";
			else
				results += "	<a class='enabled_pagination playbuzz_next' onclick='" + type + "(" + (current_page+1) + ")'></a>";

			// Close Container
			results += "</div>";

			return results;

		}

		// Add shortcode to tinyMCE editor (used by search popup)
		function embedShortcodeFromSerachPopup( url ) {

			// Add shortcode to tinyMCE editor
			if ( tinyMCE && tinyMCE.activeEditor ) {
				tinyMCE.activeEditor.selection.setContent( '[playbuzz-item url="' + url + '"]<br>' );
			}

			// Close playbuzz search popup
			(jQuery)( '.playbuzz_popup_overlay' ).remove();

			return false;

		}
		window.embedShortcodeFromSerachPopup = embedShortcodeFromSerachPopup;

		// Add playbuzz search popup
		editor.addCommand( 'search_playbuzz_items', function( ui, v ) {

			(jQuery)("div.playbuzz_search_sub_divider").addClass("playbuzz_search_sub_divider_colored");

			// Popup
			(jQuery)("<div></div>").addClass("playbuzz_popup_overlay").appendTo("body");
			results  = "<div id='playbuzz_popup' class=''>";
			results += "	<form name='search' id='playbuzz_search_form' action='#'>";
			results += "		<div id='playbuzz_search_header'>";
			results += "			<div id='playbuzz_popup_close' class='playbuzz_search_close'></div>";
			results += "			<a onclick='PlaybuzzFeatured(1)' class='playbuzz_search_logo'><span>Playbuzz</span></a>";
			results += "			<input type='text' id='playbuzz_search' name='playbuzz_search' class='playbuzz_search' size='16' autocomplete='off' value='' placeholder='Search Term or Playbuzz URL'>";
			results += "		</div>";
			results += "		<div id='playbuzz_search_sub_header'>";
			results += "			<div class='playbuzz_search_fields'>";
			results += "				<label for='playbuzz_search_type' class='playbuzz_search_label'>SHOW</label>";
			results += "				<select id='playbuzz_search_type' name='playbuzz_search_type' class='playbuzz_search_type'>";
			results += "					<option value='List,TestYourself,Poll,RankList,multipleChoice,Gallery'>ALL TYPES</option>";
			results += "					<option value='List'>LIST</option>";
			results += "					<option value='TestYourself'>PERSONALITY QUIZ</option>";
			results += "					<option value='Poll'>POLL</option>";
			results += "					<option value='RankList'>RANKED LIST</option>";
			results += "					<option value='multipleChoice'>TRIVIA</option>";
			results += "					<option value='Gallery'>GALLERY QUIZ</option>";
			results += "				</select>";
			results += "				<label for='playbuzz_search_sort' class='playbuzz_search_label'>SORT BY</label>";
			results += "				<select id='playbuzz_search_sort' name='playbuzz_search_sort' class='playbuzz_search_sort'>";
			results += "					<option value='relevance'>RELEVANCE</option>";
			results += "					<option value='views'>VIEWS</option>";
			results += "					<option value='publish_date'>DATE</option>";
			results += "				</select>";
			results += "			</div>";
			results += "			<div id='playbuzz_search_for'><p><strong>Discover Playful Content</strong></p></div>";
			results += "			<div class='playbuzz_search_sub_divider'></div>";
			results += "		</div>";
			results += "		<div id='playbuzz_search_results'>";
			results += "			<div class='playbuzz_item_pagination'></div>";
			results += "		</div>";
			results += "		<div id='playbuzz_search_bottom'></div>";
			results += "	</form>";
			results += "</div>";
			(jQuery)( '.playbuzz_popup_overlay' ).append( results );

			// Featured items from Playbuzz
			function PlaybuzzFeatured( current_page ) {
				var type           = (jQuery)("#playbuzz_search_type").val(),
				    sort           = ( ( (jQuery)("#playbuzz_search_sort").val() != 'relevance' ) ? "sort="+(jQuery)("#playbuzz_search_sort").val() : "" ),
					playbuzz_api   = "https://restapi.playbuzz.com/v1/items/?" + sort,
				    total_items    = 0,
				    items_per_page = 30,
					results        = "";

				(jQuery).ajax({
					url      : playbuzz_api,
					type     : "get",
					dataType : "json",
					data     : {
						/*tags      : "EditorsPick_Featured",*/
						item_type : type,
						size      : items_per_page,
						from      : (current_page*items_per_page)-items_per_page
					},
					success  : function( data ) {
						total_items = data.items.total;
						if ( total_items > items_per_page ) {
							var total_pages = Math.ceil(total_items / items_per_page);
						}
						var result_text_page = ( current_page > 1 ) ? ' (page ' + current_page + ' / ' + total_pages + ')' : '';
						var result_text = "<p><strong>Featured Items</strong><span class='playbuzz_search_title_pagination'>" + result_text_page + "</span></p>";

						// Data output
						if ( data.items.data.length > 0 ) {
							(jQuery).each( data.items.data, function( key, val ) {
								results += "<div class='playbuzz_featured_item'>";
								results += "	<p class='playbuzz_featured_item_thumb'><img src='" + val.img_medium + "'></p>";
								results += "	<p class='playbuzz_featured_item_title'>" + val.title + "</p>";
								results += "	<p class='playbuzz_featured_item_meta'>By <em><strong>" + val.creator_name + "</strong></em> on " + item_date( val.published_date ) + "</p>";
								results += "	<p class='playbuzz_featured_item_views'>" + val.total_views + "</p>";
								results += "	<div class='playbuzz_featured_item_buttons'>";
								results += "		<a target='_blank' href='" + val.playbuzz_url + "' class='button button-secondary'>VIEW</a>";
								results += "		<input type='button' class='button button-primary' value='EMBED' onclick=\"return embedShortcodeFromSerachPopup('" + val.playbuzz_url + "')\">";
								results += "	</div>";
								results += "</div>";
							});
						}

						// Pagination
						if ( total_items > items_per_page ) {
							results += playbuzz_pagination( total_pages, current_page, 'PlaybuzzFeatured' );
						}

						// Update the results on the screen
						(jQuery)("#playbuzz_search_for").empty();
						(jQuery)("#playbuzz_search_for").append( result_text );
						(jQuery)("#playbuzz_search_results").empty();
						(jQuery)("#playbuzz_search_results").append( results );
						(jQuery)("#playbuzz_search_results").animate( { scrollTop:0 }, 0 );
					},
					error : function( data ) {
						// Error output
						(jQuery)("#playbuzz_search_for").empty();
						(jQuery)("#playbuzz_search_for").append( "<p><strong>Featured Items</strong></p>" );
						(jQuery)("#playbuzz_search_results").empty();
						(jQuery)("#playbuzz_search_results").append( "<div class='playbuzz_server_error'>Server error.</div>." );
					}
				});
			}
			window.PlaybuzzFeatured = PlaybuzzFeatured;

			// Search items from Playbuzz
			function PlaybuzzSearch( current_page ) {
				var query          = (jQuery)("#playbuzz_search").val(),
				    type           = (jQuery)("#playbuzz_search_type").val(),
				    sort           = ( ( (jQuery)("#playbuzz_search_sort").val() != 'relevance' ) ? "&sort="+(jQuery)("#playbuzz_search_sort").val() : "" ),
				    playbuzz_api   = "https://restapi.playbuzz.com/v1/items/?q=" + query  + sort,
				    total_items    = 0,
				    items_per_page = 10,
					results        = "";

				(jQuery).ajax({
					url      : playbuzz_api,
					type     : "get",
					dataType : "json",
					data     : {
						item_type : type,
						size      : items_per_page,
						from      : (current_page*items_per_page)-items_per_page,
						moderation : false
					},
					success  : function( data ) {
						total_items = data.items.total;
						if ( total_items > items_per_page ) {
							var total_pages = Math.ceil(total_items / items_per_page);
						}
						var result_text_page = ( current_page > 1 ) ? ' (page ' + current_page + ' / ' + total_pages + ')' : '';
						var result_text = "<p>Results for '<strong>" + query + "</strong>' <span class='playbuzz_search_title_pagination'>" + result_text_page + "</span></p>";

						// Data output
						results += "<table>";
						results += "<tbody>";
						if ( data.items.data.length > 0 ) {
							(jQuery).each( data.items.data, function( key, val ) {
								results += "<tr valign='top'>";
								results += "	<td class='playbuzz_item_thumb'><div><img src='" + val.img_thumbnail + "'></div></td>";
								results += "	<td class='playbuzz_item_desc' ><div><p>" + val.title + "</p><p class='playbuzz_item_meta'>Created by " + val.creator_name + " on " + item_date( val.published_date ) + "</p></div></td>";
								results += "	<td class='playbuzz_item_type' ><div><p>" + item_type( val.item_type )  + "</p></div></td>";
								results += "	<td class='playbuzz_item_views'><div><p>" + val.total_views + "</p></div></td>";
								results += "	<td class='playbuzz_item_buttons'><div><p>";
								results += "		<a target='_blank' class='button button-secondary' href='" + val.playbuzz_url + "'>VIEW</a>";
								results += "		<input type='button' class='button button-primary' value='EMBED' onclick=\"return embedShortcodeFromSerachPopup('" + val.playbuzz_url + "')\">";
								results += "	</p></div></td>";
								results += "</tr>";
							});
						} else {
							results += "<tr>";
							results += "	<td class='playbuzz_no_search_results'>No results found, try a different search.</td>";
							results += "</tr>";
						}
						results += "</tbody>";
						results += "</table>";

						// Pagination
						if ( total_items > items_per_page ) {
							results += playbuzz_pagination( total_pages, current_page, 'PlaybuzzSearch' );
						}

						// Update the results on the screen
						(jQuery)("#playbuzz_search_for").empty();
						(jQuery)("#playbuzz_search_for").append( result_text );
						(jQuery)("#playbuzz_search_results").empty();
						(jQuery)("#playbuzz_search_results").append( results );
						(jQuery)("#playbuzz_search_results").animate( { scrollTop:0 }, 0 );
					},
					error : function( data ) {
						// Error output
						(jQuery)("#playbuzz_search_for").empty();
						(jQuery)("#playbuzz_search_for").append( "<p>Results for <strong>" + query + "</strong></p>" );
						(jQuery)("#playbuzz_search_results").empty();
						(jQuery)("#playbuzz_search_results").append( "<div class='playbuzz_server_error'>Server error.</div>" );

					}
				});
			}
			window.PlaybuzzSearch = PlaybuzzSearch;

			// Load featured items on popup load
			PlaybuzzFeatured( 1 );

			// Close popup
			(jQuery)("#playbuzz_popup_close").click ( function() {
				(jQuery)(".playbuzz_popup_overlay").remove();
			});

			// Search
			(jQuery)("#playbuzz_search").keyup( function() { 
				if ( (jQuery)(this).val().trim() != '' )
					PlaybuzzSearch(1);
				else
					PlaybuzzFeatured(1);
			});
			(jQuery)("#playbuzz_search_type").change( function() {
				if ( (jQuery)("#playbuzz_search").val().trim() != '' )
					PlaybuzzSearch(1);
				else
					PlaybuzzFeatured(1);
			});
			(jQuery)("#playbuzz_search_sort").change( function() {
				if ( (jQuery)("#playbuzz_search").val().trim() != '' )
					PlaybuzzSearch(1);
				else
					PlaybuzzFeatured(1);
			});

		});

		// Add playbuzz button to tinyMCE visual editor
		editor.addButton( 'playbuzz', {
			icon    : 'playbuzz',
			tooltip : 'Playbuzz',
			onclick : function() {
				// Open search popup
				editor.execCommand( 'search_playbuzz_items' );
			}
		});

		// Replace the shortcode with an item info box
		editor.on( 'BeforeSetContent', function( event ) {

			event.content = event.content.replace( /\[playbuzz-item([^\]]*)\]/g, function( all, attr, con ) {

				// Encode all the shortcode attributes, to be stored in <div data-playbuzz-attr="...">
				var encodedShortcodeAttributes = window.encodeURIComponent( attr );

				// Split shortcode attributes
				var splitedAttr = attr.split(" ");

				// Extract itemPath from itemUrl -  "http://playbuzz.com/{creatorName}/{gameName}
				var itemUrl       = get_attr( decodeURIComponent( encodedShortcodeAttributes ), 'url' ),
				    itemPath      = itemUrl.split("playbuzz.com/").pop(),
				    itemPathArray = itemPath.split("/"),
				    creatorName   = itemPathArray[0],
				    gameName      = itemPathArray[1];

				// Set random image id
				var id = Math.round(Math.random() * 100000);

				// Get Item info
				(jQuery).ajax({
					url      : "https://restapi.playbuzz.com/v1/items/?size=1",
					type     : "get",
					dataType : "json",
					data     : {
						creator_name_seo : creatorName,
						item_name_seo    : gameName,
						moderation       : false
					},
					success  : function( data ) {

						// Set vars
						var info  = " ",
						    image = " ";

						// Data output
						if ( data.items.total > 0 ) {
							info  = "<p class='wp_playbuzz_title'>" + data.items.data[0].title + "</p><p class='wp_playbuzz_meta'>Created by <span class='wp_playbuzz_author'>" + data.items.data[0].creator_name + "</span> on " + item_date( data.items.data[0].published_date ) + "</p>";
							image = data.items.data[0].img_large;
						} else {
							info  = "<p class='wp_playbuzz_title'>Playbuzz item does not exist</p><p class='wp_playbuzz_meta'>Check shortcode URL in the text editor.</p>";
							image = url + '/../img/playbuzz-placeholder.png';
						}

						// Update item on screen
						(jQuery)("#" + id).attr("src", image);
						(jQuery)(tinyMCE.activeEditor.dom.doc.body).find("#playbuzz_placeholder_" + id).attr("src", image);
						(jQuery)(tinyMCE.activeEditor.dom.doc.body).find("#playbuzz_info_" + id).html(info);

					}
				});

				// Shortcode replacement
				var output = '';
				output += '<div class="wp_playbuzz_container" contenteditable="false">';
				output += '	<div id="playbuzz_info_' + id + '" class="wp_playbuzz_info"></div>';
				output += '	<div id="playbuzz_image_' + id + '" class="wp_playbuzz_image">';
				output += '		<img id="playbuzz_placeholder_' + id + '" src="' + url + '/../img/playbuzz-placeholder.png' + '" class="mceItem wp_playbuzz_placeholder" data-mce-resize="false" data-mce-placeholder="1" />';
				output += '	</div>';
				output += '	<div id="playbuzz_embed_' + id + '" class="wp_playbuzz_embed">YOUR ITEM WILL BE EMBEDDED HERE</div>';
				output += '	<div id="playbuzz_overlay_' + id + '" class="wp_playbuzz_buttons" data-playbuzz-attr="' + encodedShortcodeAttributes + '"></div>';
				output += '	<div id="playbuzz_overlay_close_' + id + '" class="wp_playbuzz_delete"></div>';
				output += '	<div id="playbuzz_overlay_edit_' + id + '" class="wp_playbuzz_edit" data-playbuzz-attr="' + encodedShortcodeAttributes + '"></div>';
				output += '</div>';
				output += ' ';

				// Replace the shortcode with custom output
				return output;

			});

		});

		// Replace the item info box with the shortcode
		editor.on( 'GetContent', function( event ) {

			event.content = event.content.replace( /((<div class="wp_playbuzz_container"[^<>]*>)(.*?)(?:<\/div><\/div>))*/g, function( match, tag ) {

				// Extract shortcode attributes from <div data-playbuzz-attr="...">
				var data = get_attr( tag, 'data-playbuzz-attr' );

				// Create the shortcode
				if ( data ) {
					return '<p>[playbuzz-item' + data + ']</p>';
				}

				return match;

			});

		});

		// Item edit popup
		editor.on( 'click', function(e) {

			// Delete item
			if ( ( e.target.nodeName == 'DIV' ) && ( e.target.className.indexOf( 'wp_playbuzz_delete' ) > -1 ) ) {
				(jQuery)(tinyMCE.activeEditor.dom.doc.body).find("#" + tinyMCE.activeEditor.selection.getNode().id).parent().remove();
				(jQuery)( '.playbuzz_popup_overlay' ).remove();
			}

			// Edit item
			if ( ( e.target.nodeName == 'DIV' ) && ( ( e.target.className.indexOf( 'wp_playbuzz_buttons' ) > -1 ) || ( e.target.className.indexOf( 'wp_playbuzz_edit' ) > -1 ) ) ) {

				// if a=b return c
				function return_if( a, b, c ) {
					if ( a == b ) {
						result = c;
					} else {
						result = "";
					}
					return result;
				}

				// Is checkbox checked
				function is_checked( checkbox, return_value ) {
					if ( ( typeof checkbox != 'undefined' ) && ( checkbox.length ) && ( ( checkbox == true ) || ( checkbox > 0 ) || ( checkbox.toLowerCase() == "true" ) || ( checkbox.toLowerCase() == "on" ) || ( checkbox == "1" ) ) ) {
						result = return_value;
					} else {
						result = "";
					}
					return result;
				}

				// Extract shortcode attributes stored in <div data-playbuzz-attr="...">
				var attr = e.target.attributes['data-playbuzz-attr'].value;
				attr = window.decodeURIComponent( attr );

				// Set values
				var item_url      = get_attr( attr, 'url' ),
					info          = get_attr( attr, 'info' ),
					shares        = get_attr( attr, 'shares' ),
					comments      = get_attr( attr, 'comments' ),
					recommend     = get_attr( attr, 'recommend' ),
					margin_top    = get_attr( attr, 'margin-top' ),
					width         = get_attr( attr, 'width' ),
					height        = get_attr( attr, 'height' ),
					links         = get_attr( attr, 'links' ),
					tags          = get_attr( attr, 'tags' ),
					itemPath      = item_url.split( 'playbuzz.com/' ).pop(),
					itemPathArray = itemPath.split("/"),
					creatorName   = itemPathArray[0],
					gameName      = itemPathArray[1];

				// Which settings to use? site default or custom item settings
				var settings_to_use = ( ( info.length > 0 ) || ( shares.length > 0 ) || ( comments.length > 0 ) || ( recommend.length > 0 ) || ( margin_top.length > 0 ) || ( !isNaN( margin_top ) && margin_top.trim() != '' ) ) ? 'custom' : 'default';

				// Item popup
				(jQuery)("<div></div>").addClass("playbuzz_popup_overlay").appendTo("body");
				results  = "<div id='playbuzz_popup' class=''>";
				results += "	<form name='item' id='playbuzz_i_form' action='#'>";
				results += "		<div id='playbuzz_item_header'>";
				results += "			<div id='playbuzz_popup_close'></div>";
				results += "			<p class='playbuzz_item_header_text'>Playbuzz Item Settings</a>";
				results += "		</div>";
				results += "		<div id='playbuzz_item_body'>";
				results += "			<div id='playbuzz_item_preview'></div>";
				results += "			<div id='playbuzz_item_settings'>";
				results += "				<p class='playbuzz_item_settings_title'>Item Settings <span>Embedded Item Appearance</span></p>";
				results += "				<div class='playbuzz_item_settings_select'>";
				results += "					<input type='radio' name='playbuzz_item_settings' id='playbuzz_item_settings_default' value='default' " + return_if( settings_to_use, "default", " checked='checked'" ) + ">";
				results += "					<label for='playbuzz_item_settings_default'>Use site default settings <a href='options-general.php?page=playbuzz&tab=embed' target='_blank'>Configure default settings</a></label>";
				results += "					<br>";
				results += "					<input type='radio' name='playbuzz_item_settings' id='playbuzz_item_settings_custom'  value='custom'  " + return_if( settings_to_use, "custom",  " checked='checked'" ) + ">";
				results += "					<label for='playbuzz_item_settings_custom'>Custom</label>";
				results += "					<br>";
				results += "					<div class='settings_half'>";
				results += "						<input type='checkbox' id='playbuzz_item_settings_info' " + is_checked( info, " checked='checked'" ) + ">";
				results += "						<label for='playbuzz_item_settings_info'>Display item information</label>";
				results += "						<div class='description'>Show item thumbnail, name, description, creator.</div>";
				results += "						<input type='checkbox' id='playbuzz_item_settings_shares' " + is_checked( shares, " checked='checked'" ) + ">";
				results += "						<label for='playbuzz_item_settings_shares'>Display share buttons</label>";
				results += "						<div class='description'>Show share buttons with links to YOUR site.</div>";
				results += "						<input type='checkbox' id='playbuzz_item_settings_recommend' " + is_checked( recommend, " checked='checked'" ) + ">";
				results += "						<label for='playbuzz_item_settings_recommend'>Display more recommendations</label>";
				results += "						<div class='description'>Show recommendations for more items.</div>";
				results += "					</div>";
				results += "					<div class='settings_half'>";
				results += "						<input type='checkbox' id='playbuzz_item_settings_comments' " + is_checked( comments, " checked='checked'" ) + ">";
				results += "						<label for='playbuzz_item_settings_comments'>Display facebook comments</label>";
				results += "						<div class='description'>Show Facebook comments in your items.</div>";
				results += "						<input type='checkbox' id='playbuzz_item_settings_margin'>";
				results += "						<label for='playbuzz_item_settings_margin'>Site has fixed (sticky) top header</label>";
				results += "						<div class='playbuzz_item_settings_margin_top_text'>Height <input type='input' id='playbuzz_item_settings_margin_top' value='" + margin_top + "'> px</div>";
				results += "						<div class='description'>Use this if your website has top header that's always visible, even while scrolling down.</div>";
				results += "					</div>";
				results += "				</div>";
				results += "			</div>";
				results += "		</div>";
				results += "		<div id='playbuzz_item_update'>";
				results += "			<input type='hidden' id='playbuzz_item_settings_url'    value='" + item_url   + "'>";
				results += "			<input type='hidden' id='playbuzz_item_settings_links'  value='" + links      + "'>";
				results += "			<input type='hidden' id='playbuzz_item_settings_tags'   value='" + tags       + "'>";
				results += "			<input type='hidden' id='playbuzz_item_settings_width'  value='" + width      + "'>";
				results += "			<input type='hidden' id='playbuzz_item_settings_height' value='" + height     + "'>";
				results += "			<div class='playbuzz_item_cancel_button'>Cancel</div>";
				results += "			<div class='playbuzz_item_update_button'>Update Item</div>";
				results += "		</div>";
				results += "	</form>";
				results += "</div>";
				(jQuery)( '.playbuzz_popup_overlay' ).append( results );

				// Item Preview
				(jQuery).ajax({
					url      : "https://restapi.playbuzz.com/v1/items/?size=1",
					type     : "get",
					dataType : "json",
					data     : {
						creator_name_seo : creatorName,
						item_name_seo    : gameName,
						moderation       : false
					},
					success  : function( data ) {

						if ( data.items.total > 0 ) {

							// Data output
							var results = '';
							results += "<table>";
							results += "<tbody>";
							results += "	<tr valign='top'>";
							results += "		<td>";
							results += "			<p class='playbuzz_item_thumb'><img src='" + data.items.data[0].img_large + "'></p>";
							results += "		</td>";
							results += "		<td>";
							results += "			<p class='playbuzz_item_title'>" + data.items.data[0].title + "</p>";
							results += "			<p class='playbuzz_item_meta'>Created by <span>" + data.items.data[0].creator_name + "</span> on " + item_date( data.items.data[0].published_date ) + "</p>";
							results += "			<p class='playbuzz_item_desc'>" + data.items.data[0].description + "</p>";
							results += "			<p class=''>";
							results += "				<span class='playbuzz_item_views'>" + data.items.data[0].total_views + "</span>";
							results += "				<span class='playbuzz_item_type'>" + item_type( data.items.data[0].item_type ) + "</span>";
							results += "				<span class='playbuzz_item_link'><a href='" + data.items.data[0].playbuzz_url + "' target='_blank'>Preview item</a></span>";
							results += "			</p>";
							results += "		</td>";
							results += "	</tr>";
							results += "</tbody>";
							results += "</table>";

							// Update the results on the screen
							(jQuery)( '#playbuzz_item_preview' ).empty();
							(jQuery)( '#playbuzz_item_preview' ).append( results );
						}

					},
					error : function( data ) {
						(jQuery)( '#playbuzz_item_preview' ).empty();
					}
				});

				// Set/Change fields visibility
				function settings_visibility() {
					if ( (jQuery)("input[type='radio'][name='playbuzz_item_settings']:checked").val() == 'default' ) {
						(jQuery)(".settings_half").addClass("settings_disabled");
						(jQuery)("#playbuzz_item_settings_info").prop('disabled', true);
						(jQuery)("#playbuzz_item_settings_shares").prop('disabled', true);
						(jQuery)("#playbuzz_item_settings_recommend").prop('disabled', true);
						(jQuery)("#playbuzz_item_settings_comments").prop('disabled', true);
						(jQuery)("#playbuzz_item_settings_margin").prop('disabled', true);
						(jQuery)("#playbuzz_item_settings_margin_top").prop('disabled', true);
					} else {
						(jQuery)(".settings_half").removeClass("settings_disabled");
						(jQuery)("#playbuzz_item_settings_info").prop('disabled', false);
						(jQuery)("#playbuzz_item_settings_shares").prop('disabled', false);
						(jQuery)("#playbuzz_item_settings_recommend").prop('disabled', false);
						(jQuery)("#playbuzz_item_settings_comments").prop('disabled', false);
						(jQuery)("#playbuzz_item_settings_margin").prop('disabled', false);
						if ( (jQuery)("#playbuzz_item_settings_margin").prop( "checked" ) ) {
							(jQuery)("#playbuzz_item_settings_margin_top").prop('disabled', false);
						} else {
							(jQuery)("#playbuzz_item_settings_margin_top").prop('disabled', true);
						}
					}
				}
				settings_visibility();
				(jQuery)("input[type='radio'][name='playbuzz_item_settings']:radio").change(function(){
					settings_visibility();
				});

				// Margin-top
				if ( !isNaN( margin_top ) && margin_top.trim()!='' ) {
					(jQuery)("#playbuzz_item_settings_margin").prop('checked', true);
					(jQuery)("#playbuzz_item_settings_margin_top").prop('disabled', false);
				} else {
					(jQuery)("#playbuzz_item_settings_margin_top").prop('disabled', true);
				}

				// Change margin top
				(jQuery)("#playbuzz_item_settings_margin").change(function(){
					if ( (jQuery)(this).is(':checked') ) {
						(jQuery)("#playbuzz_item_settings_margin_top").prop('disabled', false);
					} else {
						(jQuery)("#playbuzz_item_settings_margin_top").prop('disabled', true);
					}
				});

				// Close Popup
				(jQuery)("#playbuzz_popup_close").click(function() {
					(jQuery)( '.playbuzz_popup_overlay' ).remove();
				});
				(jQuery)(".playbuzz_item_cancel_button").click(function() {
					(jQuery)( '.playbuzz_popup_overlay' ).remove();
				});

				// Click Update button
				(jQuery)(".playbuzz_item_update_button").click(function( e ) {

					// start shortcode tag
					var shortcode_str = '[playbuzz-item';

					// use site default settings or custom settings
					default_or_custom = (jQuery)("input[type='radio'][name='playbuzz_item_settings']:checked").val()

					// add "url"
					new_item_url = (jQuery)("#playbuzz_item_settings_url");
					if ( typeof new_item_url != 'undefined' && new_item_url.length && new_item_url.val() != '' )
						shortcode_str += ' url="' + new_item_url.val() + '"';

					// add "info"
					new_info = (jQuery)("#playbuzz_item_settings_info").prop( "checked" );
					if ( default_or_custom == 'custom' )
						shortcode_str += ' info="' + new_info + '"';

					// add "shares"
					new_shares = (jQuery)("#playbuzz_item_settings_shares").prop( "checked" );
					if ( default_or_custom == 'custom' )
						shortcode_str += ' shares="' + new_shares + '"';

					// add "comments"
					new_comments = (jQuery)("#playbuzz_item_settings_comments").prop( "checked" );
					if ( default_or_custom == 'custom' )
						shortcode_str += ' comments="' + new_comments + '"';

					// add "recommend"
					new_recommend = (jQuery)("#playbuzz_item_settings_recommend").prop( "checked" );
					if ( default_or_custom == 'custom' )
						shortcode_str += ' recommend="' + new_recommend + '"';

					// add "links"
					new_links = (jQuery)("#playbuzz_item_settings_links");
					if ( typeof new_links != 'undefined' && new_links.length && new_links.val() != '' )
						shortcode_str += ' links="' + new_links.val() + '"';

					// add "tags"
					new_tags = (jQuery)("#playbuzz_item_settings_tags");
					if ( typeof new_tags != 'undefined' && new_tags.length && new_tags.val() != '' )
						shortcode_str += ' tags="' + new_tags.val() + '"';

					// add "width"
					new_width = (jQuery)("#playbuzz_item_settings_width");
					if ( typeof new_width != 'undefined' && new_width.length && new_width.val() != '' && new_width.val() != 'auto' )
						shortcode_str += ' width="' + new_width.val() + '"';

					// add "height"
					new_height = (jQuery)("#playbuzz_item_settings_height");
					if ( typeof new_height != 'undefined' && new_height.length && new_height.val() != '' && new_height.val() != 'auto' )
						shortcode_str += ' height="' + new_height.val() + '"';

					// add "margin-top"
					new_margin_top = (jQuery)("#playbuzz_item_settings_margin_top");
					if ( default_or_custom == 'custom' && typeof new_margin_top != 'undefined' && new_margin_top.length && new_margin_top.val() != '' && new_margin_top.val() != '0' && new_margin_top.val() != '0px' && (jQuery)("#playbuzz_item_settings_margin").is(':checked') )
						shortcode_str += ' margin-top="' + new_margin_top.val() + '"';

					// End shortcode tag
					shortcode_str += ']';

					// Insert shortcode to the editor
					(jQuery)(tinyMCE.activeEditor.dom.doc.body).find("#" + tinyMCE.activeEditor.selection.getNode().id).parent().remove();
					tinyMCE.activeEditor.selection.setContent( shortcode_str );
					(jQuery)( '.playbuzz_popup_overlay' ).remove();

				});

			}

		});

	});

})();
