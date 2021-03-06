<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Harvey Kane <code@ragepank.com>
 * Copyright 2008 Michael Holt <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

// Shopping Cart
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart'));
if (!$data) {
   echo "Adding <b>Shopping Cart</b> Page to menu<br />";
   $_SHOPPING_CART_ID = Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_mainnav = ?, pg_xmlsitemapnav = ?, pg_ssl = ?',
        array("Shopping Cart", "cart", "[editor:html]\n", "jojo_plugin_jojo_cart", "no", "no", "no", "no", "no", "yes"));
} else {
    $_SHOPPING_CART_ID = $data['pageid'];
}

// Shopping Cart Update Handler
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_update'));
if (!$data) {
   echo "Adding <b>Shopping Cart Update Handler</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_link = ?, pg_parent = ?, pg_order = ?, pg_index = ?, pg_followto = ?, pg_followfrom = ?, pg_contentcache = ?, pg_mainnav = ?, pg_breadcrumbnav = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?, pg_footernav = ?, pg_ssl = ?',
        array("Shopping Cart Update Handler", "cart/update", "jojo_plugin_jojo_cart_update", $_SHOPPING_CART_ID, "8", "no", "no", "no", "no", "no", "no", "no", "no", "no", "yes"));
}
// Remove 'Shopping cart update handler' from footer navigation
Jojo::updateQuery("UPDATE {page} SET pg_footernav='no' WHERE pg_link='jojo_plugin_jojo_cart_update'");

// Shipping Method
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_shipping'));
if (!$data) {
   echo "Adding <b>Shipping Method</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_mainnav = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?, pg_footernav = ?, pg_ssl = ?',
        array("Shipping Method", "cart/shipping", "[editor:html]\n", "jojo_plugin_jojo_cart_shipping", $_SHOPPING_CART_ID, "no", "no", "no", "no", "no", "no", "no", "yes"));
}
// Remove 'Shipping method' from footer navigation
Jojo::updateQuery("UPDATE {page} SET pg_footernav='no' WHERE pg_link='jojo_plugin_jojo_cart_shipping'");
// Remove 'Shipping method' from sitemap
Jojo::updateQuery("UPDATE {page} SET pg_sitemapnav='no' WHERE pg_link='jojo_plugin_jojo_cart_shipping'");

// Shipped
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_shipped'));
if (!$data) {
   echo "Adding <b>Shipped confirmation</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_mainnav = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?, pg_footernav = ?, pg_ssl = ?',
        array("Shipped confirmation", "cart/shipped", "[editor:html]\n", "jojo_plugin_jojo_cart_shipped", $_SHOPPING_CART_ID, "no", "no", "no", "no", "no", "no", "no", "yes"));
}
// Remove 'Shipping method' from footer navigation
Jojo::updateQuery("UPDATE {page} SET pg_footernav='no' WHERE pg_link='jojo_plugin_jojo_cart_shipped'");
// Remove 'Shipping method' from sitemap
Jojo::updateQuery("UPDATE {page} SET pg_sitemapnav='no' WHERE pg_link='jojo_plugin_jojo_cart_shipped'");

// Paid
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_paid'));
if (!$data) {
   echo "Adding <b>Paid confirmation</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_mainnav = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?, pg_footernav = ?, pg_ssl = ?',
        array("Paid confirmation", "cart/paid", "[editor:html]\n", "jojo_plugin_jojo_cart_paid", $_SHOPPING_CART_ID, "no", "no", "no", "no", "no", "no", "no", "yes"));
}

// Payment Method
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_payment'));
if (!$data) {
   echo "Adding <b>Payment Method</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_mainnav = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?, pg_footernav = ?, pg_ssl = ?',
        array("Payment Method", "cart/payment", "[editor:html]\n", "jojo_plugin_jojo_cart_payment", $_SHOPPING_CART_ID, "no", "no", "no", "no", "no", "no", "no", "yes"));
}
// Remove 'Payment method' from footer navigation
Jojo::updateQuery("UPDATE {page} SET pg_footernav='no' WHERE pg_link='jojo_plugin_jojo_cart_payment'");
// Remove 'Payment method' from sitemap
Jojo::updateQuery("UPDATE {page} SET pg_sitemapnav='no' WHERE pg_link='jojo_plugin_jojo_cart_payment'");


// Payment Processor
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_process'));
if (!$data) {
   echo "Adding <b>Payment Processor</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_mainnav = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?, pg_footernav = ?, pg_ssl = ?',
        array("Payment Processor", "cart/process", "[editor:html]\n", "jojo_plugin_jojo_cart_process", $_SHOPPING_CART_ID, "no", "no", "no", "no", "no", "no", "no", "yes"));
}
// Remove 'Payment processor' from footer navigation
Jojo::updateQuery("UPDATE {page} SET pg_footernav='no' WHERE pg_link='jojo_plugin_jojo_cart_process'");
// Remove 'Payment processor' from sitemap
Jojo::updateQuery("UPDATE {page} SET pg_sitemapnav='no' WHERE pg_link='jojo_plugin_jojo_cart_process'");

