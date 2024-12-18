<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->paginate(20);

        if ($orders) {
            foreach ($orders as $order) {
                foreach ($order->items as $order_items) {
                    $product = Product::where('id', $order_items->product_id)->pluck('name');
                    $order_items->product_name = $product[0];
                }
            }
            return response()->json($orders, 200);
        } else {
            return response()->json('there is no orders');
        }
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order, 200);
    }


    public function store(Request $request)
    {
        try {
            // Check if user has a location
            $location = Location::where('user_id', Auth::id())->first();
            if (!$location) {
                return response()->json(['error' => 'No location found for the user'], 400);
            }

            // Validate request
            $request->validate([
                'order_items'      => 'required|array',
                'order_items.*.product_id' => 'required|exists:products,id',
                'order_items.*.price'      => 'required|numeric',
                'order_items.*.quantity'   => 'required|integer|min:1',
                'total_price'      => 'required|numeric|min:0',
                'date_of_delivery' => 'required|date',
            ]);

            // Create new order
            $order = new Order();
            $order->user_id = Auth::id();
            $order->location_id = $location->id;
            $order->total_price = $request->total_price;
            $order->date_of_delivery = $request->date_of_delivery;
            $order->save();

            // Save order items and update product stock
            foreach ($request->order_items as $order_item) {
                $item = new OrderItem();
                $item->order_id = $order->id;
                $item->product_id = $order_item['product_id'];
                $item->price = $order_item['price'];
                $item->quantity = $order_item['quantity'];
                $item->save();

                // Update product stock
                $product = Product::findOrFail($order_item['product_id']);
                $product->amount -= $order_item['quantity'];
                if ($product->amount < 0) {
                    return response()->json(['error' => 'Insufficient stock for product: ' . $product->id], 400);
                }
                $product->save();
            }

            return response()->json(['message' => 'Order created successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the order.'], 500);
        }
    }

    public function get_order_items($id){
        $order_items = OrderItem::where('order_id',$id)->get();
        if($order_items){
        foreach($order_items as $order_item){
            $product = Product::where('id',$order_item->product_id)->pluck('name');
            $order_item->product_name = $product['0'];
        }
        return response()->json($order_items,200);

    }else return response()->json('no items Found');


    }
    public function get_user_orders($id)
    {
        try {
            // Fetch orders with related items and products
            $orders = Order::where('user_id', $id)
                ->with(['items.product' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // Check if orders exist
            if ($orders->isEmpty()) {
                return response()->json(['message' => 'No orders found'], 404);
            }

            // Append product names to each order item
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    $item->product_name = $item->product->name ?? 'Unknown Product';
                }
            }

            return response()->json($orders, 200);

        } catch (\Exception $e) {
            Log::error('Get User Orders Error: ', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while fetching user orders.'], 500);
        }
    }

    public function change_order_status($id,Request $request){
        $order = Order::find($id);
        if($order){
            $order->update([
                'status' => $request->status
            ]);
            return response()->json(['message' => 'order status updated successfully'], 200);

        }
        else return response()->json('no orders Found');

    }
}
