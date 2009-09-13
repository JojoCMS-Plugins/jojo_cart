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
    echo json_encode(array());
    exit;
}

/* Get the states */
$states = Jojo::selectQuery("SELECT statecode, state FROM {cart_state} WHERE countrycode = ? ORDER BY state", strtoupper($country));
if (!$states) {
    echo json_encode(array());
    exit;
}

echo json_encode(array_values($states));

