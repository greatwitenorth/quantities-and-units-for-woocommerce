=== Quantities and Units for WooCommerce ===
Contributors: greatwitenorth
Tags: woocommerce, product quantities, product minimum values, product maximum values, product step values, incremental product quantities, min, max, decimal
Requires at least: 3.5
Tested up to: 4.3
Stable tag: 1.0.8
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily require your customers to buy a minimum / maximum / incremental amount of products to continue with their checkout.

== Description ==
NOTE: This plugin has been forked from the "Advanced Product Quantities" plugin. It adds decimal quantities support and units to products.

With Quantities and Units for WooCommerce you can easily create rules that restrict the amount of products a user can buy at once. Set Minimum, Maximum and Step values for any product type and must be valid before a customer can proceed to checkout.

This plugin works great with [Rapid Order](http://rapidorderplugin.com/), a fast ordering system for WooCommerce. 

New Features

* Allow decimal quantities (configure this in Quantity Rule -> Advanced Rule -> Site Wide Step Value)
* Specify a unit of measurement for the quantity (ie lbs, kg, bag etc)

Features:

* Added Custom Quantity Message Option
* Added Out of Stock Min/Max
* Added Role Support, create rules based on user roles.
* Improved performance / caching
* Improved admin interface
* Allows rules to have a minimum of 0
* Get started in minutes
* Set a Minimum product quantity requirement
* Set a Maximum product quantity requirement
* Sell products by a desired increment ie. by two's or the dozen
* Create product category based rules
* WooCommerce Validation rules tells users what rules they've broken directly on the cart or product page
* Set rule priority to layer multiple rules on top of each other
* Add your rule based input boxes to products thumbnails using [WooCommerce Thumbnail Input Quantities](http://wordpress.org/plugins/woocommerce-thumbnail-input-quantities/installation/)
* Easily override rules on a per-product basis
* Integrates with [WooCommerce's Product CSV Import Suite](http://www.woothemes.com/products/product-csv-import-suite/?utm_source=docs&utm_medium=docssite&utm_campaign=docs)
* See which rule is being applied to an individual product on your edit product page
* Now fully supports ALL PRODUCT TYPES, simple, variable, grouped and affiliate
* Create Site Wide rules that apply to every product unless overwritten on a per-product basis
* Create rules by Product Tags (opposed to just categories)
* Woocommerce +2.0 compatible 

== Installation ==

Automatic WordPress Installation

1. Log-in to your WordPress Site
2. Under the plugin sidebar tab, click ‘Add New’
3. Search for 'Quantities and Units for WooCommerce'
4. Install and Activate the Plugin
5. Set Rules for categories by clicking the new ‘Quantity Rules’ sidebar option or assign per-product rules by using the new metabox on your product page.

NOTE: to enable decimal quantities you need to define the 'Step' value on a per product basis, or globally for the site under Quantity Rule -> Advanced Rule -> Site Wide Step Value.

Manual Installation

1. Download the latest version of the plugin from Quantities and Units for WooCommerce WordPress page.
3. Uncompress the file
4. Upload the uncompressed directory to ‘/wp-content/plugins/’ via FTP
5. Active the plugin from your WordPress backend ‘Plugins -> Installed Plugins’
6. Set Rules for categories by clicking the new ‘Quantity Rules’ sidebar option or assign per-product rules by using the new metabox on your product page.

== Changelog ==
= 1.0.8 =
* allow decimals on Advanced Rules page

= 1.0.7 =
* fixed min step value based min value.

= 1.0.6 =
* Min and step rules now work with ajax add to cart functionality in WooCommerce 2.5+
* Added min width css rule on quantity input box

= 1.0.5 =
* Fixed issue when trying to save advanced rules.

= 1.0.4 =
* Fixed floating point precision errors

= 1.0.3 =
* Fixed Call to undefined method WooCommerce::add_error() bug

= 1.0.2 =
* Removed admin notice

= 1.0.1 =
* Bug fixes

= 1.0.0 =
* Forked from "Advanced Product Quantities"
* Allow decimal quantities
* Specify a unit of measurement for the quantity (ie lbs, kg, bag etc)

= APQ 2.1.6 = 
* Hides message when no quantity rule is being applied.

= APQ 2.1.5 = 
* Minor bug fix, couldn't unset max out of stock value

= APQ 2.1.4 = 
* Upgrade fix, removed error for unset value

= APQ 2.1.3 = 
* Added Quantity Message Options
* Added Out of Stock min/max values 
* Fixed 0 quantity appearing as 1 bug
* Minor class tweaks

= APQ 2.1.2 = 
* Default user role bug fix.

= APQ 2.1.1 = 
* Product Page UI Update
* Minor bug fixes.

= APQ 2.1.0 = 
* Added Role Support, create rules based on user roles.
* Improved performance / cacheing
* Improved admin interface
* Allows rules to have a minimum of 0

= APQ 2.0.0 = 
* Updated name from WooCommerce Incremental Product Quantities to WooCommerce Advanced Product Quantities
* Now fully supports ALL PRODUCT TYPES, simple, variable, grouped and affiliate
* Create Site Wide rules that apply to every product unless overwritten on a per-product basis
* Create rules by Product Tags (opposed to just categories)
* Code reconfiguration puts everything into classes, the way it should be.

= APQ 1.1.4 =
* Added back WC 2.0.x validation compatibility. 

= APQ 1.1.3 =
* Minor bug fixes.

= APQ 1.1.2 =
* Undefined variable bug fix.

= APQ 1.1.1 =
* Fixed bug that was unsetting rule checkboxes.

= APQ 1.1.0 =
* Updated plugin to work with WC 2.1.2 and below.
* New error response methods.
* Update validations.
* Updated comments.
* Added extra help text.

= APQ 1.0.8 =
* Fixed division by zero error in validations.

= APQ 1.0.7 =
* Contributor consolidation.

= APQ 1.0.6 =
* Fixed cart bug, added additional validation so users can't enter minimum values that are less then the step value.

= APQ 1.0.5 =
* Fixed additional bug related to missing input values and error messages on some installs. Also updated notice window. 

= APQ 1.0.4 =
* Style sheet and link update. 
* Added potential solution for niche validation problem.  

= APQ 1.0.3 =
* Readme.txt updates.

= APQ 1.0.2 =
* Another small url change.

= APQ 1.0.1 =
* Minor variable updates to account for changing directory.

= APQ 1.0.0 =
* Initial Commit

== Screenshots ==

1. Single product page, page loads with it's minimum quantity and notifies the user below.
1. Create rule page. 
1. Single product 'Product Quantity Rules' meta box. Deactivate or override rules. Even set out of stock min/max values.
1. Single product 'Product Quantity Rules' meta box. Display of values by user role.
1. 'Advanced Rules' page, set sitewide rules and configure quantity notifications (screenshot 1)
1. Required configuration for Out of Stock quantities to be displayed.
