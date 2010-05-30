<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Harvey Kane <code@ragepank.com>
 * Copyright 2008 Michael Holt <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$table = 'discount';
$query = "
     CREATE TABLE {discount} (
      `discountcode` varchar(30) NOT NULL,
      `name` varchar(255) NOT NULL,
      `description` varchar(255) NOT NULL,
      `startdate` int(11) NOT NULL,
      `finishdate` int(11) NOT NULL,
      `discountpercent` decimal(10,0) NOT NULL,
      `discountfixed` decimal(10,0) NOT NULL,
      `minorder` int(11) NOT NULL,
      `products` TEXT NOT NULL,
      `exclusions` TEXT NOT NULL,
      `custom` text NOT NULL,
      PRIMARY KEY  (`discountcode`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_cart: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_cart: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);