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

class Jojo_Field_freight extends Jojo_Field
{
    var $error;

    function displayJs()
    {
        global $smarty;
        return  $smarty->fetch('admin/fields/freight_js.tpl');
    }

    function checkvalue()
    {
        return true;
    }

    /*
     * Return the html for editing this field
     */
    function displayedit()
    {
        global $smarty;

        $freight = new Jojo_Cart_Freight($this->value);
        $methods = Jojo::selectAssoc('SELECT * FROM {cart_freightmethod} ORDER BY shortname');
        $smarty->assign('methods',                 $methods);
        $smarty->assign('methods_count',           count($methods));
        $smarty->assign('sharedmodels',            Jojo::selectAssoc('SELECT id, name FROM {cart_freight_model} ORDER BY name'));
        $smarty->assign('sharedmodels_nonpack',    Jojo::selectAssoc('SELECT id, name FROM cart_freight_model WHERE freightmodel NOT LIKE \'%:5:"model";s:6:"packed"%\' ORDER BY name'));
        $smarty->assign('freight_type',            $freight->getModel());
        $smarty->assign('freight_default',         $freight->getDefault());
        $smarty->assign('freight_default_base',    $freight->getDefaultBase());
        $smarty->assign('freight_default_min',     $freight->getDefaultMin());
        $smarty->assign('freight_regions',         $freight->getRegions());
        $smarty->assign('freight_regions_base',    $freight->getRegionsBase());
        $smarty->assign('freight_regions_min',     $freight->getRegionsMin());
        $smarty->assign('freight_combine',         $freight->getCombine());
        if ($freight->getModel() == "shared") {
            $smarty->assign('sharedmodel',         $freight->getSharedModel(true));
        }
        if ($freight->getModel() == "packed") {
            $smarty->assign('packs',         $freight->getPacks());
        }

        $smarty->assign('fd_field',      $this->fd_field);
        $smarty->assign('onlyadvanced',  ($this->fd_options == 'advanced'));
        $smarty->assign('value',         htmlentities($this->value, ENT_COMPAT, 'UTF-8'));
        $smarty->assign('fd_help',       htmlentities($this->fd_help));
        return $smarty->fetch('admin/fields/freight.tpl');
    }

    function setvalue($newvalue)
    {
        $freight = new Jojo_Cart_Freight();
        $type = Jojo::getFormData('fm_' . $this->fd_field . '_type', 'fixed');
        $freight->setModel($type);
        $freight->setCombine(Jojo::getFormData('fm_' . $this->fd_field . '_combine', array()));

        if ($type == 'fixed') {
            $freight->setDefault(Jojo::getFormData('fm_' . $this->fd_field . '_fixed_default', 0));
            $freight->setDefaultBase(Jojo::getFormData('fm_' . $this->fd_field . '_fixed_base', 0));
            $freight->setDefaultMin(Jojo::getFormData('fm_' . $this->fd_field . '_fixed_min', 0));
        } elseif ($type == 'region') {
            $freight->setDefault(Jojo::getFormData('fm_' . $this->fd_field . '_default', 0));
            $freight->setDefaultBase(Jojo::getFormData('fm_' . $this->fd_field . '_default_base', 0));
            $freight->setDefaultMin(Jojo::getFormData('fm_' . $this->fd_field . '_default_min', 0));
            $regions = $freight->getRegions();
            foreach ($regions as $region) {
                if (Jojo::getFormData('fm_' . $this->fd_field . '_region_'.$region['code'], false)) {
                    $freight->setFreight(Jojo::getFormData('fm_' . $this->fd_field . '_region_'.$region['code'], false), $region['code']);
                    $freight->setBasePrice(Jojo::getFormData('fm_' . $this->fd_field . '_region_base_'.$region['code'], false), $region['code']);
                    $freight->setMinPrice(Jojo::getFormData('fm_' . $this->fd_field . '_region_min_'.$region['code'], false), $region['code']);
                }
            }
        } elseif ($type == 'shared') {
            $freight->setSharedModel(Jojo::getFormData('fm_' . $this->fd_field . '_model', 0));
        } elseif ($type == 'packed') {
            $packs = array();
            foreach(Jojo::getFormData('fm_' . $this->fd_field . '_pack', array()) as $pack) {
                if ($pack['size'] && $pack['model']) {
                    $packs[$pack['size']] = $pack['model'];
                }
            }
            $freight->setPacks($packs);
        }

        $this->value = $freight->export();

        return true;
    }
}