<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();

        return view('admin.order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        return view('admin.order.show', compact('order', 'orderItems'));
    }

    public function statusUpdate($id)
    {
        $order = Order::findOrFail($id);


        if (Auth::user()->role->role_name == 'admin' || Auth::user()->role->role_name == 'cashier') {
            $order->status = 'settlement';
        }else{
            $order->status = 'cooked';
        }

        $order->save();

        return redirect()->route('orders.index')->with('success', 'Pembayaran telah dikonfirmasi');
    }

}
