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

$table = 'cart_region';
$query = "
     CREATE TABLE {cart_region} (
      `regioncode` varchar(30) NOT NULL,
      `name` varchar(255) NOT NULL,
      `displayorder` int(11) NOT NULL,
      `minfrieght` blob NOT NULL,
      PRIMARY KEY  (`regioncode`)
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

/* ensure we have the default regions in there */
$data = Jojo::selectQuery("SELECT * FROM {cart_region}");
if (!count($data)) {
    $defaultregions = array(
                            array('africa',         'Africa',         1),
                            array('asia',           'Asia',           2),
                            array('europe',         'Europe',         3),
                            array('middle_east',    'Middle East',    4),
                            array('north_america',  'North America',  5),
                            array('oceania',        'Oceania',        6),
                            array('south_america',  'South America',  7),
                            array('united_kingdom', 'United Kingdom', 8),
                            array('other',          'Rest of world',  9)
                           );

    foreach ($defaultregions as $region) {
        Jojo::insertQuery("INSERT INTO {cart_region} SET regioncode=?, name=?, displayorder=?", $region);
    }
}