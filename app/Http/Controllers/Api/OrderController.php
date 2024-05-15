<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Save order
    public function saveOrder(Request $request)
    {
        // Log raw request data
        Log::info('Raw Request Data:', [$request->getContent()]);

        // Decode JSON data
        $data = json_decode($request->getContent(), true);

        // Jika $data adalah string, maka perlu decode lagi
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        Log::info('Decoded Request Data:', $data);

        // Validate data
        $validated = Validator::make($data, [
            'payment_amount' => 'required|numeric',
            'sub_total' => 'required|numeric',
            'tax' => 'required|numeric',
            'discount' => 'required|numeric',
            'service_charge' => 'required|numeric',
            'total' => 'required|numeric',
            'payment_method' => 'required|string',
            'total_item' => 'required|integer',
            'id_kasir' => 'required|integer',
            'nama_kasir' => 'required|string',
            'transaction_time' => 'required',
            'order_items' => 'required|array',
            'order_items.*.id_product' => 'required|integer',
            'order_items.*.quantity' => 'required|integer',
            'order_items.*.price' => 'required|numeric',
            'order_items.*.buyer_name' => 'nullable|string',
            'buyer_name' => 'nullable|string',
        ])->validate();

        // Create order
        $order = Order::create([
            'payment_amount' => $data['payment_amount'],
            'sub_total' => $data['sub_total'],
            'tax' => $data['tax'],
            'discount' => $data['discount'],
            'service_charge' => $data['service_charge'],
            'total' => $data['total'],
            'payment_method' => $data['payment_method'],
            'total_item' => $data['total_item'],
            'id_kasir' => $data['id_kasir'],
            'nama_kasir' => $data['nama_kasir'],
            'transaction_time' => $data['transaction_time'],
            'is_sync' => isset($data['is_sync']) ? $data['is_sync'] : 0,
            'buyer_name' => $data['buyer_name'] ?? '',
        ]);

        // Create order items
        foreach ($data['order_items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id_product'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'buyer_name' => $data['buyer_name'] ?? '',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order
        ], 200);
    }
}
