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

$default_td['cart_freight_model'] = array(
        'td_name' => "cart_freight_model",
        'td_displayname' => "Shared Freight Models",
        'td_primarykey' => "id",
        'td_displayfield' => "name",
        'td_orderbyfields' => "name",
        'td_menutype' => "list",
        'td_defaultpermissions' => "everyone.show=1\neveryone.view=1\neveryone.edit=1\neveryone.add=1\neveryone.delete=1\nadmin.show=1\nadmin.view=1\nadmin.edit=1\nadmin.add=1\nadmin.delete=1\nnotloggedin.show=1\nnotloggedin.view=1\nnotloggedin.edit=1\nnotloggedin.add=1\nnotloggedin.delete=1\nregistered.show=1\nregistered.view=1\nregistered.edit=1\nregistered.add=1\nregistered.delete=1\nsysinstall.show=1\nsysinstall.view=1\nsysinstall.edit=1\nsysinstall.add=1\nsysinstall.delete=1\n",
    );

// ID Field
$default_fd['cart_freight_model']['id'] = array(
        'fd_name' => "ID",
        'fd_type' => "hidden",
        'fd_order' => "1",
    );

// Name Field
$default_fd['cart_freight_model']['name'] = array(
        'fd_name' => "Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_order' => "2",
    );

// Freightmodel Field
$default_fd['cart_freight_model']['freightmodel'] = array(
        'fd_name' => "Freightmodel",
        'fd_type' => "freight",
        'fd_options' => "advanced",
        'fd_showlabel' => "no",
        'fd_order' => "3",
    );


