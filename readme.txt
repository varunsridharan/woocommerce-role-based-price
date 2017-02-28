=== WC Role Based Price ===
Contributors: varunms,arnis.arbidans
Author URI: http://varunsridharan.in/
Plugin URL: https://wordpress.org/plugins/woocommerce-role-based-price/
Tags: WooCommerce,Quick Dontion,quick donation,online donation,wordpress donation,simple donation,donation form,WC donation,Online Payment,Payment,Online,Donate,Monthly Goal,affiliate, cart, checkout, commerce, configurable, digital, download, downloadable, e-commerce, ecommerce, inventory, reports, sales, sell, shipping, shop, shopping, stock, store, tax, variable, widgets, woothemes, wordpress ecommerce,discounts, prices, roles, wholesale, woocommerce,advertising, bogo, deals, discount, dynamic pricing, group pricing, marketing, Membership, price, promotion, role
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=36Y7KSYPF7KTU
Requires at least: 3.0
Tested up to: 4.7.1
WC requires at least: 2.0
WC tested up to: 2.7
Stable tag: 3.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html 

Sell product in different price for different user role based on your settings.

== Description ==
This plugin can make your simple WooCommerce shop in to multi currency and price level marketplace where products can be offered at different prices for different customer groups. 
For example, Subscribers or Contributors get different prices than new customers. Differentiate your registered users to get your shop more attractive. New roles can be created and managed. 


Latest version have been greatly improved for smoother and more speedy functioning, all user interfaces have been improved for more friendly user/webmaster experience. It is now WPML ready straight of of box. 

We have added built-in plugin extension marketplace for more PRO level functions that not all might need but we think they are very useful. Please check full feature list below.

