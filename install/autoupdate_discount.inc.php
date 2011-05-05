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
        'td_displayname' => "Discount codes",
        'td_primarykey' => "discountcode",
        'td_displayfield' => "discountcode",
        'td_rolloverfield' => "name",
        'td_orderbyfields' => "discountcode",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
    );

// Discount code Field
$default_fd['discount']['discountcode'] = array(
        'fd_name' => "Discount code",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_order' => "1",
    );

// Name Field
$default_fd['discount']['name'] = array(
        'fd_name' => "Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "50",
        'fd_order' => "2",
    );

// Description Field
$default_fd['discount']['description'] = array(
        'fd_name' => "Description",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "50",
        'fd_order' => "3",
    );

// Start date Field
$default_fd['discount']['startdate'] = array(
        'fd_name' => "Start date",
        'fd_type' => "unixdate",
        'fd_order' => "4",
    );

// Finish date Field
$default_fd['discount']['finishdate'] = array(
        'fd_name' => "Finish date",
        'fd_type' => "unixdate",
        'fd_order' => "5",
    );

// Discount percentage Field
$default_fd['discount']['discountpercent'] = array(
        'fd_name' => "Discount percentage",
        'fd_type' => "decimal",
        'fd_order' => "6",
        'fd_units' => "%",
    );

// Discount fixed Field
$default_fd['discount']['discountfixed'] = array(
        'fd_name' => "Discount fixed",
        'fd_type' => "decimal",
        'fd_order' => "7",
    );

// Minimum order Field
$default_fd['discount']['minorder'] = array(
        'fd_name' => "Minimum order",
        'fd_type' => "decimal",
        'fd_order' => "8",
    );

// Products Field
$default_fd['discount']['products'] = array(
        'fd_name' => "Products",
        'fd_type' => "textarea",
        'fd_rows' => "8",
        'fd_cols' => "50",
        'fd_order' => "9",
        'fd_help'  => "If you wish to restrict this discount code to only specific products, enter the product IDs here, comma separated.",
    );

// Exclusions Field
$default_fd['discount']['exclusions'] = array(
        'fd_name' => "Exclusions",
        'fd_type' => "textarea",
        'fd_rows' => "8",
        'fd_cols' => "50",
        'fd_order' => "10",
        'fd_help'  => "Enter the product IDs of any products you wish to exclude from this discount code, comma separated.",
    );
    
// Custom Field
$default_fd['discount']['custom'] = array(
        'fd_name'  => "Custom Discount",
        'fd_type'  => "textarea",
        'fd_rows'  => "8",
        'fd_cols'  => "50",
        'fd_order' => "10",
        'fd_help'  => "Use the custom field when you need one discount code to apply different discount amounts to different products. One entry per line. eg to apply a $10 discount to product ID 123, enter the following: '123=10'.",
    );

// Single Use Field
$default_fd['discount']['singleuse'] = array(
        'fd_name'    => "Single Use Code",
        'fd_type'    => "radio",
        'fd_order'   => "11",
        'fd_default' => "no",
        'fd_options' => "yes\nno",
        'fd_help'    => "Regular discount codes can be used multiple times. Single use codes expire after they have been used.",
    );

// Used By Field
$default_fd['discount']['usedby'] = array(
        'fd_name'    => "Used by",
        'fd_type'    => "readonly",
        'fd_order'   => "12",
        'fd_default' => "",
        'fd_options' => "",
        'fd_help'    => "The Transaction token / ID if this code has been used.",
    );