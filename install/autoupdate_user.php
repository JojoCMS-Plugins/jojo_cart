<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2012 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

/* User Tab */

$new_fields = array('us_company' => 'Company', 'us_phone' => 'Phone', 'us_address1' => 'Address 1', 'us_address2' => 'Address 2', 'us_address3' => 'Address 3', 'us_suburb' => 'Suburb', 'us_city' => 'City', 'us_state' => 'State', 'us_postcode' => 'Postcode', 'us_country' => 'Country');
    $o = 5;
    foreach ($new_fields as $new_field => $display_name) {
        $default_fd['user'][$new_field] = array(
        'fd_name' => $display_name,
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "30",
        'fd_order' => $o++,
        'fd_tabname' => "Contact",
        'fd_flags' => "REGISTER,PROFILE,PRIVACY",
    );

    }

