<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

        if (!$menu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan!'
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
                'qty' => 1
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil ditambahkan ke keranjang',
            'cart' => $cart
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
        // dd($cart);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['qty'] = $newQty;
            Session::put('cart', $cart);
            Session::flash('success', 'Jumlah item berhasil diperbarui');

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
}
