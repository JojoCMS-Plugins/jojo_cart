<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2012 Harvey Kane <code@ragepank.com>
 * Copyright 2012 JojoCMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Mike Cochrane <mikec@mikenz.geek.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$table = 'cart_log';
$query = "
     CREATE TABLE {cart_log} (
      `id` INT NOT NULL auto_increment,
      `token` VARCHAR( 40 ) NOT NULL,
      `updated` INT NOT NULL ,
      `data` text NOT NULL ,
      PRIMARY KEY ( `id` )
      ) ENGINE = InnoDB ";


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