// User Transaction History
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_transaction_history'));
if (!$data) {
   echo "Adding <b>Transaction History</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_mainnav = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?, pg_footernav = ?, pg_ssl = ?',
        array("Your Order History", "cart/transaction_history", "[editor:html]\n", "jojo_plugin_jojo_cart_transaction_history", $_SHOPPING_CART_ID, "no", "no", "no", "no", "no", "no", "no", "yes"));
}

// Admin Shopping Cart
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = ?", array('admin/cart'));
if (!$data) {
    echo "Adding <b>Admin Shopping Cart</b> Page to menu<br />";
    $_ADMIN_SHOPPING_ID = Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Shopping Cart', pg_link = 'Jojo_Plugin_Admin_Content', pg_url = 'admin/cart', pg_order=3, pg_parent=$_ADMIN_ROOT_ID, pg_mainnav='yes', pg_secondarynav='no', pg_breadcrumbnav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no'");
} else {
    $_ADMIN_SHOPPING_ID = $data['pageid'];
}

// Discount codes
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = ?", array('admin/edit/discount'));
if (!$data) {
   echo "Adding <b>Discounts</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_order = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?',
        array("Discounts", "admin/edit/discount", "[editor:html]\n", "jojo_plugin_admin_edit", "3", $_ADMIN_SHOPPING_ID, "no", "no", "no", "no", "no"));
}

// Cart Countries
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_country_admin'));
if (!$data) {
   echo "Adding <b>Cart Countries</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_order = ?, pg_parent = ?, pg_contentcache = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?',
        array("Cart Countries", "admin/cart/country", "[editor:html]\n", "jojo_plugin_jojo_cart_country_admin", "3", $_ADMIN_SHOPPING_ID, "no", "no", "no"));
}

// Cart transaction report
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_transaction_report'));
if (!$data) {
   echo "Adding <b>Cart Transaction report</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_order = ?, pg_parent = ?, pg_contentcache = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?',
        array("Transaction report", "admin/cart/transactions", "[editor:html]\n", "jojo_plugin_jojo_cart_transaction_report", "3", $_ADMIN_SHOPPING_ID, "no", "no", "no"));
}

// Cart transactionlist report
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('jojo_plugin_jojo_cart_transaction_list'));
if (!$data) {
  echo "Adding <b>Cart Transactionlist report</b> Page to menu<br />";
  Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_order = ?, pg_parent = ?, pg_contentcache = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?, pg_mainnav = ?',
       array("Transactionlist report", "admin/cart/transaction_list", "[editor:html]\n", "jojo_plugin_jojo_cart_transaction_list", "3", $_ADMIN_SHOPPING_ID, "no", "no", "no", "no"));
}

// Cart regions
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = ?", array('admin/edit/cart_region'));
if (!$data) {
   echo "Adding <b>Cart regions</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_order = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?',
        array("Cart Regions", "admin/edit/cart_region", "[editor:html]\n", "jojo_plugin_admin_edit", "4", $_ADMIN_SHOPPING_ID, "no", "no", "no", "no", "no"));
}

// Cart freight methods
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = ?", array('admin/edit/cart_freightmethod'));
if (!$data) {
   echo "Adding <b>Cart freight methods</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_order = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?',
        array("Freight methods", "admin/edit/cart_freightmethod", "[editor:html]\n", "jojo_plugin_admin_edit", "4", $_ADMIN_SHOPPING_ID, "no", "no", "no", "no", "no"));
}

// Shared freight models
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = ?", array('admin/edit/cart_freight_model'));
if (!$data) {
   echo "Adding <b>Cart freight models</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_body_code = ?, pg_link = ?, pg_order = ?, pg_parent = ?, pg_index = ?, pg_followto = ?, pg_contentcache = ?, pg_sitemapnav = ?, pg_xmlsitemapnav = ?',
        array("Shared freight models", "admin/edit/cart_freight_model", "[editor:html]\n", "jojo_plugin_admin_edit", "5", $_ADMIN_SHOPPING_ID, "no", "no", "no", "no", "no"));
}

/* new fields for storing logged-in user data */
if (Jojo::tableExists('user')) {
    $new_fields = array('us_company', 'us_phone', 'us_address1', 'us_address2', 'us_address3', 'us_suburb', 'us_city', 'us_state', 'us_postcode', 'us_country');
    
    foreach ($new_fields as $new_field) {
        if (!Jojo::fieldexists('user', $new_field)) {
            echo "Add <b>".$new_field."</b> to <b>user</b><br />";
            Jojo::structureQuery("ALTER TABLE {user} ADD `".$new_field."` VARCHAR( 255 ) NOT NULL;");
        }
    }
}