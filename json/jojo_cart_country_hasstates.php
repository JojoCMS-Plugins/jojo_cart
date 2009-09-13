<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 JojoCMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Mike Cochrane <mikec@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$country = Jojo::getFormData('c', false);
if (!$country) {
    echo json_encode(true);
    exit;
}

/* Get the current special status */
$country = Jojo::selectRow("SELECT hasstates FROM {cart_country} WHERE countrycode = ?", strtoupper($country));
echo json_encode(!isset($country['hasstates']) || $country['hasstates'] != 'no');

