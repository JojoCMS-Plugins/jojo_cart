<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$default_td['discount'] = array(
        'td_name' => "discount",
        'td_displayname' => "Discounts",
        'td_primarykey' => "discountid",
        'td_displayfield' => "if(CHAR_LENGTH(discountcode) > 0, CONCAT(name,' (',discountcode,')'), name)",
        'td_rolloverfield' => "",
        'td_orderbyfields' => "discountcode",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
    );

$o=0;


// Userid Field
$default_fd['discount']['discountid'] = array(
        'fd_name' => "ID",
        'fd_type' => "hidden",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => $o++
    );

// Name
$default_fd['discount']['name'] = array(
        'fd_name' => "Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "50",
        'fd_order' => $o++
    );

// Type
$default_fd['discount']['type'] = array(
        'fd_name'    => "Discount method",
        'fd_type'    => "radio",
        'fd_default' => "no",
        'fd_options' => "code\nautomatic",
        'fd_help'    => "Apply the discount only when code is entered, or automatically",
        'fd_order' => $o++
    );

// Status
$default_fd['discount']['status'] = array(
        'fd_name' => "Active",
        'fd_type' => "yesno",
        'fd_order' => $o++
    );

// Code
$default_fd['discount']['discountcode'] = array(
        'fd_name' => "Discount code",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_order' => $o++
    );

// Description Field
$default_fd['discount']['description'] = array(
        'fd_name' => "Description",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "50",
        'fd_order' => $o++
    );

// Start date Field
$default_fd['discount']['startdate'] = array(
        'fd_name' => "Start date",
        'fd_type' => "unixdate",
        'fd_order' => $o++
    );

// Finish date Field
$default_fd['discount']['finishdate'] = array(
        'fd_name' => "Finish date",
        'fd_type' => "unixdate",
        'fd_order' => $o++
    );

// Discount percentage Field
$default_fd['discount']['discountpercent'] = array(
        'fd_name' => "Per Item Discount %",
        'fd_type' => "decimal",
        'fd_units' => "%",
        'fd_help'  => "Percentage discount per item",
        'fd_order' => $o++
    );

// Discount fixed Field
$default_fd['discount']['discountfixed'] = array(
        'fd_name' => "Per Item Discount $",
        'fd_type' => "decimal",
        'fd_help'  => "Fixed $ discount per item",
        'fd_order' => $o++
    );

// Minimum order Field
$default_fd['discount']['minorder'] = array(
        'fd_name' => "Minimum Item Quantity",
        'fd_type' => "integer",
        'fd_help'  => "Minimum quantity of an item that must be ordered before the item discount applies",
        'fd_order' => $o++
    );

// Discount percentage per order
$default_fd['discount']['percentorder'] = array(
        'fd_name' => "Order Discount %",
        'fd_type' => "decimal",
        'fd_help'  => "Fixed % discount per order (off subtotal before freight)",
        'fd_order' => $o++
    );
    
// Discount fixed per order
$default_fd['discount']['fixedorder'] = array(
        'fd_name' => "Order Discount $",
        'fd_type' => "decimal",
        'fd_help'  => "Fixed $ discount per order (off subtotal before freight)",
        'fd_order' => $o++
    );

// Freeshipping
$default_fd['discount']['freeshipping'] = array(
        'fd_name'    => "Free shipping",
        'fd_type'    => "radio",
        'fd_default' => "no",
        'fd_options' => "yes\nno",
        'fd_help'    => "If selected, the discount code will remove all freight charges.",
        'fd_order' => $o++
    );

// Minimum subtotal $ amount
$default_fd['discount']['minamount'] = array(
        'fd_name' => "Minimum Order Total",
        'fd_type' => "decimal",
        'fd_help'  => "Minimum order $ amount for this discount to apply (based on subtotal before freight)",
        'fd_order' => $o++
    );

// Products Field
$default_fd['discount']['products'] = array(
        'fd_name' => "Products",
        'fd_type' => "textarea",
        'fd_rows' => "8",
        'fd_cols' => "50",
        'fd_help'  => "If you wish to restrict this discount code to only specific products, enter the product IDs here, comma separated.",
        'fd_order' => $o++
    );

// Exclusions Field
$default_fd['discount']['exclusions'] = array(
        'fd_name' => "Exclusions",
        'fd_type' => "textarea",
        'fd_rows' => "8",
        'fd_cols' => "50",
        'fd_help'  => "Enter the product IDs of any products you wish to exclude from this discount code, comma separated.",
        'fd_order' => $o++
    );

// Custom Field
$default_fd['discount']['custom'] = array(
        'fd_name'  => "Custom Discount",
        'fd_type'  => "textarea",
        'fd_rows'  => "8",
        'fd_cols'  => "50",
        'fd_help'  => "Use the custom field when you need one discount code to apply different discount amounts to different products. One entry per line. eg to apply a $10 discount to product ID 123, enter the following: '123=10'.",
        'fd_order' => $o++
    );

// Single Use Field
$default_fd['discount']['singleuse'] = array(
        'fd_name'    => "Single Use Code",
        'fd_type'    => "radio",
        'fd_default' => "no",
        'fd_options' => "yes\nno",
        'fd_help'    => "Regular discount codes can be used multiple times. Single use codes expire after they have been used.",
        'fd_order' => $o++
    );

// Used By Field
$default_fd['discount']['usedby'] = array(
        'fd_name'    => "Used by",
        'fd_type'    => "readonly",
        'fd_default' => "",
        'fd_options' => "",
        'fd_help'    => "The Transaction token / ID if this code has been used.",
        'fd_order' => $o++
    );