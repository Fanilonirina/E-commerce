<?php

namespace App\Service;


class Cart
{
    public $items = null;
    public $totalQty = 0;
    public $totalPrice = 0;

    public function __construct($oldCart)
    {
        if ($oldCart) {
            $this->items = $oldCart->items;
            $this->totalQty = $oldCart->totalQty;
            $this->totalPrice = $oldCart->totalPrice;
        }
    }


    public function add($item, $product_id)
    {

        $storedItem = [
            'qty' => 0,
            'product_id' => 0,
            'designation' => $item->getDesignation(),
            'price' => $item->getPrix(),
            'image' => $item->getImage(),
            'item' => $item
        ];

        if ($this->items) {
            if (array_key_exists($product_id, $this->items)) {
                $storedItem = $this->items[$product_id];
            }
        }

        $storedItem['qty']++;
        $storedItem['product_id'] = $product_id;
        $storedItem['designation'] = $item->getDesignation();
        $storedItem['price'] = $item->getPrix();
        $storedItem['image'] = $item->getImage();
        $this->totalQty++;
        $this->totalPrice += $item->getPrix();
        $this->items[$product_id] = $storedItem;
    }

    public function updateQty($id, $qty)
    {
        $this->totalQty -= $this->items[$id]['qty'];
        $this->totalPrice -= $this->items[$id]['price'] * $this->items[$id]['qty'];
        $this->items[$id]['qty'] = $qty;
        $this->totalQty += $qty;
        $this->totalPrice += $this->items[$id]['price'] * $qty;
    }

    public function removeItem($id)
    {
        $this->totalQty -= $this->items[$id]['qty'];
        $this->totalPrice -= $this->items[$id]['price'] * $this->items[$id]['qty'];
        unset($this->items[$id]);
    }
}
