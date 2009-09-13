<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Harvey Kane <code@ragepank.com>
 * Copyright 2008 JojoCMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Mike Cochrane <mikec@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

/* Ensure users of this function have access to the admin page */
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin'));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
    echo "Permission Denied";
    exit;
}

$country = Jojo::getFormData('c', false);
if (!$country) {
    exit;
}

/* Get the current special status */
$current = Jojo::selectRow("SELECT special FROM {cart_country} WHERE countrycode = ?", strtoupper($country));
if (!$current) {
    exit;
}

/* Toggle the special status */
$special = ($current['special'] == 'yes') ? 'no' : 'yes';
$res = Jojo::updateQuery("UPDATE {cart_country} SET special = ? WHERE countrycode = ?", array($special, strtoupper($country)));
echo ($res) ? 'Country updated' : 'Update failed';

