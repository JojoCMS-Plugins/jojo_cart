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
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class Jojo_Plugin_Jojo_cart extends Jojo_Plugin
{
    /**
     * Store the class name of the product handler
     */
    static $productHandlers;

    /**
     * An array or class names of payment handlers
     */
    static $paymentHandlers;

    /**
     * gets the tax portion of a price. $type indicates whether $amount is an exclusive or inclusive amount
     */
    public static function getTax($amount, $type)
    {
        $tax_percentage = Jojo::getOption('cart_tax_amount', 0);
        if (in_array(strtolower($type), array('e', 'ex', 'exc', 'excl', 'exclusive', 'excluding'))) {
            $tax_amount = $amount * ($tax_percentage / 100);
        } elseif (in_array(strtolower($type), array('i', 'in', 'inc', 'incl', 'inclusive', 'including'))) {
            $excluding = $amount / (1 + ($tax_percentage / 100));
            $tax_amount = $amount - $excluding;
        }
        return $tax_amount;
    }

    /**
     * Add tax to an exclusive price
     */
    public static function addTax($exclusive)
    {
        return $exclusive + self::getTax($exclusive, 'exc');
    }

    /**
     * Removes tax from an inclusive amount
     */
    public static function removeTax($inclusive)
    {
        return $inclusive - self::getTax($inclusive, 'inc');
    }

    /**
     * returns boolean value indicating whether the cart should show add tax to line items and totals. This depends whether the order is being sent to an 'applytax' country (as per the shipping country field and 'Cart Countries' settings), and if this is not yet known the 'cart_tax_pricing_type' default is used instead
     */
    public static function getApplyTax()
    {
        $cart = self::getCart();

        if (!empty($cart->fields['shipping_country'])) {
            $data = Jojo::selectRow("SELECT applytax FROM {cart_country} WHERE countrycode=?", $cart->fields['shipping_country']);
            if (!empty($data['applytax'])) {
                $cart->order['apply_tax'] = Jojo::yes2True($data['applytax']);
                return $cart->order['apply_tax'];
            }
        }
        /* if we don't yet know the shipping country, assume the site default */
        $type = Jojo::getOption('cart_tax_pricing_type', 'inclusive');
        $cart->order['apply_tax'] = ($type == 'inclusive') ? true : false;
        return $cart->order['apply_tax'];
    }

    /**
     * Is this cart in test mode?
     */
    public static function isTestMode()
    {
        $testmode = Jojo::getOption('cart_test_mode', 'yes');
        return $testmode == 'no' ? false : true;
    }

    /**
     * to be called by product plugins in api.php - the product plugin must have a function called "getProductDetails" which takes a product ID and returns array(id, name, description, image, price, currency, code)
     */
    public static function setProductHandler($class)
    {
        if (!is_array(self::$productHandlers)) {
            self::$productHandlers = array();
        }

        if (!in_array($class, self::$productHandlers)) {
            self::$productHandlers[] = $class;
        }
        return true;
    }

    /**
     * Get the class name of the product handler
     */
    public static function getProductHandlers() {
        return (array)self::$productHandlers;
    }

    /**
     * Allow a payment handler to register itself with the cart
     *
     * var $class Class name of the payment handler
     */
    public static function setPaymentHandler($class)
    {
        if (!is_array(self::$paymentHandlers)) {
            self::$paymentHandlers = array();
        }

        if (!in_array($class, self::$paymentHandlers)) {
            self::$paymentHandlers[] = $class;
        }
        return true;
    }

    /**
     * Get and array of payment handlers
     */
    public static function getPaymentHandlers() {
        return is_array(self::$paymentHandlers) ? self::$paymentHandlers : array();
    }

    /**
     * Ensures the cart has a data_blob field - in case setup hasn't been run recently
     */
    public static function addDataBlobField()
    {
        static $has_blob;
        if (isset($has_blob)) return true;
        if (!Jojo::fieldExists('cart', 'data_blob')) {
            Jojo::structureQuery("ALTER TABLE `cart` ADD `data_blob` BLOB NOT NULL AFTER `data`");
        }
        $has_blob = true;
        return true;
    }

    /**
     * Retrieve a cart instance. If $token is supplied then return it from the
     * database, else return the one in the session.
     */
    public static function getCart($token = false) {
        self::addDataBlobField();
        if ($token && $data = Jojo::selectRow("SELECT *, OCTET_LENGTH(data_blob) AS blobsize FROM {cart} WHERE token = ?", $token)) {
            /* Return a database cart */
            //$_SESSION['jojo_cart'] = unserialize($data['data']);
            if ($data['blobsize'] > 0) {
                $_SESSION['jojo_cart'] = unserialize($data['data_blob']);
            } else {
                $_SESSION['jojo_cart'] = unserialize($data['data']);
            }

            /* save token to cookie */
            if (Jojo::getOption('cart_lifetime', 0)) {
                setcookie("jojo_cart_token", $token, time() + (60 * 60 * 24 * Jojo::getOption('cart_lifetime', 0)), '/' . _SITEFOLDER);
            }
            return $_SESSION['jojo_cart'];
        }

        /* Attempt to load the cart from a cookie */
        $cart_lifetime = Jojo::getOption('cart_lifetime', 0);
        if ($cart_lifetime && (!isset($_SESSION['jojo_cart']) || !is_object($_SESSION['jojo_cart']))) {
            if (!empty($_COOKIE['jojo_cart_token']) && $data = Jojo::selectRow("SELECT *, OCTET_LENGTH(data_blob) AS blobsize FROM {cart} WHERE token = ? AND updated > ? AND status='pending'", array($_COOKIE['jojo_cart_token'], strtotime('-'.$cart_lifetime.' day')))) {
                if ($data['blobsize'] > 0) {
                    $_SESSION['jojo_cart'] = unserialize($data['data_blob']);
                } else {
                    $_SESSION['jojo_cart'] = unserialize($data['data']);
                }
            }
        }

        /* Check that the session cart has all the fields */
        if (!isset($_SESSION['jojo_cart']) || !is_object($_SESSION['jojo_cart'])) {
            $_SESSION['jojo_cart'] = new stdClass();
        }
        $_SESSION['jojo_cart']->items      = isset($_SESSION['jojo_cart']->items)       ? $_SESSION['jojo_cart']->items       : array();
        $_SESSION['jojo_cart']->token      = isset($_SESSION['jojo_cart']->token)       ? $_SESSION['jojo_cart']->token       : self::newToken();
        $_SESSION['jojo_cart']->errors     = isset($_SESSION['jojo_cart']->errors)      ? $_SESSION['jojo_cart']->errors      : array();
        $_SESSION['jojo_cart']->fields     = isset($_SESSION['jojo_cart']->fields)      ? $_SESSION['jojo_cart']->fields      : array();
        $_SESSION['jojo_cart']->handler    = isset($_SESSION['jojo_cart']->handler)     ? $_SESSION['jojo_cart']->handler     : '';
        $_SESSION['jojo_cart']->amount     = isset($_SESSION['jojo_cart']->amount)      ? $_SESSION['jojo_cart']->amount      : '';
        $_SESSION['jojo_cart']->shipped    = isset($_SESSION['jojo_cart']->shipped)     ? $_SESSION['jojo_cart']->shipped     : 0;
        $_SESSION['jojo_cart']->cartstatus = isset($_SESSION['jojo_cart']->cartstatus)  ? $_SESSION['jojo_cart']->cartstatus  : 'pending';

        /* Create unique action code for use in admin emails, ensure they are not already in the database for another order */
        if (empty($_SESSION['jojo_cart']->actioncode)) {
            $query = 'SELECT * FROM {cart} WHERE actioncode = ?';
            do {
                $_SESSION['jojo_cart']->actioncode = Jojo::randomString(10, '0123456789abcdefghijklmnopqrstuvwxyz');
                $res = Jojo::selectQuery($query, $_SESSION['jojo_cart']->actioncode);
            } while (count($res) > 0);
        }
        ksort($_SESSION['jojo_cart']->items);

        /* Return the session cart */
        return $_SESSION['jojo_cart'];
    }

    /**
     * Save a cart instance to the database
     */
    public static function saveCart($cart = false) {
        if ($cart === false) {
            $cart = self::getCart();
        }
        $token = $cart->token;

        /* clear credit card fields - don't want those saved in the database */
        if (isset($cart->order['cardType']))        unset($cart->order['cardType']);
        if (isset($cart->order['cardNumber']))      unset($cart->order['cardNumber']);
        if (isset($cart->order['cardExpiryMonth'])) unset($cart->order['cardExpiryMonth']);
        if (isset($cart->order['cardExpiryYear']))  unset($cart->order['cardExpiryYear']);
        if (isset($cart->order['cardName']))        unset($cart->order['cardName']);

        $status = (isset($cart->cartstatus)) ? $cart->cartstatus : 'pending'; //default to pending

        /* Create unique action code for use in admin emails, ensure they are not already in the database for another order */
        if (!empty($cart->actioncode)) {
            $actioncode = $cart->actioncode;
        } else {
            $query = 'SELECT * FROM {cart} WHERE actioncode = ?';
            do {
                $actioncode = Jojo::randomString(10, '0123456789abcdefghijklmnopqrstuvwxyz');
                $res = Jojo::selectQuery($query, $actioncode);
            } while (count($res) > 0);
        }

       if(!isset($cart->id)) $cart->id = 0;

       if (empty($cart->order['amount'])) $cart->order['amount'] = 0;

        $cart->order['currency']        = self::getCartCurrency();
        $cart->order['currency_symbol'] = self::getCurrencySymbol($cart->order['currency']);

        /* Save */
        self::addDataBlobField();
        Jojo::updateQuery("REPLACE INTO {cart} SET id=?, token=?, data=?, data_blob=?, status=?, ip=?, userid = ?, updated=?, handler=?, amount=?, actioncode=?, shipped=?",
            array($cart->id, $token, serialize($cart), serialize($cart), $status, Jojo::getIp(), isset($_SESSION['userid']) ? $_SESSION['userid'] : '', time(), $cart->handler, $cart->order['amount'], $actioncode, $cart->shipped));
        /* save token to cookie */
        if (Jojo::getOption('cart_lifetime', 0)) {
            setcookie("jojo_cart_token", $token, time() + (60 * 60 * 24 * Jojo::getOption('cart_lifetime', 0)), '/' . _SITEFOLDER);
        }
        return true;
    }

    /**
     * Get the currency for the shopping cart.
     */
    public static function getCartCurrency($token=false)
    {
        /* If forcing a default currency, use that */
        if (Jojo::getOption('cart_force_default_currency', 'yes') == 'yes') {
            return Jojo::getOption('cart_default_currency', 'USD');
        }

        /* Scan products for currency */
        $cart = self::getCart($token);
        $currency = false;
        foreach ($cart->items as $item) {
            if ($currency && ($currency != $item['currency'])) {
                /* returns 'MIXED' if there are mixed currencies in the cart - this shouldn't happen */
                return 'MIXED';
            }
            $currency = $item['currency'];
        }
        return $currency;
    }

    /**
     * Returns the currency symbol for currency
     */
    public static function getCurrencySymbol($currency)
    {
        $currencies = array(
                            'USD' => '$',
                            'NZD' => '$',
                            'AUD' => '$',
                            'CAD' => '$',
                            'GBP' => '£',
                            'EUR' => '€',
                            'JPY' => '¥',
                           );
        return (isset($currencies[$currency])) ? $currencies[$currency] : '';
    }

    /**
     * Add a product to the cart
     *
     * var $id mixed  The id of the product to add
     */
    public static function addToCart($id, $quantity = false) {

        /* Get product details from the product plugin */
        $found = false;
        foreach (self::getProductHandlers() as $productHandler) {
            $item = call_user_func(array($productHandler, 'getProductDetails'), $id);
            if ($item) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            /* Product doesn't exist */
            return false;
        }

        if ($quantity !== false) {
            $quantity_to_add = $quantity;
        } else {
            $quantity_to_add = !empty($item['quantity']) ? $item['quantity'] : 1;
        }

        /* Get the current number in the cart */
        $cart = self::getCart();
        $id = $item['id'];
        $current = !empty($cart->items[$id]['quantity']) ? $cart->items[$id]['quantity'] : 0;

        return self::setQuantity($id, $current + $quantity_to_add);
    }

    /**
     * Remove a product from the cart
     */
    public static function removeFromCart($id) {
        /* get product details from the product plugin */
        $found = false;
        foreach (self::getProductHandlers() as $productHandler) {
            $item = call_user_func(array($productHandler, 'getProductDetails'), $id);
            if ($item) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            /* Product doesn't exist */
            return false;
        }

        /* Remove the product */
        $cart = self::getCart();
        unset($cart->items[$item['id']]);
        self::saveCart();

        /* Run hook */
        Jojo::runHook('jojo_cart_quantity_updated', array());

        return true;
    }

    /**
     * Empty all the details out of the cart
     */
    public static function emptyCart() {
        unset($_SESSION['jojo_cart']);
        setcookie("jojo_cart_token", '', time() + (60 * 60 * 24 * Jojo::getOption('cart_lifetime', 0)), '/' . _SITEFOLDER);
        return true;
    }

    /**
     * Empty just the items out of the cart
     */
    public static function emptyCartItems() {
        unset($_SESSION['jojo_cart']->items);
        return true;
    }

    /**
     * Set the quantity of a product in the cart
     *
     */
    public static function setQuantity($id, $qty) {
        /* get product details from the product plugin */
        $found = false;
        foreach (self::getProductHandlers() as $productHandler) {
            $item = call_user_func(array($productHandler, 'getProductDetails'), $id);
            if ($item) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            /* Product doesn't exist */
            return false;
        }

        /* Get the cart */
        $cart = self::getCart();

        if ($qty == 0 && Jojo::getOption('cart_zero_quantities', 'no')=='no') {
            return self::removeFromCart($id);
        }

        /* Get the item details */
        $item['quantity']   = isset($item['quantity_fixed']) && $item['quantity_fixed'] ? 1 : $qty;
        if (isset($item['min_quantity']) && $item['min_quantity'] && $item['quantity']) {
            $item['quantity'] = max($item['min_quantity'], $item['quantity']);
        }
        if (isset($item['max_quantity']) && $item['max_quantity'] && $item['quantity']) {
            $item['quantity'] = min($item['max_quantity'], $item['quantity']);
        }
        $item['baseprice']  = $item['price']; //baseprice is the original prices as stored in the DB, before tax calculations are applied (this value is exclusive or inclusive as per 'cart_tax_pricing_type' option)
        $item['netprice']   = $item['price']; //price is the price before discounts - netprice is the price after discounts
        $item['linetotal']  = $item['netprice'] * $qty;
        $item['currency']   = empty($item['currency']) ? Jojo::getOption('cart_default_currency', 'USD') : $item['currency'];
        $item['currency']   = strtoupper($item['currency']);

        /* Ensure the currency is acceptable */
        if (Jojo::getOption('cart_force_default_currency', 'yes') == 'yes') {
            $defaultcurrency = strtoupper(Jojo::getOption('cart_default_currency', 'USD'));
            if ($item['currency'] != $defaultcurrency) {
                $cart->errors[] = 'All products added to the cart must be in '.$defaultcurrency.'. '.$item['name'].' is in '. $item['currency'].'.';
                return false;
            }
        } else {
            /* Ensure the currency matches other items already in the cart */
            $cartcurrency = strtoupper(self::getCartCurrency());
            if ($cartcurrency && $item['currency'] != $cartcurrency) {
                $cart->errors[] = 'Because your cart already contains items in '.$cartcurrency.', you can only add '.$cartcurrency.' items to this cart. If you wish to purchase an item in another currency, please complete this transaction and start a new cart. The product you tried to add ('.$item['name'].') is in '. $item['currency'].'.';
                return false;
            }
        }

        $cart->items[$id] = $item;

        /* Run hook */
        Jojo::runHook('jojo_cart_quantity_updated', array());

        /* Update subtotal / total */
        $cart->order['subtotal'] = self::subTotal();
        $cart->order['amount']   = self::total();
        self::saveCart();
        return true;
    }

    /**
     * Get all the items in the cart
     */
    public static function getItems()
    {
        $cart = self::getCart();
        $items = $cart->items;
        $items = Jojo::applyFilter('jojo_cart_sort', $items);
        return $items;
    }

    public static function getNumItems($items)
    {
        $numItems = 0;
        foreach ($items as $k => $item) {
           $numItems += $item['quantity'];
        }

        return $numItems;
    }

    /**
     * Calculate the sub total for the current cart
     */
    public static function subTotal()
    {
        $cart = self::getCart();
        $cart->discount['exclusions'] = isset($cart->discount['exclusions']) ? $cart->discount['exclusions'] : array();
        $cart->discount['products']   = isset($cart->discount['products'])   ? $cart->discount['products']   : array();
        $cart->discount['minorder']   = isset($cart->discount['minorder'])   ? $cart->discount['minorder']   : 0;
        $cart->discount['percent']    = isset($cart->discount['percent'])    ? $cart->discount['percent']    : 0;
        $cart->discount['fixed']      = isset($cart->discount['fixed'])      ? $cart->discount['fixed']      : 0;
        $cart->discount['fixedorder'] = isset($cart->discount['fixedorder']) ? $cart->discount['fixedorder'] : 0;
        $cart->discount['singleuse']  = isset($cart->discount['singleuse'])  ? $cart->discount['singleuse']  : false;

        $cart->points['used']  = isset($cart->points['used'])  ? $cart->points['used']  : false;

        $subtotal = 0;
        foreach ($cart->items as $k => $item) {
            $cart->items[$k]['price'] = $cart->items[$k]['baseprice'];
            $cart->items[$k]['netprice'] = $cart->items[$k]['price'];

            /* Apply discounts */
            if (!in_array($item['id'], $cart->discount['exclusions']) &&
                (!count($cart->discount['products']) || in_array($item['id'], $cart->discount['products'])) &&
                $cart->items[$k]['quantity'] >= $cart->discount['minorder']) {
                if (isset($cart->discount['custom'][$item['id']])) {
                    if (preg_match('/^(\\d+)%$/', $cart->discount['custom'][$item['id']], $match)) {
                        /* Per item Percentage discount */
                        $percentage = $match[1];
                        $cart->items[$k]['netprice'] -= $cart->items[$k]['price'] * $percentage / 100;
                    } elseif (preg_match('/^(\\d+)$/', $cart->discount['custom'][$item['id']], $match)) {
                        /* Per item Fixed discount */
                        $fixed = $match[1];
                        $cart->items[$k]['netprice'] -= $fixed;
                    }
                } elseif ($cart->discount['percent']) {
                    /* Percentage discount */
                    $cart->items[$k]['netprice'] -= $cart->items[$k]['price'] * $cart->discount['percent'] / 100;
                } elseif ($cart->discount['fixed']) {
                    /* Fixed discount */
                    $cart->items[$k]['netprice'] -= $cart->discount['fixed'];
                }
            }

            /* add of remove tax to line items before totalling */
            $cart_tax_pricing_type = Jojo::getOption('cart_tax_pricing_type', 'inclusive');
            if (($cart_tax_pricing_type == 'exclusive') && self::getApplyTax()) {
                /* need to add tax to all amounts */
                $cart->items[$k]['price']    = self::addTax($cart->items[$k]['price']);
                $cart->items[$k]['netprice'] = self::addTax($cart->items[$k]['netprice']);
            } elseif (($cart_tax_pricing_type == 'inclusive') && !self::getApplyTax()) {
                /* need to remove tax from all amounts */
                $cart->items[$k]['price']    = self::removeTax($cart->items[$k]['price']);
                $cart->items[$k]['netprice'] = self::removeTax($cart->items[$k]['netprice']);
            }

            $cart->items[$k]['linetotal'] = $cart->items[$k]['netprice'] * $cart->items[$k]['quantity'];
            $cart->items[$k]['linetotal'] = Jojo::applyFilter('cart_linetotal', $cart->items[$k]['linetotal'], array($item['id'], $item['quantity']));
            $subtotal += $cart->items[$k]['linetotal'] ;
        }
        $cart->order['fixedorder'] = $cart->discount['fixedorder'];
        $subtotal -= $cart->order['fixedorder'];
        /* Apply Points discount */
        if ($cart->points['used']) {
            $pointsvalue = $cart->points['used']*Jojo::getOption('cart_loyalty_value', 0);
            $subtotal -= $pointsvalue;
        }
        $subtotal = max(0, $subtotal); //ensure discounts don't bring the total below 0
        $cart->order['subtotal'] = $subtotal;
        return $subtotal;
    }

    /**
     * Set the shipping region for this order
     */
    public static function setShippingRegion($region)
    {
        $cart = self::getCart();
        $cart->fields['shippingRegion'] = $region;
        self::saveCart();
    }

    /**
     * Set the shipping method for this order
     */
    public static function setShippingMethod($method)
    {
        $cart = self::getCart();
        $cart->fields['shippingMethod'] = $method;
        $data = Jojo::selectRow("SELECT longname FROM {cart_freightmethod} WHERE id=?", $method);
        $cart->fields['shippingMethodName'] = $data['longname'];
        self::saveCart();
    }

    /**
     * Get the shipping method for this order
     */
    public static function getShippingMethod()
    {
        $cart = self::getCart();
        return isset($cart->fields['shippingMethod']) ? $cart->fields['shippingMethod'] : -1;
    }

    public static function getFreight()
    {
        $cart = self::getCart();

        /* Currently, we cannot deal with freight unless it's in the default cart currency */
        if(Jojo::getOption('cart_freight_in_multiple_currencies', 'no')=='no'){
          if (self::getCartCurrency() != Jojo::getOption('cart_default_currency', 'USD')) {
            return false;
          }
        }

        /* Check for free shipping via a discount code */
        if (isset($cart->discount['freeshipping']) && ($cart->discount['freeshipping'] == true)) {
            return 0;
        }

        /* Check for a shipping region */
        if (!isset($cart->fields['shippingRegion'])) {
            return false;
        }
        $region = $cart->fields['shippingRegion'];

        /* Check for a shipping method */
        if (!isset($cart->fields['shippingMethod'])) {
            return false;
        }
        $method = $cart->fields['shippingMethod'];

        /* Get the shipping cost for each item in the cart */
        $total = 0;
        $sharedModelQuantities = array();
        foreach ($cart->items as $k => $item) {
            $freight = new Jojo_Cart_Freight($item['freight']);
            if ($freight->getModel() == 'shared') {
                /* Shared model, calculate this later */
                $modelid = $freight->getSharedModel(true);
                $freightUnits = $freight->getFreightUnits();
                $sharedModelQuantities[$modelid] = isset($sharedModelQuantities[$modelid]) ? $sharedModelQuantities[$modelid] + $freightUnits * $item['quantity'] : $freightUnits * $item['quantity'];
                $sharedModel[$modelid] = $freight->getSharedModel();
            } else {
                /* Individual freight, calculate now */
                $total += $freight->getFreight($region, $method, $item['quantity']);
            }
        }
        /* Get the shipping for the shared models */
        foreach ($sharedModelQuantities as $modelid => $quantity) {
            $quantity = Jojo::getOption('cart_freight_rounding', 'yes')=='yes' ? ceil($quantity) : $quantity;

            $total += $sharedModel[$modelid]->getFreight($region, $method, $quantity);
        }
        $total = max($total, Jojo_Cart_Freight::getRegionMinimum($region, $method));
        $total = Jojo::applyFilter('jojo_cart:getFreight:total', $total, $cart);

        /* add or remove tax to final freight price */
        $cart_tax_pricing_type = Jojo::getOption('cart_tax_pricing_type', 'inclusive');
        $cart_tax_shipping = (boolean)(Jojo::getOption('cart_tax_pricing_type_shipping', 'no')=='yes');
        if ($cart_tax_pricing_type == 'exclusive' && (self::getApplyTax() || $cart_tax_shipping)) {
            /* need to add tax to all amounts */
            $total    = self::addTax($total);
        } elseif ($cart_tax_pricing_type == 'inclusive' && !self::getApplyTax() && !$cart_tax_shipping) {
            /* need to remove tax from all amounts */
            $total    = self::removeTax($total);
        }

        return $total;
    }

    public static function getSurcharge()
    {
        /* check if we're using surcharges */
		if (!Jojo::getOption('cart_freight_surcharge', 0)) {
			return false;
		} 
        /* check if the subtotal is more than the surcharge trigger amount */
        $cart = self::getCart();
  		if ($cart->order['subtotal'] < (int)(Jojo::getOption('cart_freight_surcharge_at', 0))) {
			return false;
		}
		return (int)(Jojo::getOption('cart_freight_surcharge', 0));
    }

    /**
     * Calculate the total cost of this cart
     */
    public static function total()
    {
        return self::subTotal() + self::getFreight() + self::getSurcharge();
    }

    /**
     * Set gift wrapping options for this order
     */
    public static function setGiftWrap($giftwrap=true)
    {
        $cart = self::getCart();
        $cart->order['giftwrap'] = $giftwrap;
    }

    /**
     * Set the discount code to use for this cart
     */
    public static function applyDiscountCode($code)
    {
        $cart = self::getCart();
       /* If empty then clear last code */
        if (empty($code)) {
            unset($cart->discount);
            return true;
        }

        /* Check the code */
        $discount = Jojo::selectRow("SELECT * FROM {discount} WHERE discountcode=?", $code);
        if (empty($discount['discountcode'])) {
            /* The code is not valid */
            $cart->errors[] = "'$code' is not a valid discount code";
            unset($cart->discount);
            return false;
        } elseif (($discount['finishdate'] > 0) && (time() > $discount['finishdate'])) {
            /* The code has expired */
            unset($cart->discount);
            $cart->errors[] = 'This discount code has expired';
            return false;
        } elseif (($discount['startdate'] > 0) && (time() < $discount['startdate'])) {
            /* The code is not available yet */
            unset($cart->discount);
            $cart->errors[] = 'This discount code is not yet active';
            return false;
        } elseif (($discount['singleuse'] == 'yes') && ($discount['usedby'] != '')) {
            unset($cart->discount);
            $cart->errors[] = 'This discount code has already been used';
            return false;
        }

        /* Add discount details to the cart */
        $cart->discount = array();
        $cart->discount['code']       = $discount['discountcode'];
        $cart->discount['percent']    = $discount['discountpercent'];
        $cart->discount['fixed']      = $discount['discountfixed'];
        $cart->discount['fixedorder'] = $discount['fixedorder'];
        $cart->discount['minorder']   = $discount['minorder'];
        $cart->discount['products']   = array();
        $cart->discount['exclusions'] = array();
        $cart->discount['singleuse']  = ($discount['singleuse'] == 'yes') ? true : false;
        $cart->discount['freeshipping']  = (isset($discount['freeshipping']) && ($discount['freeshipping'] == 'yes')) ? true : false;

        /* Clean up codes and remove empty ones */
        foreach (explode("\n", str_replace(',', "\n", $discount['products'])) as $k => $v) {
            $v = trim($v);
            if (!empty($v)) {
                $cart->discount['products'][] = $v;
            }
        }

        /* Clean up codes and remove empty ones */
        foreach (explode("\n", str_replace(',', "\n", $discount['exclusions'])) as $k => $v) {
            $v = trim($v);
            if (!empty($v)) {
                $cart->discount['exclusions'][] = $v;
            }
        }

        /* apply custom discounts */
        foreach (explode("\n", str_replace(',', "\n", $discount['custom'])) as $k => $v) {
            $v = trim($v);
            if (!empty($v)) {
                $parts = explode('=', $v);
                $cart->discount['custom'][trim($parts[0])] = trim($parts[1]);
            }
        }

        Jojo::runHook('jojo_cart_apply_discount_code', array($cart, $discount));

        self::total();
        return true;
    }

    public static function applyPoints($points=false)
    {
        global $_USERID;
        $cart = self::getCart();
       /* If empty then clear points used */
        if (!$points) {
            $cart->points['used']= 0;
            $cart->points['discount']= 0;
            $cart->points['currentbalance'] = isset($cart->points['balance']) ? $cart->points['balance'] : 0;
            return true;
        }

        /* Check the user actually has points available */
        if (isset($cart->points) && $cart->points['balance'] && $cart->points['balance']>0) {
            /* Add details to the cart */
            $cart->points['used'] = $points;
            $cart->points['currentbalance'] = $cart->points['balance'] - $cart->points['used'];
            $cart->points['discount'] = Jojo::getOption('cart_loyalty_value')*$points;
           
        } else {
            $cart->points['used'] = 0;
            $cart->points['discount'] = 0;
            $cart->points['currentbalance'] = $cart->points['balance'];
            return true;        
        }

        self::total();
        return true;
    }

    /**
     * Generates a new unique token
     */
    public static function newToken()
    {
        while (true) {
            $token = Jojo::randomstring(20);
            if (!Jojo::selectRow("SELECT token FROM {cart} WHERE token = ?", $token)) {
                return $token;
            }
        }
    }

    function _getContent()
    {
        global $smarty, $_USERGROUPS, $_USERID;

        $testmode = self::isTestMode();

        $content = array();

        $languageurlprefix = Jojo::getPageUrlPrefix($this->page['pageid']);

        /* Read GET variables */
        $action   = Jojo::getFormData('action',      '');
        $id       = Jojo::getFormData('id',       false);
        $discount = Jojo::getFormData('discount', false);
        $points = Jojo::getFormData('points', false);

        /* Get the cart array */
        $cart = self::getCart();
        $smarty->assign('token',  $cart->token);
        $smarty->assign('status', $cart->cartstatus);
        $cart->order['subtotal'] = self::subTotal();

        if ($_USERID && Jojo::getOption('cart_loyalty_cost', '') && JOJO_Plugin_Jojo_cart::getCartCurrency($cart->token)==Jojo::getOption('cart_default_currency', 'USD')) {
            if (isset($cart->points['balance'])) {
                $pointsavailable = $cart->points['balance']; 
            } else {
                $pointsavailable = Jojo::selectRow("SELECT points FROM {cart_points} WHERE userid=?", array($_USERID));
                $pointsavailable =  ($pointsavailable && $pointsavailable['points']>0) ? $pointsavailable['points'] : 0;
                $cart->points['balance'] = $pointsavailable;
            }
            $smarty->assign('pointsavailable', $pointsavailable);
            if (isset($cart->points['used'])) {
                $smarty->assign('pointsused', $cart->points['used']);
                $smarty->assign('pointsdiscount', isset($cart->points['discount']) ? $cart->points['discount'] : '');
            }
        }
        /* calculate freight */
        $cart->order['freight'] = self::getFreight();

         /* calculate surcharge */
        $cart->order['surcharge'] = self::getSurcharge();
        $cart->order['surchargedescription'] = Jojo::getOption('cart_freight_surcharge_description', '');

       /* calculate total */
        $cart->order['amount'] = $cart->order['subtotal'] + $cart->order['freight'] + $cart->order['surcharge'];

        /* Assign vars to Smarty */
        $smarty->assign('items', self::getItems());
        if (!count(self::getItems())) {
            $smarty->assign('cartisempty', true);
        } else {
            $cart->order['numitems'] = self::getNumItems($cart->items);
        }

        if ($action == 'cancel') {
            $content['title']    = 'Transaction cancelled';
            $content['seotitle'] = 'Transaction cancelled';
            $content['content'] = $smarty->fetch('jojo_cart_cancel.tpl');
            return $content;
        }

        if ($action == 'complete') {
            $token = Jojo::getFormData('token', false);
            if ($token) {
                $savedcart = self::getCart($token);
                if (isset($savedcart->receipt)) $smarty->assign('receipt', $savedcart->receipt);
                if (isset($savedcart->handler)) $smarty->assign('handler', $savedcart->handler);
            }
            if ($_USERID) {
                $current = Jojo::selectRow("SELECT points FROM {cart_points} WHERE userid=?", array($_USERID));
                if ($current) {
                    $smarty->assign('pointsbalance', $current['points']);
                }
            }

            $content['title']    = '##Transaction complete##';
            $content['seotitle'] = '##Transaction complete##';
            $content['content']  = $smarty->fetch('jojo_cart_complete.tpl');

            /* empty the cart from session - this may not have happened at process stage if done via remote call */
            self::emptyCart();

            /* Add complete breadcrumb */
            $breadcrumbs                      = $this->_getBreadCrumbs();
            $breadcrumb                       = array();
            $breadcrumb['name']               = 'Complete';
            $breadcrumb['rollover']           = 'Complete';
            $breadcrumb['url']                = $languageurlprefix.'cart/complete/';
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
            $content['breadcrumbs']           = $breadcrumbs;

            return $content;
        }

        if ($action == 'payment-info') {
            $token = Jojo::getFormData('token', false);
            if ($token) {
                $savedcart = self::getCart($token);
                if (isset($savedcart->receipt)) $smarty->assign('receipt', $savedcart->receipt);
                if (isset($savedcart->handler)) $smarty->assign('handler', $savedcart->handler);
            }
            $smarty->assign('fields',   $savedcart->fields);
            $smarty->assign('order',    $savedcart->order);
            $smarty->assign('items',    $savedcart->items);
            $smarty->assign('id',       $savedcart->id);
            $content['title']    = 'Payment Information';
            $content['seotitle'] = 'Payment Information';
            $content['content']  = $smarty->fetch('jojo_cart_payment_info.tpl');

            /* empty the cart from session - this may not have happened at process stage if done via remote call */
            self::emptyCart();

            /* Add complete breadcrumb */
            $breadcrumbs                      = $this->_getBreadCrumbs();
            $breadcrumb                       = array();
            $breadcrumb['name']               = 'Payment Information';
            $breadcrumb['rollover']           = 'Payment Information';
            $breadcrumb['url']                = $languageurlprefix.'cart/payment-info/';
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
            $content['breadcrumbs']           = $breadcrumbs;

            return $content;
        }

        /* View cart - Default action */
        if (!isset($cart->fields)) $cart->fields = array();
        if (!isset($cart->order))  $cart->order  = array();
        if (isset($cart->errors))  {
            $smarty->assign('errors', $cart->errors);
            unset($cart->errors);
        }

        /* are we using the discount code functionality? No need to show the UI if the discount table is empty */
        $data = Jojo::selectRow("SELECT COUNT(*) AS numdiscounts FROM {discount}");
        $usediscount = $data['numdiscounts'] > 0 ? $smarty->assign('usediscount', true) : '';

        $cart->order['currency']        = self::getCartCurrency();
        $cart->order['currency_symbol'] = self::getCurrencySymbol($cart->order['currency']);

        $smarty->assign('fields',   $cart->fields);
        $smarty->assign('order',    $cart->order);
        $smarty->assign('discount', $cart->discount);
        session_write_close();
        $content['content'] = $smarty->fetch('jojo_cart.tpl');

        return $content;
    }

    function getCorrectUrl()
    {
        $action   = Jojo::getFormData('action',   '');
        $id       = Jojo::getFormData('id',       false);
        $discount = Jojo::getFormData('discount', false);
        $languageurlprefix = Jojo::getPageUrlPrefix($this->page['pageid']);

        switch ($action) {
        case 'cheque':
            $expectedurl = _SECUREURL.'/'.$languageurlprefix.'cart/cheque/';
            break;
        case 'complete':
           $expectedurl =  _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            break;
        case 'payment-info':
           $expectedurl =  _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            break;
        case 'cancel':
           $expectedurl =  _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            break;
        default:
            /*  */
            $allowed_vars = array('__utma', '__utmb', '__utmc', '__utmx', '__utmz', 'gclid=', 'gad=', 'OVKEY=', 'OVRAW=', 'OVMTC=');
            foreach ($allowed_vars as $var) {
                if (strpos($_SERVER['REQUEST_URI'], $var) !== false) {
                    return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                }
            }
            $expectedurl = _SECUREURL.'/'.$languageurlprefix.'cart/';
        }

        return $expectedurl;
    }

    /**
     * Auto load a class file if we know where to find it
     *
     */
    public static function autoload($classname)
    {
        if (strtolower($classname) == 'jojo_cart_freight') {
            require_once(dirname(__FILE__) . '/classes/Jojo/Cart/Freight.php');
        }
    }

    /**
     * If on the 'transaction complete' page, add Google Analytics tracking code
     */
    public static function foot()
    {
        global $smarty;
        $action = Jojo::getFormData('action', '');

        if ($action != 'complete' || !Jojo::getOption('analyticscode', false)) {
            return false;
        }

        $token  = Jojo::getFormData('token',  false);
        if ($token) {
            $savedcart = self::getCart($token);
            $smarty->assign('token',  $savedcart->token);
            $smarty->assign('items',  $savedcart->items);
            $smarty->assign('order',  $savedcart->order);
            $smarty->assign('fields', $savedcart->fields);
        }
        $smarty->assign('testmode', self::isTestMode());
        self::emptyCart();
        return $smarty->fetch('jojo_cart_foot.tpl');
    }
}
