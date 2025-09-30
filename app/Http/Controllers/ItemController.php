<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::orderBy('name', 'asc')->get();

        return view('admin.item.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('cat_name', 'asc')->get();

        return view('admin.item.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'The item name is required.',
            'description.string' => 'The description must be a string.',
            'price.required' => 'The price is required.',
            'category_id.required' => 'The category is required.',
            'img.image' => 'The image must be an image file.',
            'img.max' => 'The image size must not exceed 2MB.',
            'is_active.required' => 'The active status is required.',
            'is_active.boolean' => 'The active status must be true or false.',
        ]);

        // upload image
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img_item_upload'), $imageName);
            $validateData['img'] = $imageName;
        }

        Item::create($validateData);

        return redirect()->route('items.index')->with('success', 'Menu berhasil ditambahkan');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Item::findOrFail($id);
        $categories = Category::orderBy('cat_name', 'asc')->get();

        return view('admin.item.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Item::findOrFail($id);

        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'img' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'The item name is required.',
            'description.string' => 'The description must be a string.',
            'price.required' => 'The price is required.',
            'category_id.required' => 'The category is required.',
            'img.image' => 'The image must be an image file.',
            'img.max' => 'The image size must not exceed 2MB.',
            'is_active.required' => 'The active status is required.',
            'is_active.boolean' => 'The active status must be true or false.',
        ]);

        // upload image jika ada file baru
        if ($request->hasFile('img')) {
            // hapus gambar lama jika ada
            if ($item->img && file_exists(public_path('img_item_upload/'.$item->img))) {
                unlink(public_path('img_item_upload/'.$item->img));
            }
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img_item_upload'), $imageName);
            $validateData['img'] = $imageName;
        }

        $item->update($validateData);

        return redirect()->route('items.index')->with('success', 'Menu berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);

        // Hapus gambar jika ada
        if ($item->img && file_exists(public_path('img_item_upload/'.$item->img))) {
            unlink(public_path('img_item_upload/'.$item->img));
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Menu berhasil dihapus');
    }

    public function updateStatus($id)
    {
        $item = Item::findOrFail($id);
        $item->is_active = ! $item->is_active;
        $item->save();

        return redirect()->route('items.index')->with('success', 'Item status updated successfully.');
    }
}
