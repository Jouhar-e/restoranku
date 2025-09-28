<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $tableNumber = $request->query('meja');
        if ($tableNumber) {
            Session::put('tableNumber', $tableNumber);
        }

        $items = Item::where('is_active', 1)->orderBy('name', 'asc')->get();

        return view('customer.menu', compact('items', 'tableNumber'));
    }

    // Menu Cart
    public function cart()
    {
        $cart = Session::get('cart');

        return view('customer.cart', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        $menuId = $request->id;
        $menu = Item::find($menuId);

        // dd(session('cart'));

        if (! $menu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan!',
            ]);
        }

        $cart = Session::get('cart');

        if (isset($cart[$menuId])) {
            $cart[$menuId]['qty'] += 1;
        } else {
            $cart[$menuId] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'img' => $menu->img,
                'qty' => 1,
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil ditambahkan ke keranjang',
            'cart' => $cart,
        ]);
    }

    public function updateCart(Request $request)
    {
        $itemId = $request->id;
        $newQty = $request->qty;

        if ($newQty <= 0) {
            return response()->json(['success' => false]);
        }

        $cart = Session::get('cart');
        // dd($request->all());
        if (isset($cart[$itemId])) {
            $cart[$itemId]['qty'] = $newQty;
            Session::put('cart', $cart);
            Session::flash('success', 'Jumlah item berhasil diperbarui');
            // $qty = $cart[$itemId]['qty'];

            // return response()->json(['success' => true, 'qty' => $qty]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function removeCart(Request $request)
    {
        $itemId = $request->id;
        $cart = Session::get('cart');

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            Session::put('cart', $cart);

            Session::flash('success', 'Item berhasil dihapus dari keranjang');

            return response()->json(['success' => true]);
        }
    }

    public function clearCart()
    {
        Session::forget('cart');

        return redirect()->route('menu')->with('success', 'Keranjang berhasil dikosongkan');
    }

    // Menu CheckOut
    public function checkout()
    {
        $cart = Session::get('cart');
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang masih kosong');
        }

        $tableNumber = Session::get('tableNumber');

        return view('customer.checkout', compact('cart', 'tableNumber'));
    }

    public function storeOrder(Request $request)
    {
        $cart = Session::get('cart');
        $tableNumber = Session::get('tableNumber');

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang masih kosong');
        }

        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return redirect()->route('checkout')->withErrors($validator);
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['qty'] * $item['price'];

            $itemDetails[] = [
                'id' => $item['id'],
                'price' => (int) ($item['price'] + ($item['price'] * 0.1)),
                'quantity' => $item['qty'],
                'name' => substr($item['name'], 0, 50),
            ];
        }

        $user = User::firstOrCreate([
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'role_id' => 4,
        ]);

        $order = Order::create([
            'order_code' => 'ORD-'.$tableNumber.'-'.time(),
            'user_id' => $user->id,
            'subtotal' => $totalAmount,
            'tax' => 0.1 * $totalAmount,
            'grand_total' => $totalAmount + (0.1 * $totalAmount),
            'status' => 'pending',
            'table_number' => $tableNumber,
            'payment_method' => $request->payment,
            'notes' => $request->text,
        ]);

        foreach ($cart as $itemId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item['id'],
                'quantity' => $item['qty'],
                'price' => $item['price'] * $item['qty'],
                'tax' => 0.1 * $item['price'] * $item['qty'],
                'total_price' => ($item['price'] * $item['qty']) + (0.1 * $item['price'] * $item['qty']),
            ]);
        }

        Session::forget('cart');

        if ($request->payment == 'tunai') {
            return redirect()->route('checkout.success', ['orderId' => $order->order_code])->with('success', 'Pesanan berhasil dibuat');
        } else {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_code,
                    'gross_amount' => (int)$order->grand_total,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => $user->fullname ?? 'Guest',
                    'phone' => $user->phone,
                ],
                'payment_type' => 'qris',
            ];

            try {
                $sanpToken = \Midtrans\Snap::getSnapToken($params);
                return response()->json([
                    'status'=>'success',
                    'snap_token'=>$sanpToken,
                    'order_code'=>$order->order_code,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status'=>'error',
                    'message'=>'Gagal membuat pesanan. Silakan coba lagi',
                ]);
            }
            
        }

    }

    public function checkoutSuccess($orderId)
    {
        $order = Order::where('order_code', $orderId)->first();

        if (! $order) {
            return redirect()->route('menu')->with('error', 'Pesanan tidak ditemukan');
        }

        $orderItems = OrderItem::where('order_id', $order->id)->get();

        if ($order->payment_method == 'qris') {
            $order->status = 'settlement';
            $order->save();
        }

        return view('customer.success', compact('order', 'orderItems'));

    }
}
