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

/* Define the class for the cart */
if (!defined('Jojo_Cart_Class')) {
    define('Jojo_Cart_Class', Jojo::getOption('jojo_cart_class', 'jojo_plugin_jojo_cart'));
}

/* Make sure this is the current cart class */
if (Jojo_Cart_Class != 'jojo_plugin_jojo_cart') {
    Jojo::setOption('jojo_cart_class', 'jojo_plugin_jojo_cart');
}

/* Register a class autoloaders */
spl_autoload_register(array('jojo_plugin_jojo_cart', 'autoload'));

Jojo::addHook('foot', 'foot', 'jojo_cart');

$_provides['pluginClasses'] = array(
        Jojo_Cart_Class => 'jojo Cart - shopping cart pages'
        );

$_provides['pluginClasses'] = array(
  Jojo_Cart_Class => 'jojo Cart - shopping cart pages',
  'Jojo_plugin_jojo_cart_transaction_list' => 'jojo cart - transaction list'
);

/* add a new field type for admin section */
$_provides['fieldTypes'] = array(
        'freight' => 'Jojo Cart - Freight',
        'minfreight' => 'Jojo Cart - Country Min Freight'
        );

/* Register URI patterns */
Jojo::registerURI("cart/[action:process]/[token:[a-zA-Z0-9]{20}]",                              'jojo_plugin_Jojo_cart_process'); // "cart/process/VUvx2v7beGA5QWUlydU1/"
Jojo::registerURI("cart/[action:complete|payment-info]/[token:[a-zA-Z0-9]{20}]",                Jojo_Cart_Class);                 // "cart/complete/da9fdd0cd8175bd247bd04bdebe90fe6133572b2/"
Jojo::registerURI("cart/[action:complete|cancel|cheque|payment-info]",                          Jojo_Cart_Class);                 // "cart/action/"
Jojo::registerURI("cart/[action:empty]",                                                        'jojo_plugin_Jojo_cart_update');  // "cart/action/"
Jojo::registerURI("cart/[action:add|remove]/[id:[a-zA-Z0-9_\-\+]*]",                               'jojo_plugin_Jojo_cart_update');  // "cart/add/product-name/" or "cart/add/id/" or "cart/remove/product-name/" or "cart/remove/id/"
Jojo::registerURI("cart/[action:add|remove]",                                                   'jojo_plugin_Jojo_cart_update');  // "cart/add/" - with the ID in a POST variable

Jojo::registerURI("cart/[action:shipped|shippedadmin|shippedadmin_unshipped]/[token:[a-zA-Z0-9]{20}]/[actioncode:[a-zA-Z0-9]{10}]", 'jojo_plugin_Jojo_cart_shipped'); // "cart/shipped/VUvx2v7beGA5QWUlydU1/2v7beGydU1/"
Jojo::registerURI("cart/[action:paid|paidadmin_complete|paidadmin_paymentpending|paidadmin_abandoned]/[token:[a-zA-Z0-9]{20}]/[actioncode:[a-zA-Z0-9]{10}]",    'jojo_plugin_Jojo_cart_paid');    // "cart/paid/VUvx2v7beGA5QWUlydU1/2v7beGydU1/"
Jojo::registerURI(_ADMIN."/cart/transactions/[token:[a-zA-Z0-9]{20}]",                          'jojo_plugin_Jojo_cart_process'); // "admin/cart/transactions/VUvx2v7beGA5QWUlydU1/"
Jojo::registerURI(_ADMIN."/cart/transactions/[token:[a-zA-Z0-9]{32}]",                          'jojo_plugin_Jojo_cart_process'); // "admin/cart/transactions/VUvx2v7beGA5QWUlydU1eGA5QWUlydU1/" - the longer MD5 token Jojo used to use
Jojo::registerURI(_ADMIN."/cart/transaction_list/[token:[a-zA-Z0-9]{20}]",                      'jojo_plugin_Jojo_cart_transaction_list'); // "admin/cart/transactionlist/VUvx2v7beGA5QWUlydU1/"

