<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Product::query();

            return DataTables::of($query)
                ->addColumn('action', function ($product) {
                    return '
                    <button type="button" class="btn btn-warning btn-sm edit-product mr-2" data-toggle="modal" data-target="#editProductModal' . $product->id . '" data-product-id="' . $product->id . '" data-backdrop="false">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <form id="delete-form-' . $product->id . '" action="' . route('products.destroy', $product->id) . '" method="POST" class="d-inline">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm delete-product" onclick="deleteProduct(event, ' . $product->id . ')">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                    <a href="' . route('products.show', $product->id) . '" class="btn btn-primary btn-sm">View</a>
                ';
                })
                ->addColumn('status', function ($product) {
                    return $product->status ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make();
        }

        $products = Product::all();
        $categories = Category::all();
        return view('pages.product.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'nullable',
            'image' => 'required|image',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle image upload
        $imagePath = $request->file('image')->store('public/product_images');
        $imagePath = str_replace('public/', 'storage/', $imagePath); // Mengubah path sesuai dengan kebutuhan

        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image' => $imagePath,
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('pages.product.detail', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'nullable',
            'image' => 'nullable|image',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete existing image file
            Storage::delete(str_replace('storage/', 'public/', $product->image));

            // Upload new image
            $imagePath = $request->file('image')->store('public/product_images');
            $imagePath = str_replace('public/', 'storage/', $imagePath);

            $product->image = $imagePath;
        }

        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Update the product image.
     */
    public function updateImage(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Delete existing image file
        Storage::delete(str_replace('storage/', 'public/', $product->image));

        // Handle image upload
        $imagePath = $request->file('image')->store('public/product_images');
        $imagePath = str_replace('public/', 'storage/', $imagePath);

        // Update product image
        $product->image = $imagePath;
        $product->save();

        return redirect()->route('products.show', $product->id)->with('success', 'Product image updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image file when deleting product
        Storage::delete(str_replace('storage/', 'public/', $product->image));

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
