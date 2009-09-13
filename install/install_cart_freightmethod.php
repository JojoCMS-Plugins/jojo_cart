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

$table = 'cart_freightmethod';
$query = "
        CREATE TABLE {cart_freightmethod} (
            `id` INT NOT NULL auto_increment,
            `shortname` VARCHAR( 20 ) NOT NULL ,
            `longname` VARCHAR( 255 ) NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE = MYISAM ";

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

/* Ensure we have some defaults */
$data = Jojo::selectQuery("SELECT * FROM {cart_freightmethod}");
if (!count($data)) {
   $defaultmethods = array(
                            array('Standard',        'Standard Courier'),
                            array('Express',        'Express Courier'),
                           );

    foreach ($defaultmethods as $method) {
        Jojo::insertQuery("INSERT INTO {cart_freightmethod} SET shortname = ?, longname = ?", $method);
    }
}