$_options[] = array(
    'id'          => 'jojo_cart_class',
    'category'    => 'Cart',
    'label'       => 'Shopping cart class name',
    'description' => 'The name of the class to use',
    'type'        => 'hidden',
    'default'     => 'jojo_plugin_jojo_cart',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_merchant_name',
    'category'    => 'Cart',
    'label'       => 'Merchant name',
    'description' => 'The official name of the company/organization accepting payments via the cart',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_merchant_address',
    'category'    => 'Cart',
    'label'       => 'Merchant address',
    'description' => 'The official address of the company/organization accepting payments via the cart',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_test_mode',
    'category'    => 'Cart',
    'label'       => 'Test mode enabled',
    'description' => 'Use test mode for processing non-real transactions before going live',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_success_message',
    'category'    => 'Cart',
    'label'       => 'Success message',
    'description' => 'A message to be displayed to the visitor after a successful order has been made',
    'type'        => 'textarea',
    'default'     => 'Thank you for your order.',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_tracking_code',
    'category'    => 'Cart',
    'label'       => 'Conversion tracking code',
    'description' => 'Optional javascript code for tracking conversions (eg Google Adwords or similar). This code is outputted after a successful order has been made.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_default_currency',
    'category'    => 'Cart',
    'label'       => 'Default currency',
    'description' => 'The default currency for the shopping cart (if the currency is not specified on a per-product basis). Eg USD, EUR, GBP, NZD, AUD etc.',
    'type'        => 'text',
    'default'     => 'USD',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_order_email',
    'category'    => 'Cart',
    'label'       => 'Order email address',
    'description' => 'Order confirmation emails will be copied to this email address, in addition to the _CONTACTADDRESS defined for the site',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_shipped_email',
    'category'    => 'Cart',
    'label'       => 'Admin Shipped email address',
    'description' => 'Order shipped emails will be copied to this email address rather than the normal contact address',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);


$_options[] = array(
    'id'          => 'cart_order_name',
    'category'    => 'Cart',
    'label'       => 'Order name',
    'description' => 'The name of the person to whom order confirmation emails will be copied to',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_force_default_currency',
    'category'    => 'Cart',
    'label'       => 'Force default currency',
    'description' => 'Only allows transactions in the default currency (many payment providers only allow a single currency).',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_freight_in_multiple_currencies',
    'category'    => 'Cart',
    'label'       => 'Allow freight in multiple currencies',
    'description' => 'Allow freight in multiple currencies, or if multiple currencies, dont have freight for other currencies.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_confirm_shipped',
    'category'    => 'Cart',
    'label'       => 'Confirm orders have shipped',
    'description' => 'Order admin emails will contain a confirmation link which marks the order as being shipped',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_cart'
);
$_options[] = array(
    'id'          => 'cart_show_gst',
    'category'    => 'Cart',
    'label'       => 'Show GST value on orders',
    'description' => 'Shows the GST value on NZD transactions (and not on foreign currency), or dont mention GST at all.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_cart'
);
$_options[] = array(
    'id'          => 'cart_webmaster_copy',
    'category'    => 'Cart',
    'label'       => 'Send a copy of order emails to webmaster',
    'description' => 'If this option is set, the webmaster will receive a copy of all orders.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'cart_shipped_tracking_message',
    'category'    => 'Cart',
    'label'       => 'Tracking information',
    'description' => 'A default block of text used to record tracking information for orders',
    'type'        => 'textarea',
    'default'     => "Tracking Information:\nTicket number: xxxxxxxxx\nPhone: 555-5555\nWebsite: www.example.com",
    'options'     => '',
    'plugin'      => 'jojo_cart'
);

$_options[] = array(
    'id'          => 'cart_transactions_report_number',
    'category'    => 'Cart',
    'label'       => 'The number of transactions shown on the transaction report',
    'description' => '',
    'type'        => 'text',
    'default'     => '150',
    'options'     => '',
    'plugin'      => 'jojo_cart'
);
