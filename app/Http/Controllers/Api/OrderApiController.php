<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    public function order(Request $request)
    {
        // store request values
        $product_id = $request->product_id;
        $quantity = $request->quantity;

        // find the product first
        $product = Product::findOrFail($product_id);

        //check stock availability
        if($product && $product->available_stock < $request->quantity)
        {
            return response()->json([
                'message' => 'Failed to order this product due to unavailability of the stock.'
            ], 400);
        }

        // deduct the given quantity on the available stock
        $product->available_stock = $product->available_stock - $request->quantity;
        $product->save();

        // placing the order
        Order::create([
            'product_id' => $product_id,
            'quantity' => $quantity,
            'user_id' => Auth::user()->id // who placed the order
        ]);

        return response()->json([
            'message' => 'You have successfully ordered this product.'
        ], 201);
    }
}
