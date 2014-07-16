<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

/* delete empty carts older than the greater of 1 day or cart_lifetime */
$limit = Jojo::getOption('cart_lifetime', 0) ? Jojo::getOption('cart_lifetime', 0) : 1;
$limittime = time() - ($limit * 60*60*24);

if (Jojo::tableExists('cart')) {
    Jojo::deleteQuery("DELETE FROM {cart} WHERE id='0' AND amount='0.00' AND updated<?", array($limittime));
}
