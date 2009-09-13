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
 * @author  Mike Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class Jojo_Cart_Freight {
    /**
     * What pricing model to us
     */
    private $model   = 'fixed';

    /**
     * Base price per region
     */
    private $base    = array();

    /**
     * Per item per region price
     */
    private $data    = array();

    /**
     * Minimum Shipping cost
     */
    private $minimum = array();

    /**
     * Default per region price
     */
    private $default = array();

    /**
     * How many items to combine for shipping
     */
    private $combine = array();

    /**
     * Pack sizes
     */
    private $packs = array();

    /**
     * Shared Model ID to use
     */
    private $sharedmodel = false;

    function __construct($serialized = null)
    {
        /* Unserialize the input */
        if (!empty($serialized) && !is_array($serialized)) {
            $input = unserialize($serialized);
        } elseif (is_array($serialized)) {
            $input = $serialized;
        } else {
            $input = array();
        }
        $this->setModel(isset($input['model'])  ? $input['model']  : 'fixed');
        $this->data        = isset($input['data'])        ? $input['data']  : array();
        $this->default     = isset($input['default'])     ? $input['default'] : array();
        $this->base        = isset($input['base'])        ? $input['base'] : array();
        $this->combine     = isset($input['combine'])     ? $input['combine'] : array();
        $this->minimum     = isset($input['minimum'])     ? $input['minimum'] : array();
        $this->sharedmodel = isset($input['sharedmodel']) ? $input['sharedmodel'] : array();
        $this->packs       = isset($input['packs'])       ? $input['packs'] : array();
    }

    /* exports the object into a string, for storing in a database / session */
    function export()
    {
        return serialize(array(
                            'model' => $this->model,
                            'data' => $this->data,
                            'base' => $this->base,
                            'default' => $this->default,
                            'combine' => $this->combine,
                            'minimum' => $this->minimum,
                            'sharedmodel' => $this->sharedmodel,
                            'packs' => $this->packs
                            )
                        );
    }

    /**
     * Get the shared model being used.
     */
    function getSharedModel($idOnly = false)
    {
        if ($idOnly) {
            return $this->sharedmodel;
        }

        static $cache;
        if (!isset($cache[$this->sharedmodel])) {
            $row = Jojo::selectRow('SELECT * FROM {cart_freight_model} WHERE id = ?', $this->sharedmodel);
            if (!$row) {
                $cache[$this->sharedmodel] = false;
            } else {
                $cache[$this->sharedmodel] = new Jojo_Cart_Freight($row['freightmodel']);
            }
        }
        return $cache[$this->sharedmodel];
    }

    /**
     * Set the id of the shared model to use
     */
    function setSharedModel($id)
    {
        $this->sharedmodel = $id;
    }

    /**
     * Set the freight model to be used. Options are limited to fixed and region.
     */
    function setModel($model = false)
    {
        $valid = array('fixed', 'region', 'shared', 'packed');
        $this->model = in_array($model, $valid) ? $model : 'fixed';
        return true;
    }

    /**
     * Returns the freight model used by this freight object.
     */
    function getModel()
    {
        return $this->model;
    }

    /**
     * Returns the freight model used by this freight object.
     */
    function getCombine()
    {
        return $this->combine;
    }

    /**
     * Returns the freight model used by this freight object.
     */
    function setCombine($data)
    {
        $this->combine = $data;
    }

    /**
     * Returns the frieght methods for a particular region
     */
    function getFreightMethods($region)
    {
        if ($this->getModel() == 'shared') {
            $sharedModel = $this->getSharedModel();
            if ($sharedModel) {
                return $sharedModel->getFreightMethods($region);
            } else {
                return array();
            }
        }

        if ($this->getModel() == 'packed') {
            $methods = array();
            foreach ($this->packs as $size => $modelid) {
                $row = Jojo::selectRow('SELECT * FROM {cart_freight_model} WHERE id = ?', $modelid);
                $sharedmodel = new Jojo_Cart_Freight($row['freightmodel']);
                foreach($sharedmodel->getFreightMethods($region) as $k => $v) {
                    $methods[$k] = $v;
                }
            }
            return $methods;
        }

        /* Get Methods descriptions */
        $methodNames = Jojo::selectAssoc('SELECT * FROM {cart_freightmethod} ORDER BY longname');

        /* Default pricing */
        $methods = array();
        foreach($this->default as $method => $price) {
            if (strtoupper($price) != 'NA') {
                $methods[$method] = $methodNames[$method]['longname'];
            }
        }

        if (!isset($this->data[$region])) {
            return $methods;
        }

        /* Regional pricing over riding defaults */
        foreach($this->data[$region] as $method => $price) {
            if ($price === '') {
                /* Use default */
                continue;
            } elseif (strtoupper($price) == 'NA') {
                /* Not avaliable here */
                unset($methods[$method]);
            } else {
                /* Use region price */
                $methods[$method] = $methodNames[$method]['longname'];
            }
        }

        return $methods;
    }

    /**
     * Gets the actual freight cost to ship
     * $quantity units via $method to $region
     */
    function getFreight($region, $method, $quantity)
    {
        if ($this->model == 'packed') {
            $packs = $this->getPacks();
            $packQuantities = array();
            foreach (Jojo_Cart_Freight::bestPacksizes($quantity, array_keys($packs)) as $packSize) {
                $packQuantities[$packSize] = isset($packQuantities[$packSize]) ? $packQuantities[$packSize] + 1 : 1;
            }
            $total = 0;
            foreach($packQuantities as $packSize => $packQuantity) {
                $modelid = $packs[$packSize];
                $row = Jojo::selectRow('SELECT * FROM {cart_freight_model} WHERE id = ?', $modelid);
                $sharedmodel = new Jojo_Cart_Freight($row['freightmodel']);
                $total += $sharedmodel->getFreight($region, $method, $packQuantity);
            }
            return $total;
        }

        /* Default Per Item pricing */
        $pricePerUnit = isset($this->default[$method]) ? $this->default[$method] : 'NA';

        /* Regional Per Item override */
        if (isset($this->data[$region][$method]) && $this->data[$region][$method] !== '') {
            $pricePerUnit = $this->data[$region][$method];
        }

        /* Can we ship there? */
        if (strtoupper($pricePerUnit) == 'NA') {
            return false;
        }

        /* Default Base Item pricing */
        $priceBase = isset($this->base['default'][$method]) ? $this->base['default'][$method] : 0;

        /* Regional Per Item override */
        if (isset($this->base[$region][$method]) && $this->base[$region][$method] !== '') {
            $priceBase = $this->base[$region][$method];
        }

        /* Combine items together? */
        if (isset($this->combine[$method])) {
            $combine = max(1, $this->combine[$method]);
            $quantity = ceil($quantity / $combine);
        }
        $totalPrice = $priceBase + ($pricePerUnit * $quantity);

        /* Default Minimum Price */
        $priceMinimum = isset($this->minimum['default'][$method]) ? $this->minimum['default'][$method] : 0;

        /* Regional Minimum override */
        if (isset($this->minimum[$region][$method]) && $this->minimum[$region][$method] !== '') {
            $priceMinimum = $this->minimum[$region][$method];
        }

        return max($priceMinimum, $totalPrice);
    }

    /**
     * Given a number of items and an array of available pack sizes, work out
     * how to pack them using the minimum number of packs.
     *
     * Implements the a dynamic programming solution to the Change-Making problem.
     */
    private static function bestPacksizes($targetPacksize, $packsizes) {
        $packsizesUsed[0] = 0;
        $lastPack[0] = 1;

        for ($packSize = max($packsizesUsed) + 1; $packSize <= $targetPacksize; $packSize++ ) {
            $minPacksizes = $packSize;
            $newPacksize = 1;

            foreach ($packsizes as $value) {
                if ($value > $packSize) {
                    continue;
                }
                if ($packsizesUsed[$packSize - $value] + 1 < $minPacksizes) {
                    $minPacksizes = $packsizesUsed[$packSize - $value] + 1;
                    $newPacksize  = $value;
                }
            }

            $packsizesUsed[$packSize] = $minPacksizes;
            $lastPack[$packSize]  = $newPacksize;
        }

        $packs = array();
        $oneallowed = in_array(1, $packsizes);
        while ($targetPacksize > 0) {
            $size = $lastPack[$targetPacksize];
            if (!$oneallowed && $size == 1) {
                return false;
            }
            $packs[] = $size;
            $targetPacksize -= $size;
        }
        return $packs;
    }

    /**
     * Get the minium freight cost for a region and shipping method
     */
    public static function getRegionMinimum($region, $method)
    {
        $region = Jojo::selectRow('SELECT minfrieght FROM {cart_region} WHERE regioncode = ?', $region);

        if (!$region || !$region['minfrieght']) {
            return 0;
        }
        $prices = unserialize($region['minfrieght']);
        return (isset($prices[$method]) && $prices[$method]) ? $prices[$method] : 0;
    }

    /* returns an array of all regions, with current prices if applicable (use this for building the UI) */
    function getRegions()
    {
        $output = array();

        $data = Jojo::selectQuery("SELECT * FROM {cart_region} ORDER BY name");
        foreach ($data as $region) {
            $price = (isset($this->data[$region['regioncode']])) ? $this->data[$region['regioncode']] : array();
            $output[] = array('code' => $region['regioncode'], 'name' => $region['name'], 'price' => $price);
        }
        return $output;
    }

    /* returns an array of all regions, with current prices if applicable (use this for building the UI) */
    function getRegionsBase()
    {
        $output = array();

        $data = Jojo::selectQuery("SELECT * FROM {cart_region} ORDER BY name");
        foreach ($data as $region) {
            $price = (isset($this->base[$region['regioncode']])) ? $this->base[$region['regioncode']] : array();
            $output[] = array('code' => $region['regioncode'], 'name' => $region['name'], 'price' => $price);
        }
        return $output;
    }

    /**
     * Returns an array of all regions, with minimum prices if applicable (use this for building the UI)
     */
    function getRegionsMin()
    {
        $output = array();

        $data = Jojo::selectQuery("SELECT * FROM {cart_region} ORDER BY name");
        foreach ($data as $region) {
            $price = (isset($this->minimum[$region['regioncode']])) ? $this->minimum[$region['regioncode']] : array();
            $output[] = array('code' => $region['regioncode'], 'name' => $region['name'], 'price' => $price);
        }
        return $output;
    }

    /**
     * Sets the default priceing
     */
    function setDefault($default = array())
    {
        $this->default = $default;
    }

    /**
     * Sets the default base pricing
     */
    function setDefaultBase($default = array())
    {
        $this->base['default'] = $default;
    }

    /**
     * Gets the default base pricing
     */
    function getDefaultBase()
    {
        return isset($this->base['default']) ? $this->base['default'] : array();
    }

    /**
     * Sets the default minimum pricing
     */
    function setDefaultMin($default = array())
    {
        $this->minimum['default'] = $default;
    }

    /**
     * Gets the default minimum pricing
     */
    function getDefaultMin()
    {
        return isset($this->minimum['default']) ? $this->minimum['default'] : array();
    }

    /**
     * Get's the default priceing
     */
    function getDefault($default = array())
    {
        return $this->default;
    }

    /**
     * Set a freight price
     *
     * Leave the second argument blank for "fixed" model.
     * For "region" model, call this function repeatedly for each region.
     */
    function setFreight($price, $regioncode = false)
    {
        if (!$regioncode) {
            $this->setDefault($price);
        } else {
            $this->data[$regioncode] = $price;
        }
        return true;
    }

    /**
     * Set a base price
     *
     * Leave the second argument blank for "fixed" model.
     * For "region" model, call this function repeatedly for each region.
     */
    function setBasePrice($price, $regioncode = false)
    {
        if (!$regioncode) {
            $this->setDefaultBase($price);
        } else {
            $this->base[$regioncode] = $price;
        }
        return true;
    }

    /**
     * Set a minimum price
     *
     * Leave the second argument blank for "fixed" model.
     * For "region" model, call this function repeatedly for each region.
     */
    function setMinPrice($price, $regioncode = false)
    {
        if (!$regioncode) {
            $this->setDefaultMinPrice($price);
        } else {
            $this->minimum[$regioncode] = $price;
        }
        return true;
    }

    /**
     * Get the pack size details
     *
     * Returns an array where the key is the pack size and the value is the
     * id of the shared model for this pack
     */
    function getPacks()
    {
        return $this->packs;
    }

    /**
     * Set the pack size details
     *
     * Takes an array where the key is the pack size and the value is the
     * id of the shared model for this pack
     */
    function setPacks($packs)
    {
        $this->packs = $packs;
    }
}