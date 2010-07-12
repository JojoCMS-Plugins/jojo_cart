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

$default_td['cart_region'] = array(
        'td_name' => "cart_region",
        'td_primarykey' => "regioncode",
        'td_orderbyfields' => "displayorder",
        'td_menutype' => "list",
    );

// Region Code Field
$default_fd['cart_region']['regioncode'] = array(
        'fd_name' => "Region Code",
        'fd_type' => "readonly",
        'fd_order' => "1",
    );

// Name Field
$default_fd['cart_region']['name'] = array(
        'fd_name' => "Name",
        'fd_type' => "text",
        'fd_size' => "50",
        'fd_order' => "2",
    );

// Display Order Field
$default_fd['cart_region']['displayorder'] = array(
        'fd_name' => "Display Order",
        'fd_type' => "order",
        'fd_order' => "3",
    );

// Minfreight Field
$default_fd['cart_region']['minfreight'] = array(
        'fd_name' => "Minfreight",
        'fd_type' => "minfreight",
        'fd_showlabel' => "no",
        'fd_order' => "4",
    );


