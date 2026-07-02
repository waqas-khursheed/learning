<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;

// Presentation layer: request le kar, validated data Model (business+data
// layer) ko de deta hai. Controller sirf "traffic cop" hai — koi heavy
// business rule yahan nahi likhi jaati.
class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $order = Order::createFromValidated($request->validated());

        return redirect()->route('orders.show', $order);
    }

    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }
}
