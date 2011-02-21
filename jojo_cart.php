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

class JOJO_Plugin_Jojo_cart extends JOJO_Plugin
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
     * Retrieve a cart instance. If $token is supplied then return it from the
     * database, else return the one in the session.
     */
    public function getCart($token = false) {
        if ($token && $data = Jojo::selectRow("SELECT * FROM {cart} WHERE token = ?", $token)) {
            /* Return a database cart */
            $_SESSION['jojo_cart'] = unserialize($data['data']);
            return $_SESSION['jojo_cart'];
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
        $_SESSION['jojo_cart']->cartstatus    = isset($_SESSION['jojo_cart']->cartstatus)     ? $_SESSION['jojo_cart']->cartstatus     : 'pending';

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
    public function saveCart($cart = false) {
        $cart = self::getCart($cart);
        $token = $cart->token;

        /* clear credit card fields - don't want those saved in the database */
        unset($cart->order['cardType']);
        unset($cart->order['cardNumber']);
        unset($cart->order['cardExpiryMonth']);
        unset($cart->order['cardExpiryYear']);
        unset($cart->order['cardName']);

        $status = isset($cart->cartstatus) ? $cart->cartstatus : 'pending'; //default to pending

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

        /* Save */
        Jojo::updateQuery("REPLACE INTO {cart} SET id=?, token=?, data=?, status=?, ip=?, userid = ?, updated=?, handler=?, amount=?, actioncode=?, shipped=?",
            array($cart->id, $token, serialize($cart), $status, Jojo::getIp(), isset($_SESSION['userid']) ? $_SESSION['userid'] : '', time(), $cart->handler, $cart->order['amount'], $actioncode, $cart->shipped));
        return true;
    }

    /**
     * Get the currency for the shopping cart.
     */
    public function getCartCurrency($token=false)
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
    function addToCart($id, $quantity = false) {

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
    function removeFromCart($id) {
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

        /* Run hook */
        Jojo::runHook('jojo_cart_quantity_updated', array());

        return true;
    }

    /**
     * Empty all the details out of the cart
     */
    function emptyCart() {
        unset($_SESSION['jojo_cart']);
        return true;
    }

    /**
     * Set the quantity of a product in the cart
     *
     */
    function setQuantity($id, $qty) {
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

        /* Get the item details */
        $item['quantity']   = isset($item['quantity_fixed']) && $item['quantity_fixed'] ? 1 : $qty;
        if (isset($item['min_quantity']) && $item['min_quantity'] && $item['quantity']) {
            $item['quantity'] = max($item['min_quantity'], $item['quantity']);
        }
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

        /* Get the cart */
        $cart = self::getCart();
        $cart->items[$id] = $item;

        /* Run hook */
        Jojo::runHook('jojo_cart_quantity_updated', array());

        /* Update subtotal / total */
        $cart->order['subtotal'] = self::subTotal();
        $cart->order['amount']   = self::total();
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
    function subTotal()
    {
        $cart = self::getCart();
        $cart->discount['exclusions'] = isset($cart->discount['exclusions']) ? $cart->discount['exclusions'] : array();
        $cart->discount['products']   = isset($cart->discount['products'])   ? $cart->discount['products']   : array();
        $cart->discount['minorder']   = isset($cart->discount['minorder'])   ? $cart->discount['minorder']   : 0;
        $cart->discount['percent']    = isset($cart->discount['percent'])    ? $cart->discount['percent']    : 0;
        $cart->discount['fixed']      = isset($cart->discount['fixed'])      ? $cart->discount['fixed']      : 0;

        $subtotal = 0;
        foreach ($cart->items as $k => $item) {
            $cart->items[$k]['netprice'] = $item['price'];

            /* Apply discounts */
            if (!in_array($item['id'], $cart->discount['exclusions']) &&
                (!count($cart->discount['products']) || in_array($item['id'], $cart->discount['products'])) &&
                $cart->items[$k]['quantity'] >= $cart->discount['minorder']) {
                if (isset($cart->discount['custom'][$item['id']])) {
                    if (preg_match('/^(\\d+)%$/', $cart->discount['custom'][$item['id']], $match)) {
                        /* Per item Percentage discount */
                        $percentage = $match[1];
                        $cart->items[$k]['netprice'] -= $item['price'] * $percentage / 100;
                    } elseif (preg_match('/^(\\d+)$/', $cart->discount['custom'][$item['id']], $match)) {
                        /* Per item Fixed discount */
                        $fixed = $match[1];
                        $cart->items[$k]['netprice'] -= $fixed;
                    }
                } elseif ($cart->discount['percent']) {
                    /* Percentage discount */
                    $cart->items[$k]['netprice'] -= $item['price'] * $cart->discount['percent'] / 100;
                } elseif ($cart->discount['fixed']) {
                    /* Fixed discount */
                    $cart->items[$k]['netprice'] -= $cart->discount['fixed'];
                }

/** This looks like custom code for a specific site?
                if (preg_match('/^(.*?)-T(\\d*)$/i', $item['id'], $match)) {
                    $newid = $match[1];
                    foreach ($cart->discount['custom'] as $c_id => $c_v) {
                        if (preg_match('/^(\\d+)%$/', $cart->discount['custom'][$newid], $match)) {
                            $percentage = $match[1];
                            $cart->items[$k]['netprice'] -= $item['price'] * $percentage / 100;
                        } elseif (preg_match('/^(\\d+)$/', $cart->discount['custom'][$item['id']], $match)) {
                            $fixed = $match[1];
                            $cart->items[$k]['netprice'] -= $fixed;
                        }
                    }
                }
**/
            }

            $cart->items[$k]['linetotal'] = $cart->items[$k]['netprice'] * $cart->items[$k]['quantity'];
            $cart->items[$k]['linetotal'] = Jojo::applyFilter('cart_linetotal', $cart->items[$k]['linetotal'], array($item['id'], $item['quantity']));
            $subtotal += $cart->items[$k]['linetotal'] ;
        }

        $cart->order['subtotal'] = $subtotal;
        return $subtotal;
    }

    /**
     * Set the shipping region for this order
     */
    function setShippingRegion($region)
    {
        $cart = self::getCart();
        $cart->fields['shippingRegion'] = $region;
        self::saveCart();
    }

    /**
     * Set the shipping method for this order
     */
    function setShippingMethod($method)
    {
        $cart = self::getCart();
        $cart->fields['shippingMethod'] = $method;
        self::saveCart();
    }

    /**
     * Get the shipping method for this order
     */
    function getShippingMethod()
    {
        $cart = self::getCart();
        return isset($cart->fields['shippingMethod']) ? $cart->fields['shippingMethod'] : -1;
    }

    function getFreight()
    {
        $cart = self::getCart();

        /* Currently, we cannot deal with freight unless it's in the default cart currency */
        if(Jojo::getOption('cart_freight_in_multiple_currencies', 'no')=='no'){
          if (self::getCartCurrency() != Jojo::getOption('cart_default_currency', 'USD')) {
            return false;
          }
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
            $total += $sharedModel[$modelid]->getFreight($region, $method, ceil($quantity));
        }
        $total = max($total, Jojo_Cart_Freight::getRegionMinimum($region, $method));
        $total = Jojo::applyFilter('jojo_cart:getFreight:total', $total, $cart);
        return $total;
    }

    /**
     * Calculate the total cost of this cart
     */
    function total()
    {
        return self::subTotal() + self::getFreight();
    }

    /**
     * Set the discount code to use for this cart
     */
    function applyDiscountCode($code)
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
        }

        /* Add discount details to the cart */
        $cart->discount = array();
        $cart->discount['code']       = $discount['discountcode'];
        $cart->discount['percent']    = $discount['discountpercent'];
        $cart->discount['fixed']      = $discount['discountfixed'];
        $cart->discount['minorder']   = $discount['minorder'];
        $cart->discount['products']   = array();
        $cart->discount['exclusions'] = array();

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

    /**
     * Generates a new unique token
     */
    function newToken()
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
        $action   = Util::getFormData('action',      '');
        $id       = Util::getFormData('id',       false);
        $discount = Jojo::getFormData('discount', false);

        /* Get the cart array */
        $cart = self::getCart();
        $smarty->assign('token',  $cart->token);
        $smarty->assign('status', $cart->cartstatus);
        $cart->order['subtotal'] = self::subTotal();

        /* calculate freight */
        $cart->order['freight'] = self::getFreight();

        /* calculate total */
        $cart->order['amount'] = $cart->order['subtotal'] + $cart->order['freight'];

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
            }
            $smarty->assign('id', $savedcart->id);
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
    function foot()
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
