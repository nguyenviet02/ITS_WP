=== Ultimate Post List ===
Contributors: kybernetikservices, Hinjiriyo
Donate link: https://www.paypal.com/donate?hosted_button_id=NSEQX73VHXKS8
Tags: authors, avatars, css, featured image, first image, grid, list, options, shortcode, thumbnail, widget, load more
Requires at least: 4.0
Requires PHP: 5.2
Tested up to: 6.7
Stable tag: 5.2.7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make up custom-tailored preview lists of the contents easily and place them in widget areas and post contents.

== Description ==

Make up custom-tailored preview lists of your website's contents easily and place them in widget areas and post contents.

Promote your website’s content with many kinds of post lists. No programming skills are necessary. No fiddling with templates. With just a few keyboard entries and clicks you get a customized posts list.

The plugin Ultimate Post List for WordPress gives you an easy-to-use toolbox to make lists of posts. You can insert the lists

* as **widgets in every widget area** per drag'n'drop easily
* via **configurable shortcodes in every post content**
* via **calls of the function upl_print_list()** in PHP code

You can switch between a **vertical list layout** or a **responsive grid layout**. A few options help you to get a nice layout without any need to code.

You can **filter** the posts by their **post type** "Post" or/and "Page" and by multiple **categories**. For filtering by custom post types check out [Ultimate Post List Pro](https://www.kybernetik-services.com/shop/wordpress/plugin/ultimate-post-list-pro/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=ultimate-post-list&utm_content=update-notice-readme).

You can switch on a **"Load more posts"** link or button which loads further posts without leaving the page. Its text is controlled easily for you.

The plugin is **multi-widget capable**. That is, you can have several widgets of Ultimate Post List on your blog, each with its own settings!

You can clone (duplicate, copy) every list with a single click on the action link.

The plugin is available in English, German (Deutsch) and Greek (Ελληνικά). It does not collect any personal data, so it is ready for EU General Data Protection Regulation (GDPR) compliance.

= Demo =
You want to test Ultimate Post List before installing on your site? Try it out on your individual and free dummy site and [click here](https://demo.tastewp.com/ultimate-post-list).

= Options you can set =

The free version of Ultimate Post List offers you many options to type and click customized post lists easily. These are the options:

* **List Options**
    * **List Display Options**
        * List Visibility
        * Text if no posts
    * **List Title Options**
        * List title
        * URL of list title
        * HTML element of list title
* **Post List Options**
    * **Post List Options In General**
        * Number of posts
        * Posts offset
        * Hide current viewed post in list
        * Show sticky posts on top of the list
    * **Posts Sort Order**
        * **Order by**
            * Post date
            * Post title
            * Post author
        * **Order direction**
            * Ascending
            * Descending
* **Post Filter Options**
    * **Post Type Filter**
        * Show posts of selected types: posts or/and pages
    * **Category Filter**
        * Show posts of selected categories only
        * Show only posts that are in all selected categories
* **Post List Item Options**
    * **Post Data Order**
        * Position of post thumbnail
        * Position of post title
        * Position of post date
        * Position of post author name
        * Position of post excerpt
    * **Post Display Options**
        * Show post thumbnail (default)
        * Show post title (default)
        * Show post date
        * Show post author name
        * Show post excerpt
    * **Post Links Options**
        * Set post title clickable (default)
        * Set post thumbnail clickable (default)
        * Set post date clickable, pointing to the month archive
        * Set post author clickable, pointing to the author&#8217;s archive
        * Set post excerpt clickable
        * Open post links in new windows
    * **Post Title Options**
        * Maximum length of post title
        * Text after shortened title
    * **Post Date Options**
        * Format of the post date (over 100 options available)
    * **Post Thumbnail Options**
        * **Source of the post thumbnail**
            * Featured image
            * First post content image if previously uploaded to the media library
            * Featured image if the first post content image is not available
            * First post content image if the featured image is not available
            * Avatars of post authors
        * Use post title as the alternative text for the thumbnail
        * **Use default thumbnail if no image could be ascertained**
            * URL of default thumbnail
            * Thumbnail size 
        * Width of thumbnail in px
        * Height of thumbnail in px
        * Use aspect ratios of original images
        * **Alignment of thumbnail**
            * Align left
            * Align center
            * Align right
        * Top image margin width in px
        * Bottom image margin width in px
        * Left image margin width in px
        * Right image margin width in px
		* Flow of text at the thumbnail (wrap right/left or side by side)
    * **Post Excerpt Options**
		* Maximum length of post excerpt
		* Text after shortened excerpt
		* Ignore post excerpt field as excerpt source
		* Ignore post content as excerpt source
* **&#8220;More&#8221; Element Options**
    * **&#8220;More&#8221; Element Appearance**
        * Show a clickable &#8220;More&#8221; element for loading further list items at the bottom of the list
        * **&#8220;More&#8221; element type**
            * Show element as a link
            * Show element as a button
        * Label of &#8220;More&#8221; element
        * Show icon while new posts are loaded
        * **Icon style**
            * **Small icons**
                * Small gray circle with rotating dot
                * Small turning wheel
            * **Big icons**
                * Big gray circle with rotating dot
                * Big turning wheel
        * Text that appears when no further posts have been found
* **List Layout Options**
    * List Layout Type: vertical list or responsive grid
    * Grid item width in px
    * Minimal height of grid item in px
    * **List Item Margin Options**
        * Top item margin in px
        * Bottom item margin in px
        * Left item margin in px
        * Right item margin in px

= Additional options in the Pro version =

The Pro version has all the options of the free version and many additional options for layouts, numbered lists, colors, custom post types and much more. [Go to Ultimate Post List Pro](https://www.kybernetik-services.com/shop/wordpress/plugin/ultimate-post-list-pro/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=ultimate-post-list&utm_content=update-notice-readme).

Pro users get premium support whilst free support is offered in the WordPress forums in our spare time.

= Support =

Support for this plugin will be provided in the form of Product Support. This means that we intend to fix any confirmed bugs, listen to ideas for this plugin and improve the user experience when enhancements are identified and can reasonably be accommodated.

Pro users get premium support whilst free support is offered in the WordPress forums in our spare time. If you are having trouble with this plugin in your particular installation of WordPress, we will not be able to help you troubleshoot the problem.

== Frequently Asked Questions ==

= Useful hints for developers =

Every Ultimate Post List offers you some IDs and class names to address the HTML elements with CSS. They are:

`div.upl-list`
is every Ultimate Post List.

`div#upl-list-%d`
is the list with %d as the list ID set by WordPress

`li.upl-sticky`
is every list item that contains a sticky post.

`div.upl-post-thumbnail`
is every container of a post thumbnail in the list.

`div.upl-post-thumbnail img`
is every post thumbnail in the list.

`div.upl-post-title`
is every post title in the list.

`div.upl-post-date`
is every post date in the list.

`div.upl-post-author`
is every post author in the list.

`div.upl-post-excerpt`
is every post excerpt in the list.

`form#%d-form`
is the "more" element with %d as the list ID set by WordPress.

`.upl-list-button`
is every "more" element, as a link or as a button in the list.

`a#%d-button`
is the "more" link with %d as the list ID set by WordPress.

`input#%d-button`
is the "more" button with %d as the list ID set by WordPress.

`img#%d-spinner`
is the spinning wheel image with %d as the list ID set by WordPress.

= Shortcode Attributes =

To place a Ultimate Post List in the content of a post or page a shortcode is available. The shortcode keyword for this plugin is `ultimate-post-list`. 

The attribute **`id`** with the ID of a published list is **required**. Otherwise there is no output.
Example: This shortcode prints the list of ID 48 as set on the List Edit page.
`[ultimate-post-list id="48"]`

You can add some attributes to overwrite the respective settings of the list.

**`list_title`**
Sets the headline of the list. To remove a headline use an empty string.
Example: `list_title="Your Site, Your Way"`
Example: `list_title=""`

**`included_categories`**
Displays only posts of the categories specified by their IDs, slugs or names, separated by commas.
Example: `included_categories="323,245,788"`
Example: `included_categories="lorem-ipsum,fringilla-mauris,dolor-sit-amet"`
Example: `included_categories="Lorem ipsum,Fringilla mauris,Dolor sit amet"`

You can use specifiers of different types in a comma-separated list.
Example: `included_categories="Lorem ipsum,245,dolor-sit-amet"`

Example: `[ultimate-post-list id="48" list_title="Your Site, Your Way" included_categories="News"]`

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Ultimate Post List'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard
5. Go to 'Ultimate Post List'

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `ultimate-post-list.zip`
from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard
6. Go to 'Ultimate Post List'

= Using FTP =

1. Download `ultimate-post-list.zip`
2. Extract the `ultimate-post-list`
directory to your computer
3. Upload the `ultimate-post-list`
directory to the `/wp-content/plugins/`
directory
4. Activate the plugin in the Plugin dashboard
5. Go to 'Ultimate Post List'

== Screenshots ==

1. The edit page of the Ultimate Post List in english language
2. The widget form of an Ultimate Post List in english language
3. Examples of Ultimate Post Lists on one page: lists as content and as widgets
4. Example of an Ultimate Post List widget with avatars of the post authors as thumbnails

== Changelog ==

= 5.2.7.1 =
* fixed debugging information where it is shown

= 5.2.7 =
* fixed pages won't be listed (thanks to @brvnbld; https://wordpress.org/support/topic/pages-not-showing-in-the-list/)

= 5.2.6 =
* fixed error on loading help page
* fixed not loading JS files anymore
* added minified JS files to improve loading time
* added compatibility with WordPress 5.8
* tested successfully with WordPress 6.0

= 5.2.5 =
* fixed widget was not shown in the admin area

= 5.2.4 =
* const names standardized
* added autoload class
* tested successfully with WordPress 5.7.1

= 5.2.3 =
* new branding
* Tested successfully with WordPress 5.6.2
* Tested successfully with WordPress 5.7

= 5.2.2 =
* Revised link text for posts with no title
* Tested successfully with WordPress 5.6

= 5.2.1 =
* Fixed broken 'more' link if the theme supports 'navigation-widgets'
* Changed order of action links 
* Corrected outdated translation
* Tested successfully with WordPress 5.5.3

= 5.2.0 =
* Added NAV element around the post list if the theme supports the 'navigation-widgets' type (introduced in WP 5.5)
* Added aria-label to the NAV element for better accessibility
* Added loading=”lazy” attribute to the default image (introduced in WP 5.5)
* Revised translations for WP 5.5
* Fixed wrong sanitazion of checkbox groups
* Updated translations
* Tested successfully with WordPress 5.5

= 5.1.2 =
* Refined the description about first post content images 
* Refactored rendering of HTML element attributes
* Updated translations

= 5.1.1 =
* Revised options rendering to enable HTML element attributes
* Tested successfully with WordPress 5.4

= 5.1.0 =
* Added option for using the post title as the alternative text for the thumbnail
* Added line breaks before descriptions on the list edit page
* Added fieldset margins in admin.css
* Refactored Javascript in public JS file
* Updated translations
* Updated screenshot of Edit Page

= 5.0.0 =
* Refactored for huge performance improvements
* Revised Help page
* Updated translations
* Tested successfully with WordPress 5.2.2

= 4.4.1 =
* Added link to Help page under the shortcode box on a list edit page
* Moved text-thumbnail-wrap option to thumbnail options
* Fixed wrong screenshot of the shortcode box
* Updated translations
* Updated screenshot of Edit Page
* New in the Pro version: Support of shortcodes in post excerpts

= 4.4.0 =
* Added option to use only except fields as the source for excerpts
* Updated translations
* Updated screenshot of Edit Page

= 4.3.1 =
* Added copy button at the shortcode field on the list edit page
* Fixed wrong list title if the list is in the post content
* Updated translations
* Updated screenshot of Edit Page

= 4.3.0 =
* Added options for excerpts
* Fixed wrong checkbox default values
* Fixed erroneous breaks in narrow lists
* Introduced in Pro version: [Numbered lists](https://www.kybernetik-services.com/shop/wordpress/plugin/ultimate-post-list-pro/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=ultimate-post-list&utm_content=update-notice-readme)
* Updated translations
* Updated screenshot of Edit Page

= 4.2.1 =
* Fixed missing output of the list

= 4.2.0 =
* Added post type filter
* Added category filter
* Revised columns adding function for the lists overview page in the backend
* Updated translations
* Updated screenshot of Edit Page

= 4.1.2 =
* Fixed missing list title in the output

= 4.1.1 =
* Revised image size selection if a registered image size name is used
* Revised paths to public CSS file
* Changed wp_get_attachment_image_src() to wp_attachment_is_image()
* Tested successfully with WordPress 5.0.3

= 4.1 =
* Added option to display the text next to the thumbnail instead of floating around  it
* Added new function upl_print_list() to call a list in PHP code
* Updated translations
* Tested successfully with WordPress 4.9.4

= 4.0.3 =
* Added greek translation. Thank you, Kostas Arvanitidis!
* Updated translations due to WordPress 4.9
* Tested successfully with WordPress 4.9.1

= 4.0.2 =
* Fixed missing initialization of UPLP options in the frontend

= 4.0.1 =
* Fixed missing detection of custom post types for the list edit page
* Added 'Requires PHP' info in readme.txt

= 4.0 =
* Added "Load more posts" feature (display further posts without page reload)
* Added options for "Load more" button
* Fixed undesired subpage in UPL admin menu if plugin "WordPress Editorial Calendar" is active
* Updated translations

= 3.4.1 =
* Fixed static width of list items if item width is greater than viewport width
* Tested successfully with WordPress 4.8.1

= 3.4 =
* Revised sanitations for texts and URLs on the pages
* Revised translations
* Set activation message as dismissible
* Tested successfully with WordPress 4.8

= 3.3 =
* Added list title option; **important**: Please add the list title in this option if it was shown in post content
* Reordered menu items in navigation; list of lists is main page now
* Revised Help page
* Revised message on plugin activation
* Updated translations

= 3.2 =
* Added on lists table: sortable "Last Modified" column
* Improved on lists table: "Author" column made sortable 

= 3.1.1 =
* Improved performance in getting avatars

= 3.1 =
* Added avatars as new source of thumbnails
* Tested successfully with WordPress 4.7.3

= 3.0.4 =
* Removed 3rd-party metaboxes from list edit page
* Fixed unnecessary loading of CSS and JS on all pages except list edit page
* Tested successfully with WordPress 4.7.2

= 3.0.3 =
* Removed sanitation of post title

= 3.0.2 =
* Added check for loading javascript and CSS only on list edit page
* Revised widget template for more conformity to WP standard widget output
* Tested successfully with WordPress 4.7.1

= 3.0.1 =
* Added new labels to the 'Ultimate Post List' post type, introduced with WordPress 4.7
* Tested successfully with WordPress 4.7
* Updated translations

= 3.0 =
* Added action link 'Copy' to duplicate lists
* Added CSS width limit for selection field in widget control form
* Improved sanitation in admin page: Added check for empty input or no-array input
* Updated translations

= 2.1 =
* Added option for post date format
* Updated translation

= 2.0 =
* Added options for grid layout
* Updated translation
* Tested successfully with WordPress 4.6.1

= 1.2 =
* Revised uninstall function for WordPress 4.6 due to the introduction of WP_Site_Query class
* Fixed translation for WP 4.6
* Tested successfully with WordPress 4.6

= 1.1.0 =
* Fixed: the list always appeared first before other content
* Tested successfully with WordPress 4.5.3

= 1.0.2 =
* Fixed missing descriptions at checkboxes on the options page
* Fixed: outdated translations for post statusses
* Tested successfully with WordPress 4.5.2

= 1.0.1 =
* Shortened readme.txt

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 5.2.2 =
Tested successfully with WordPress 5.6

= 5.2.1 =
Fixed broken 'more' link, small improvements, tested with WordPress 5.5.3

= 5.2.0 =
Revisions due to WordPress 5.5, tested successfully with WordPress 5.5

= 5.1.2 =
Refined the description about first post content images, refactored rendering of HTML element attributes

= 5.1.1 =
Revised options rendering to enable HTML element attributes, tested with WordPress 5.4

= 5.1.0 =
Added option for using the post title as the alternative text for the thumbnail, small revisions

= 5.0.0 =
Refactored for huge performance improvements, revised Help page, tested with WordPress 5.2.2

= 4.4.1 =
Added link to Help page, moved text-thumbnail-wrap option, minor revisions, tested with WP 5.2.1

= 4.4.0 =
Added option to use only except fields as the source for excerpts, updated translations

= 4.3.1 =
Added shortcode copy button, fixed wrong list title, updated translations

= 4.3.0 =
Added options for excerpts, fixed two minor errors, updated translations

= 4.2.1 =
Fixed missing output of the list

= 4.2.0 =
Added post type filter and category filter, revised columns adding function, updated translations

= 4.1.2 =
Fixed missing list title in the output

= 4.1.1 =
Small revisions, tested with WordPress 5.0.3

= 4.1 =
Added option for text/image circulation, new function upl_print_list(), tested with WordPress 4.9.4

= 4.0.3 =
Added greek translation, updated WP 4.9 translations, tested with WordPress 4.9.1

= 4.0.2 =
Fixed missing UPL options initialization in the frontend

= 4.0.1 =
Fixed missing detection of custom post types, added 'Requires PHP' info in readme.txt

= 4.0 =
Added "Load more posts" feature

= 3.4.1 =
Fixed static width of list items if item width is greater than viewport width, tested with WP 4.8.1

= 3.4 =
Revised sanitations and translations, tested with WordPress 4.8

= 3.3 =
Added list title option, reordered menu items, revised Help page, revised message on plugin activation

= 3.2 =
Added sortable "Last Modified" column, "Author" column made sortable 

= 3.1.1 =
Improved performance in getting avatars

= 3.1 =
Added avatars, tested with WP 4.7.3

= 3.0.4 =
Removed 3rd-party metaboxes, precise loading of CSS and JS

= 3.0.3 =
Removed sanitation of post title

= 3.0.2 =
Slight improvments on CSS loading and HTML output

= 3.0.1 =
Adapted to WordPress 4.7

= 3.0 =
Added action link 'Copy' to duplicate lists

= 2.1 =
Added option for post date format

= 2.0 =
Added options for grid layout, tested with WP 4.6.1

= 1.2 =
Fixed translation for WP 4.6, tested with WP 4.6

= 1.1.0 =
Fixed wrong placement of the list within content, tested with WP 4.5.3

= 1.0.2 =
Fixed errors on the edit page, tested with WP 4.5.2

= 1.0.1 =
Shortened readme.txt

= 1.0.0 =
Initial release.