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
    );

// Exclusions Field
$default_fd['discount']['exclusions'] = array(
        'fd_name' => "Exclusions",
        'fd_type' => "textarea",
        'fd_rows' => "8",
        'fd_cols' => "50",
        'fd_order' => "10",
    );