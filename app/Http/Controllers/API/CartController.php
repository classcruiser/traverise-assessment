<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartItemResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $key
     * @return \Illuminate\Http\Response
     */
    public function show($key)
    {
        $cart = $this->cartService->findByKey($key);

        return new CartResource($cart);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $key
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $key)
    {
        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*.type' => 'required|in:room,addon,transfer,class,class_addon',
            'items.*.name' => 'required',
            'items.*.item_id' => 'required|integer',
            'items.*.amount' => 'required|integer',
            'items.*.duration' => 'sometimes|nullable|integer',
            'items.*.price' => 'required|numeric|decimal:0,2|max:99999999.99',
            'items.*.notes' => 'sometimes|nullable',
            'items.*.options' => 'sometimes|nullable',
        ], [
            'items.*.price.decimal' => 'The price must be with 0 or 2 decimals'
        ]);

        $cart = $this->cartService->findByKey($key);
        $this->cartService->addItems($validatedData['items'], $cart);

        return new CartResource($cart);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $key
     * @param  mixed  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $key, $id)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:room,addon,transfer,class,class_addon',
            'name' => 'required',
            'item_id' => 'required|integer',
            'amount' => 'required|integer',
            'duration' => 'sometimes|nullable|integer',
            'price' => 'required|numeric|decimal:0,2|max:99999999.99',
            'notes' => 'sometimes|nullable',
            'options' => 'sometimes|nullable',
        ], [
            'price.decimal' => 'The price must be with 0 or 2 decimals'
        ]);

        $cart = $this->cartService->findByKey($key, false);
        $item = $this->cartService->updateItem($validatedData, $id, $cart);

        return new CartItemResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $key): JsonResponse
    {
        $validatedData = $request->validate(['items' => 'required|array']);

        $cart = $this->cartService->findByKey($key, false);
        $this->cartService->removeItems($validatedData['items'], $cart);

        return response()->json(null, 204);
    }
}
