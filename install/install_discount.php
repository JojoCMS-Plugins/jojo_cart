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

if (Jojo::tableExists('discount') && !Jojo::fieldExists('discount', 'discountid')) {
  Jojo::structureQuery("ALTER TABLE {discount} DROP PRIMARY KEY");
  Jojo::structureQuery("ALTER TABLE {discount} ADD `discountid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
}

$table = 'discount';
$query = "
     CREATE TABLE {discount} (
      `discountid` int(11) NOT NULL auto_increment,
      `discountcode` varchar(30) NOT NULL,
      `name` varchar(255) NOT NULL,
      `status` tinyint(1) NOT NULL default '1',
      `type` enum('code','automatic') NOT NULL default 'code',
      `description` varchar(255) NOT NULL,
      `startdate` int(11) NOT NULL,
      `finishdate` int(11) NOT NULL,
      `discountpercent` decimal(10,2) NOT NULL,
      `discountfixed` decimal(10,2) NOT NULL,
      `minorder` int(11) NOT NULL,
      `fixedorder` decimal(10,2) NOT NULL,
      `percentorder` decimal(10,2) NOT NULL,
      `freeshipping` enum('yes','no') NOT NULL default 'no',
      `minamount` decimal(10,0) NOT NULL,
      `products` text NOT NULL,
      `exclusions` text NOT NULL,
      `custom` text NOT NULL,
      `singleuse` enum('yes','no') NOT NULL default 'no',
      `usedby` varchar(255) NOT NULL,
      PRIMARY KEY  (`discountid`)
    ) ENGINE = InnoDB;";

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