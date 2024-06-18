<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;

class CartService
{
    public function findByKey(string $key, bool $loadItem = true): Cart
    {
        $cart = Cart::firstOrCreate(['cart_key' => $key]);

        if ($loadItem) {
            $cart->loadMissing('items');
        }

        return $cart;
    }

    public function addItems($payload, Cart &$model): void
    {
        $model->items()->createMany($payload);
        $model->refresh();
    }

    public function removeItems($payload, Cart $model): void
    {
        CartItem::where('cart_id', $model->id)->whereIn('id', $payload)->delete();
    }

    public function updateItem($payload, int $id, Cart $model): ?CartItem
    {
        $item = CartItem::where('cart_id', $model->id)->where('id', $id)->first();

        if ($item) {
            $item->fill($payload);
            $item->save();
        }

        return $item;
    }
}
