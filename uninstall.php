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

/* Remove the cart pages from the menu */
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link='jojo_plugin_jojo_cart'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link='jojo_plugin_jojo_cart_country_admin'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link='jojo_plugin_Jojo_cart_payment'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link='jojo_plugin_Jojo_cart_process'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link='jojo_plugin_Jojo_cart_shipping'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link='jojo_plugin_Jojo_cart_update'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_url='admin/cart'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_url='admin/edit/discount'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_url='admin/edit/cart_freightmethod'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_url='admin/edit/cart_region'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_url='admin/edit/cart_freight_model'");


