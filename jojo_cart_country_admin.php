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

class jojo_plugin_jojo_cart_country_admin extends JOJO_Plugin
{
    function _getContent()
    {
        global $smarty;

        $content = array();

        jojo_plugin_Admin::adminMenu();

        /* handle deleting of a region */
        if (Jojo::getFormData('delete', false)) {
            $old = Jojo::getFormData('delete_region', false);
            $new = Jojo::getFormData('reassign_region', false);
            if ($old && $new) {
                Jojo::deleteQuery("DELETE FROM {cart_region} WHERE regioncode=? LIMIT 1", $old);
                Jojo::updateQuery("UPDATE {cart_country} SET region=? WHERE region=?", array($new, $old));
                Jojo::redirect(_SITEURL.'/'.$this->page['pg_url'].'/');
            }
        }

        /* handle adding a new region */
        if (Jojo::getFormData('add', false)) {
            $code = Jojo::getFormData('add_region_code', false);
            $name = Jojo::getFormData('add_region_name', false);
            $data = Jojo::selectQuery("SELECT * FROM {cart_region} WHERE regioncode=?", $code);
            if ($code && $name && !count($data)) {
                Jojo::insertQuery("INSERT INTO {cart_region} SET regioncode=?, name=?", array($code, $name));
                Jojo::redirect(_SITEURL.'/'.$this->page['pg_url'].'/');
            }
        }

        $regions   = Jojo::selectQuery("SELECT * FROM {cart_region} ORDER BY displayorder");
        $countries = Jojo::selectQuery("SELECT * FROM {cart_country} ORDER BY name");

        $smarty->assign('regions',   $regions);
        $smarty->assign('countries', $countries);

        $content['content'] = $smarty->fetch('jojo_cart_country_admin.tpl');

        return $content;
    }
}
