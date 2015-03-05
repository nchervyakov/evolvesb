<?php
namespace App\Controller;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\Cart as CartModel;
use App\Model\CartItems;
use App\Model\Product;
use App\Page;

/**
 * Class Cart
 * @package App\Controller
 */
class Cart extends Page {

    /**
     * show overview page
     */
    public function action_index() {
        $this->redirect('/cart/view');
    }

    /**
     * Add product to cart
     */
    public function action_add() {
        $ids = [];
        if ($this->request->is_ajax()) {
            $ids = $this->getProductsInCartIds();
        }

        $qty = $this->request->post('qty', 1);
        $productId = $this->request->post('product_id');
        if (!is_numeric($productId) || !is_numeric($qty)) {
            $this->jsonResponse(['error' => 1, 'message' => 'Некорректный идентификатор продукта или количество.']);
            return;
        }

        $productId = (int) $productId;
        $qty = (int) $qty;

        /** @var Product $product */
        $product = $this->pixie->orm->get('Product')->where('productID', $productId)->find();

        if (!$product || !$product->loaded()) {
            throw new NotFoundException();
        }

        if ($qty <= 0 || $qty > $product->max_items_per_order) {
            throw new HttpException('Количество элементов должно быть в интервале от 1 до ' . $product->max_items_per_order);
        }

        $result = $this->pixie->orm->get('CartItems')->addItems($productId, $qty);
        $this->pixie->orm->get('Cart')->forceSetLastStep(CartModel::STEP_OVERVIEW);

        if ($this->request->is_ajax()) {
            $this->jsonResponse([
                'success' => 1,
                'productId' => $productId,
                'newProduct' => !in_array($productId, $ids),
                'product' => $result['product']->getFields([
                    'productID', 'name', 'Price'
                ]),
                'item' => $result['item']->getFields([
                    'id', 'qty', 'price'
                ])
            ]);

        } else {
            $this->redirect('/cart/view');
        }
    }

    /**
     * show overview page
     */
    public function action_view() {
        /** @var \App\Model\Cart $cart */
        $cart = $this->pixie->orm->get('Cart')->getCart();
        /** @var CartItems $items */
        $items = $this->pixie->orm->get('CartItems')->getAllItems();
        $this->view->subview = 'cart/view';
        $this->view->items = $items;
        $this->view->itemQty = $cart->items_qty;
        $this->view->totalPrice = $cart->total_price;
        $this->view->tab = 'overview';
        $this->view->message = $cart->message;
        $this->view->step = $this->pixie->orm->get('Cart')->getStepLabel();//last step
    }

    /**
     * update cart items qty
     */
    public function action_update() {
        $qty = $this->request->post('qty');
        $itemId = $this->request->post('itemId');
        if (!$itemId || !is_numeric($itemId) || !is_numeric($qty)) {
            throw new NotFoundException;
        }

        $qty = (int)$qty;
        $itemId = (int)$itemId;

        /** @var CartItems $item */
        $item = $this->pixie->orm->get('CartItems', $itemId);
        if (!$item->loaded()) {
            throw new NotFoundException;
        }

        if ($qty < 0) {
            throw new HttpException('Количество экземпляров данного продукта должно быть 0 или больше.');
        }

        $this->pixie->orm->get('CartItems')->updateItems($itemId, $qty);
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $item->refresh();
        $this->pixie->orm->get('Cart')->forceSetLastStep(CartModel::STEP_OVERVIEW);

        $res = [
            'items_qty' => $cart->items_qty,
            'total_price' => $cart->total_price,
            'total_price_formatted' => $this->view->getHelper()->formatPrice($cart->total_price),
            'item_price_formatted' => $this->view->getHelper()->formatPrice($item->price * $item->qty),
        ];
        $this->jsonResponse($res);
    }

    /**
     * clean cart
     */
    public function action_empty() {
        /** @var \App\Model\Cart $cart */
        $cart = $this->pixie->orm->get('Cart')->getCart();
        $cart->delete();
        $this->pixie->orm->get('Cart')->getCart();
        $this->execute=false;
    }

    /**
     * set shipping & payment methods
     */
    public function action_setMethods() {
        $this->checkCsrfToken('checkout_step_1', null, !$this->request->is_ajax());
        /** @var \App\Model\Cart $cart */
        $cart = $this->pixie->orm->get('Cart')->getCart();
        //$cart->shipping_method = $this->request->post('shipping_method');
        //$cart->payment_method = $this->request->post('payment_method');
        $cart->message = $this->request->post('message');
        $cart->save();
        $this->execute=false;
        $this->pixie->orm->get('Cart')->updateLastStep(CartModel::STEP_SHIPPING);
    }

    public function action_getSidebarCart()
    {
        $this->view->set_template('common/_sidebar_cart');
        $this->jsonResponse(['html' => $this->view->render()]);
    }
}