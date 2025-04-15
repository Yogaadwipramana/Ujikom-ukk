<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('layouts.products.index', compact('products'));
    }

    public function create()
    {
        return view('layouts.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'stock' => 'required|integer|min:0',
        ]);

        $price = str_replace(['Rp', '.', ' '], '', $request->price);

        $imagePath = $request->file('image') ? $request->file('image')->store('products', 'public') : null;

        Product::create([
            'name' => $request->name,
            'price' => $price,
            'image' => $imagePath,
            'stock' => $request->stock,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        return view('layouts.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'stock' => 'required|integer|min:0',
        ]);

        $price = str_replace(['Rp', '.', ' '], '', $request->price);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        } else {
            $imagePath = $product->image;
        }

        $product->update([
            'name' => $request->name,
            'price' => $price,
            'image' => $imagePath,
            'stock' => $request->stock,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }


    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function updateStock(Request $request, $id)
{
    $request->validate([
        'stock' => 'required|integer|min:0',
    ]);

    $product = Product::findOrFail($id);
    $product->update([
        'stock' => $request->stock,
    ]);

    return redirect()->route('products.index')->with('success', 'Stok produk berhasil diperbarui!');
}

public function export()
{
    return Excel::download(new ProductsExport, 'products.xlsx');
}

}


