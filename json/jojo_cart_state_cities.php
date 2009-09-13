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
$state = Jojo::getFormData('s', false);
if (!$country || !$state) {
    echo json_encode(array());
    exit;
}

/* Get the current special status */
$cities = Jojo::selectAssoc("SELECT DISTINCT city, city as city_name FROM {cart_city} WHERE countrycode = ? AND statecode = ? ORDER BY city", array(strtoupper($country), $state));
if (!$cities) {
    echo json_encode(array());
    exit;
}

echo json_encode(array_values($cities));

