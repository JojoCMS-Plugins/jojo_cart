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

class Jojo_Field_minfreight extends Jojo_Field
{
    var $error;

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
        $smarty->assign('methods',  Jojo::selectAssoc('SELECT * FROM {cart_freightmethod} ORDER BY shortname'));
        $smarty->assign('prices',   unserialize($this->value));
        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('fd_help',  htmlentities($this->fd_help));
        return $smarty->fetch('admin/fields/minfreight.tpl');
    }

    function setvalue($newvalue)
    {
        $this->value = serialize(Jojo::getFormData('fm_' . $this->fd_field . '_prices', array()));
        return true;
    }
}