[youtube https://www.youtube.com/watch?v=rlX8okqNfs4]

We have worked on this plugin for last 3 years and so far it have been all free but in order for us to continue with this plugin development we had to move few of its’ features/extensions in to PRO(paid) version that is available for very generous price of $59 (+$20 for extended 12 month support) here: https://codecanyon.net/item/woocommerce-role-based-pricing-pro/14120734


= Features + Add-Ons =
* User Friendly UI
* Simple , Variable / Variation , Grouped , External Product Type Supported
* Supports Regular & Selling Price
* Works With WPML
* Developer Friendly
* Easy To Create Addons 
* Aelia Currency Switcher Integration
* Price & AddToCart Visiablity
* Shortcode to get product price

= Pro Features + Add-ons =
* Schedule Selling Price 
* Dynamic Pricing
* Role Based Payment Gateway Blocker
* Role Based Product Blocker
* RBP temporary custom link access 
* List Role Pricing Table
* WPML Currency Switcher Integration
* Bulk Price Updater Integration
* WPAllImport Integration


= Integration =
* Integration  To <a href="https://aelia.co/shop/currency-switcher-woocommerce/" > Aelia Currency Swticher  </a>
* Integration  To <a href="http://www.wpallimport.com/" > WP All Import Plugin </a>
* Integration  To <a href="https://codecanyon.net/item/woocommerce-bulk-price-updater/14276992" >WC Bulk Price Updater </a>
* Integration  To <a href="http://wpml.org/" >WPML Currency Switcher </a>
 

= Get Product's Regular Price With Custom User Role =
`[wc_rbp id='99' role='administrator' price='regular_price']`

= Get Product's Selling Price Based On Logged In User =
`[wc_rbp id='99' role='current' price='selling_price']`

= Get Product's Base Regular Price =
`[wc_rbp id='99' price='product_regular_price']`

= Get Product's Base Selling Price =
`[wc_rbp id='99' price='product_selling_price']`

= Get Product's Regular Price With Custom User Role =
`[wc_rbp id='99' role='administrator' price='regular_price']`

= Get Product's Selling Price Based On Logged In User =
`[wc_rbp id='99' role='current' price='selling_price']`

= Get Product's Base Regular Price =
`[wc_rbp id='99' price='product_regular_price']`

= Get Product's Base Selling Price =
`[wc_rbp id='99' price='product_selling_price']`


= Shortcode Variables Explained =
* `id=99` you need to replace the `99` with your simple/variable product id
* `role=administrator` you need replace `administrator` with your user role id / use `current` to get loggedin user role
* `price` use `regular_price` or `selling_price` to get the value of each

= Plugin Translators =
* <a href="https://profiles.wordpress.org/miladjef/">miladjef</a> {Persian translation}

= Plugin Contributors / Testers =
* <a href="https://profiles.wordpress.org/raj5harma">Raj sharma</a>
* <a href="https://wordpress.org/support/profile/nick6352683" >nick6352683 </a>
* <a href="https://profiles.wordpress.org/arnisarbidans">arnis.arbidans</a>
* <a href="https://profiles.wordpress.org/cuppy90">Cuppy</a>
* <a href="#">Joachim Keuppens</a>
* <a href="https://github.com/diventimage">Divent Image</a>
* <a href="https://github.com/gbreen">gbreen</a>
* <a href="https://github.com/vizulatedev">vizulatedev</a>
* <a href="https://profiles.wordpress.org/cloudcuckoo/">Joeyvan Dongen</a>
* <a href="https://profiles.wordpress.org/timmyhutton" >timmy hutton</a>

== Screenshots ==
1. Plugin Installed
2. Plugin Settings Menu
3. Plugin Settings Page
4. Free Add-ons
5. Paid Add-ons
6. Simple Product Editor
7. Simple Product Editor
8. Variable Product Editor
9. Aelia Currency Switcher Integration Settings
10. Aelia Currency Switcher Simple Product Editor
11. Aelia Currency Switcher Variable Product Editor

== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of WooCommerce Role Based Price, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "WooCommerce Role Based Price"  and click Search Plugins. Once you've found our plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now"

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your Web Server via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

1. Installing alternatives:
 * via Admin Dashboard:
 * Go to 'Plugins > Add New', search for "WooCommerce Role Based Price", click "install"
 * OR via direct ZIP upload:
 * Upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin
 * OR via FTP upload:
 * Upload `woocommerce-role-based-price` folder to the `/wp-content/plugins/` directory
 
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
**Get Product's Regular Price With Custom User Role**
`[wc_rbp id='99' role='administrator' price='regular_price']`

**Get Product's Selling Price Based On Logged In User**
`[wc_rbp id='99' role='current' price='selling_price']`

**Dose This Plugin Supports Aelia Currency Switcher ?**

Yes This Plugin Support Aelia Currency Switcher. by activation of Aelia Currency Switcher In Settings Menu

**How I Can Get Support For This Plugin**

* http://varunsridharan.in/plugin-support/ 
* https://wordpress.org/support/plugin/woocommerce-role-based-price
* https://github.com/technofreaky/WooCommerce-Role-Based-Price
* Email : varunsridharan23@gmail.com
* Email : plugin@varunsridharan.in

**I have an idea for your plugin!**  
That's great. We are always open to your input, and we would like to add anything we think will be useful to a lot of people. please contact us using above methods.

**I found a bug!**  
Oops. Please Use github / WordPress to post bugs.  <a href="https://github.com/technofreaky/WooCommerce-Role-Based-Price"> Open an Issue </a>


**Where can I request new features**

Please open an issue at <a href="https://github.com/technofreaky/WooCommerce-Role-Based-Price"> GitHub </a> and we will look into it


== Changelog ==

= 3.0.2 [08-02-2017] =
* Fixed : Tax Price issue in variable product. tax getting add 2 times in base price.
* Tweaks : Minor Core Updates

= 3.0.1 [26-01-2017] =
* Fixed : Parse error: syntax error, unexpected '.', expecting ',' or ';' in ../includes/helpers/class-admin-notice.php on line 6 [https://wordpress.org/support/topic/output-error-after-update/]

= 3.0 [26-01-2017] =
* Total Plugin Redeveloped 
* Tested With latest WordPress & WooCommerce
* Made It Developer Friendly
* Easy To Create Addons
* 5+ Plugins Integrated

= 2.8.8 [28-01-2016] = 
* Tweak : Recoded for the frontend price
* Fixed : Price issue with the version 2.8.7 which aelia currency switcher active. is now fixed and minor fix
= 2.8.7 [06-01-2016] =
* Fixed : Price issue with the version 2.8.6 or below. which aelia currency switcher active. is now fixed and minor fix

= 2.8.6 [04-01-2016] =
* Fixed : Price not showing after the update 

= 2.8.5 [04-01-2016] =
* Fixed : Set 0 Price for a product [https://wordpress.org/support/topic/zero-price-3?replies=12]

= 2.8.4 [01-12-2015] =
* Fixed : Products in currencies come up with wrong prices  [https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/20]
* Fixed : Aelia Currency Switcher options not available in wp-all-import template [https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/19]

= 2.8.3 [12-11-2015] =
* Fixed : Variation pricing not showing properly {https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/17}
* Minor Bug Fixes

= 2.8.2 [09-11-2015] =
* Fixed : Selling Price Not Showing As Per Role Based Price Settings {https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/16}
* Minor Bug Fix

= 2.8.1 [08-11-2015] =
* Fixed : Selling / Regular shown from base product price if any one is empty [https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/14]
* Updated : Persian Language

= 2.8 [08-11-2015] =
* Fixed : Selling / Regular shown from base product price if any one is empty [https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/14]
* Fixed [https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/12]
* Bulk Variable Price Edit In Product Edit View
  [https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/11]
* Added Persian translation [https://github.com/technofreaky/WooCommerce-Role-Based-Price/issues/15]
* Updated POT File
* Minor Bug Fix
  
= 2.7.4 [04-11-2015] =
* Fixed https://wordpress.org/support/topic/error-676?replies=2#post-7625400
* Minor Bug Fix


= 2.7.3 [04-11-2015] =
* Fixed WooCommerce Product Addons Integration Activation
* Minor Bug Fix

= 2.7.2 [04-11-2015] =
* Added User Configurable Option For Price & Product Hide
* User Configurable Option to hide variations or with add-to-cart button for variable
* Minor Bug Fix

= 2.7.1 [03-11-2015] =
* Fixed Variable Add To Cart Form Hidden.
* Minor Bug Fix

= 2.7 [21-10-2015] =
* Added Support Addon For WooCommerce Product Addons
* Minor Bug Fix

= 2.6 [10-10-2015] =
* Fixed Price output when using custom separator.
* Minor bug fix

= 2.5 [30-09-2015] =
* Code Clean Up
* Changed Addons Activation & Deactivation Stucture 
* Fixed Price Seperator Issue [https://wordpress.org/support/plugin/woocommerce-role-based-price]
* Redeveloped Aelia CurrencySwitcher Integration
* Minor House Keeping
* Updated To Latest Version WordPress
* Updated To Latest Version WooCommerce

= 2.4 [16-09-2015] =
* Fixed POPUP Loading Issue With Variable Products.
* Minor Bug Fix With Aelia Currency Switcher Integration
* Code Cleanup
* Added Option Get Product's Base Regular & Selling Price Using Shortcode

= 2.3 [14-09-2015] =
* Fixed POPUP Loading Issue. 

= 2.2 [13-09-2015] =
* Changed POPUP View In Product Edit Page
* Product Price Not Showing Correctly When Saved As Empty Field Fixed
* Added Shortcode to get product price.
* Minor Bug Fix

= 2.1 [05-09-2015] =
* Fixed Hide Price Issue When No User LogedIn
* Fixed Hide Add-to-cart issue when no user logedIn
* Made Plugin Compatible With WP Multisite 
* Minor Bug Fix

= 2.0 [03-09-2015] =
* Total Plugin ReDeveloped
* Added Integrations To WP All Import Plugin
* Added Integration To Aelia Currency Switcher Plugin
* Updated Code Standards
* Update Plugin For Latest WP & WooCommerce (WP : 4.3 | WC : 2.4.6)
* Minor Bug Fix

= 1.3 [25-06-2015] =
* Major Update [Had Some File Conflict.. please update your plugin too]

= 1.2 [23-06-2015] =
* Fixed Error Message at product price when viewing as visitor 

= 1.1 [23-06-2015] = 
* Fixed Simple & Variation Product Price At Checkout & Cart Page
* Code CleanUP
* Fixed Minor Issue
* Fixed Variation Role Based Box in Product Edit Page
* Added Plugin Translation Files [wc-rbp]

= 1.0 [22-06-2015] =
* Added Role Based Pricing For Variable / Variation Product  [<a href="https://wordpress.org/support/topic/for-variation-products-1?replies=13"> For variation products </a> ]
* Fix Major Bugs
* Update Plugin For Latest WP & WooCommerce (WP : 4.2.2 | WC : 2.3.11)
* Option To Rename User Role (Affected Only For This Plugin) [<a href="https://wordpress.org/support/topic/rename-titles?replies=2"> Rename titles </a> ]

= 0.2 [26-02-2015] =
* Fixed Selling Price Display for role based Issue At [<a href="https://wordpress.org/support/topic/excellent-plugin-but-has-a-small-bug?replies=4#post-6620252">#6620252</a>]

= 0.1 [25-02-2015] =
* Base Version