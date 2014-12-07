=== Playbuzz Playful Content ===
Contributors: playBuzz
Tags: Playbuzz, playful content, feed, feeds, embed, oembed, content, viral, related, syndication, related content, quiz, quizzes, test yourself, list, poll, Personality Quiz, personality, entertainment, celebs, celebrities, celebrity, animals
Requires at least: 3.7
Tested up to: 4.0.1
Stable tag: 0.5.0

Playbuzz lets you embed customized Playful Content such as quizzes, listicles, polls and more!

== Description ==
The playbuzz plugin enables site owners to easily embed Playful Content items such as quizzes, lists, polls and more, and offer them as a native part of their site's offering.

Upgrade your site to include highly engaging viral content, using playbuzz's Playful Content. You can have all this content on your site in under a minute!

= Features =
* Access to hundreds of trivia quizzes, personality quizzes, lists, etc.
* Content is available in multiple categories: Entertainment, Sports, Celebrities, Music, Animals, and more.
* Display only the content relevant to your site, using tag filters.
* Create your own Playful Content items and embed them to your site.
* Everything is free, no subscription fee.

= Embedding options =
Playbuzz plugin has two embedding options:

**Embed specific Item**

Choose any Playful Content item (or create your own!) from [playbuzz.com](https://www.playbuzz.com), copy the item URL and paste it into your text editor:

`https://www.playbuzz.com/llamap10/how-weird-are-you`

Check the visual editor to make sure the item loads.

Advanced users can use playbuzz shortcode to embed items and tweak them with shortcode attributes:

`[playbuzz-item url="https://www.playbuzz.com/llamap10/how-weird-are-you" comments="false"]`

**Embed a section**

Create a playful section on your site and embed a list of Playful Content items. Just select a category from [playbuzz.com](https://www.playbuzz.com) or create your own tag, copy the URL and paste it into your text editor:

`https://www.playbuzz.com/fun`

Check the visual editor to make sure the section loads.

To customize your section use the section shortcode using advanced attribute:
`[playbuzz-section tags="fun,cats" width="600"]`

= More Information =
For any questions or more information please [contact us](https://www.playbuzz.com/contact).

== Installation ==

= Minimum Requirements =
* WordPress 3.7 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= Installation =
1. In your WordPress Dashboard go to "Plugins" -> "Add Plugin".
2. Search for "Playbuzz".
3. Install playbuzz plugin by pressing the plugin "Install" button.
4. Activate the playbuzz plugin.
5. Configure the plugin by going to the "Settings" -> "Playbuzz".

= Updating =
* Use WordPress automatic updates to upgrade to the latest version. Ensure to backup your site just in case.

== Frequently Asked Questions ==

= Why should I embed content from the playbuzz network? =
Our content is created by our network of partners and our editorial staff. We feature highly engaging items around many popular topics. Embedding these items on your site is likely to boost all of your engagement metrics - page views, ad impressions, time spent online, sharing rate, etc. Above all, it enhances your site’s content offering by complementing it with content packaged in an extremely engaging and viral way.

= Does playbuzz help me make my content viral? =
Absolutely. Our plugin includes an option to share each content item on social networks such as Facebook and Twitter. Any click on these share buttons will create a share link directing users to your website.

= Does playbuzz work on mobile and tablet versions of my site? =
Yes. Playbuzz's UI is responsive and mobile compatible.

= Can I create my own content? =
Absolutely. We encourage you to create original Playful Content using https://www.playbuzz.com/create

= Does this plugin slow down my website? =
No. Test and see for yourself. playbuzz is using a large content delivery network to ensure prompt delivery.

= How do i embed playbuzz content? =
Two ways to embed playbuzz content:
1. For basic usage you can embed content using nothing but a URL. Just copy the item/section URL from https://www.playbuzz.com and paste it to your post.
2. Advanced users can use playbuzz shortcodes using attributes to tweak the result.

= What shortcode attributes can i use? =
You can find the full list of attributes in "Settings" -> "Playbuzz" -> "Help"

= I've got more questions! =
For more information please [contact us](https://www.playbuzz.com/contact).

== Screenshots ==
1. Plugin "Getting Started" screen.
2. Plugin "General Embed Settings" screen.
3. Easy to embed single content items. Simply click playbuzz button and add the content URL.
4. Customize content settings popup (e.g item info ; sharing  ; comments).
5. See the playbuzz placeholder in the visual editor to indicate where the item will be embedded. Double click the image to customize the item settings.
6. Switch to text editor to see and edit the simple playbuzz shortcode.
7. Game preview on your WordPress website.
8. Embedded "Food" section on your WordPress website.

== Changelog ==

= 0.5.0 =
* oEmbed: Add playbuzz oEmbed support to WordPress, to embed items using nothing but a URL
* Security: use 'https' everywhere
* Security: don't allow directly file call
* Update all the graphics - logo's, icons, images, screen shots ect
* Minimum Requirements: WordPress 3.7 or greater

= 0.4.1 =
* Shortcodes: Add 'links' parameter to allow user's to open clicked items in new pages

= 0.4.0 =
* Editor: Add playbuzz button to the visual editor, making it easy to create customizable playbuzz shortcodes
* Editor: Inside the visual editor, replace playbuzz shortcodes with a placeholder image to indicate visually where the item will be embedded
* Editor: Show shortcode settings popup when clicking the playbuzz shortcodes placeholder image
* Uninstall: Delete site option in multisite installation
* i18n: Update hebrew (he_IL) translation

= 0.3.4 =
* Admin: UI css fix for older WordPress version (3.7 and before)
* Admin: Fix debug mode notices
* Widget: Fix undefined widget default values
* Widget: Use 6 consistent tags across the plugin
* Widget: Use page dropdown in the recommendations widget

= 0.3.3 =
* Widget: Use page dropdown in the recommendations widget

= 0.3.2 =
* Widget: Add 'links' parameter to the recommendations widget

= 0.3.1 =
* Bug fix: after update to v0.3, no "embeddedon' defined ; the content not shown, unless you actively go to the plugin setting page and press "save changes" ; this fix shows the embedded content without going to the settings page

= 0.3.0 =
* Verified compatibility up to WordPress 4.0
* Admin: Setting page design overhaul
* New shortcodes: [playbuzz-item url=""]
* New shortcodes: [playbuzz-section tag=""]
* Old shortcodes are still in use for backwards compatibility
* New option to control the "WordPress Theme Visibility"
* Update all embed scripts
* Use embed scripts in CDN
* i18n: Fix translation bug
* i18n: Update hebrew (he_IL) translation

= 0.2.0 =
* Verified compatibility with WordPress 3.8.2
* Added new categories!
* Separate control for Comments and Share Buttons (Previous Social settings is obsolete)
* Websites with top floating bars can now set margin for playbuzz score bar

= 0.1.5 =
* Bug fix - allow to embed code in post and pages (not only posts)
* i18n: Better Internationalization (I18n) support
* i18n: Update hebrew (he_IL) translation

= 0.1.4 =
* Enabled the embedding of Playful hub in pages rather than posts only
* Fixed an issue with EditorsPick tags

= 0.1.3 =
* Solved a problem with WordPress installation on IIS

= 0.1.2 =
* Widget: Playbuzz widget now support a title
* Admin: Activation/Deactivation saves settings
* Readme: Added FAQ section

= 0.1.1 =
* Admin: Improved plugin installations scripts
* Admin: Added helpers in the plugin's settings page
* Readme: Updated readme with new information

= 0.1.0 =
* Initial release
* Widget: Playbuzz recommendations and related playful content links
* Shortcodes: [playbuzz-game] / [playbuzz-post]
* Shortcodes: [playbuzz-hub] / [playbuzz-archive]
* Shortcodes: [playbuzz-related] / [playbuzz-recommendations]
* Admin: Settings Page - API Key, data provider, ect.
* Admin: Playbuzz Games - configure the playful games
* Admin: Playbuzz Recommendations - insert related playful content links and recommendations to posts header/footer
* Admin: Shortcodes documentation
