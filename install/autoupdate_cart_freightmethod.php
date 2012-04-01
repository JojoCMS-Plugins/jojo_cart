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

$default_td['cart_freightmethod'] = array(
        'td_name' => "cart_freightmethod",
        'td_displayname' => "Freight Methods",
        'td_primarykey' => "id",
        'td_displayfield' => "shortname",
        'td_rolloverfield' => "longname",
        'td_orderbyfields' => "displayorder, shortname",
        'td_menutype' => "list",
        'td_deleteoption' => "yes",
        'td_defaultpermissions' => "everyone.show=0\neveryone.view=0\neveryone.edit=0\neveryone.add=0\neveryone.delete=0\nadmin.show=0\nadmin.view=0\nadmin.edit=0\nadmin.add=0\nadmin.delete=0\nnotloggedin.show=0\nnotloggedin.view=0\nnotloggedin.edit=0\nnotloggedin.add=0\nnotloggedin.delete=0\nregistered.show=0\nregistered.view=0\nregistered.edit=0\nregistered.add=0\nregistered.delete=0\nsysinstall.show=0\nsysinstall.view=0\nsysinstall.edit=0\nsysinstall.add=0\nsysinstall.delete=0\n",
    );

// ID Field
$default_fd['cart_freightmethod']['id'] = array(
        'fd_name' => "ID",
        'fd_type' => "readonly",
        'fd_order' => "1",
    );

// Short Name Field
$default_fd['cart_freightmethod']['shortname'] = array(
        'fd_name' => "Short Name",
        'fd_type' => "text",
        'fd_order' => "2",
    );

// Long Name Field
$default_fd['cart_freightmethod']['longname'] = array(
        'fd_name' => "Long Name",
        'fd_type' => "text",
        'fd_order' => "3",
    );

// Display order Field
$default_fd['cart_freightmethod']['displayorder'] = array(
        'fd_name' => "Display order",
        'fd_type' => "order",
        'fd_type' => "order",
        'fd_order' => "4",
    );


