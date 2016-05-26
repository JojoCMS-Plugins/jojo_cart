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
 * @author  Mike Cochrane <mikec@mikenz.geek.nz>
 * @author  Tom Dale <tom@zero.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$table = 'cart';
$query = "
     CREATE TABLE {cart} (
      `id` int(11) NOT NULL,
      `token` varchar( 40 ) NOT NULL,
      `handler` varchar( 50 ) NULL,
      `amount` decimal(10,2) NOT NULL default '0.00',
      `data` text NOT NULL ,
      `data_blob` blob NOT NULL ,
      `status` enum('pending','abandoned','complete','payment_pending','failed','processing','unverified') NOT NULL default 'pending',
      `shipped` int NOT NULL default '-1',
      `ip` varchar( 255 ) NOT NULL,
      `userid` int(11),
      `updated` int NOT NULL,
      `actioncode` varchar( 40 ),
      `testmode` ENUM('yes','no','') NOT NULL default '',
      `locked` tinyint(1) NOT NULL default '0',
      PRIMARY KEY ( `token` )
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

// ****************************
$table = 'cart_ordernumbers';

$query = "
   CREATE TABLE {cart_ordernumbers} (
      `id` INT(11) NOT NULL auto_increment,
      `value` CHAR( 1 ) NOT NULL,
       PRIMARY KEY ( `id` )
       ) ENGINE = InnoDB ";
/* Output result */

$result = Jojo::checkTable($table, $query);

if (isset($result['created'])) {
    echo sprintf("jojo_cart: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_cart: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}
if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);


/* fix the amount database field for all existing transactions */
$rows = Jojo::selectQuery("SELECT * FROM {cart} WHERE amount=0");
foreach ($rows as $row) {
    $cart = unserialize($row['data']);
    /* old format carts are arrays, new format are objects */
    if (is_array($cart)) {
        Jojo::updateQuery("UPDATE {cart} SET amount=? WHERE token=? LIMIT 1", array($cart['order']['amount'], $cart['token']));
    } elseif (is_object($cart) ) {
        Jojo::updateQuery("UPDATE {cart} SET amount=? WHERE token=? LIMIT 1", array($cart->order['amount'], $cart->token));
    }
}

// ****************************
$table = 'cart_points';

$query = "
   CREATE TABLE {cart_points} (
      `userid` int(11) NOT NULL,
      `points` int(5) NOT NULL,
       PRIMARY KEY ( `userid` )
       ) ENGINE = InnoDB ";
/* Output result */

$result = Jojo::checkTable($table, $query);

if (isset($result['created'])) {
    echo sprintf("jojo_cart: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_cart: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}
if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);


