<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVendor;
use App\Models\Site;
use App\Models\TaxType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;


class ProductController extends Controller
{
    private $object;

    public function __construct()
    {
        $this->middleware('admin_has_permission:' . config('constants.role_modules.list_products.value'))->only(['index']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.create_products.value'))->only(['create', 'store']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.edit_products.value'))->only(['edit', 'update']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.delete_products.value'))->only(['destroy']);
        $this->object = new BaseController();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $products = Product::query()->with(['category', 'taxType']);
    
        if (auth()->user()->organization_id) {
            $products->where('organization_id', auth()->user()->organization_id);
        }
    
        if ($request->search) {
            $products->where('name', 'LIKE', "%{$request->search}%");
        }
    
        if ($request->category_id) {
            $products->where('category_id', $request->category_id);
        }
    
        $products = $products->latest()->get();
    
        if ($request->wantsJson()) {
            return ProductResource::collection($products);
        }
    
        return view('products.index', compact('products'));
    }
    
    public function categoryProducts(Request $request)
    {
        try {
            $products = Product::query();

            if (auth()->user()->organization_id) {
                $products->where('organization_id', auth()->user()->organization_id);
            }

            if ($request->search) {
                $searchTerm = strtolower($request->search);

                $products->where(function ($query) use ($searchTerm) {
                    $query->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%'])->orWhereRaw('LOWER(barcode) LIKE ?', ['%' . $searchTerm . '%']);
                });
            }

            if ($request->category_id) {
                $products->where('category_id', $request->category_id);
            }

            $products = $products->latest()->get();

            return response()->json(['data' => $products], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function create()
    {
        $categoriesQuery = Category::where('status', 1);
        $taxTypesQuery = TaxType::query();
        $productVendorsQuery = ProductVendor::query();
        if (auth()->user()->organization_id) {
            $categoriesQuery->where('organization_id', auth()->user()->organization_id);
            $taxTypesQuery->where('organization_id', auth()->user()->organization_id);
            $productVendorsQuery->where('organization_id', auth()->user()->organization_id);
        }
        $categories = $categoriesQuery->get();
        $taxTypes = $taxTypesQuery->get();
        $productVendors = $productVendorsQuery->get();
        return view('products.create', compact('categories', 'taxTypes', 'productVendors'));
    }

    /**
     * @param ProductStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductStoreRequest $request)
    {
        $filename = ''; 

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');

            $image = app('image')->resize($imageFile);

            $filename = time() . '_' . $imageFile->getClientOriginalName();
            $path = public_path('storage/products/' . $filename);
            app('image')->save($image, $path);

            
        }

   
        $quantity = $request->quantity === '*' ? -1 : $request->quantity;

        $product = Product::create([
            'category_id' => $request->category_id ?? 0,
            'tax_type_id' => $request->tax_type_id ?? 0,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $filename,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $quantity,
            'discount_type' => $request->discount_type ?? '',
            'discount' => $request->discount ?? 0,
            'status' => $request->status,
            'product_vendor_id' => $request->product_vendor_id ?? null,
            'cost' => $request->cost,
        ]);

        if (!$product) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating product.');
        }
        return redirect()->route('products.index')->with('success', 'Success, New product has been added successfully!');
    }

    /**
     * @param Product $product
     * @return void
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * @param Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View|void
     */
    public function edit(Product $product)
    {
        if (auth()->user()->organization_id == $product->organization_id || auth()->user()->admin_role_id == 1) {
            $categoriesQuery = Category::where('status', 1);
            $taxTypesQuery = TaxType::query();
            $productVendorsQuery = ProductVendor::query();
            if (auth()->user()->organization_id) {
                $categoriesQuery->where('organization_id', auth()->user()->organization_id);
                $taxTypesQuery->where('organization_id', auth()->user()->organization_id);
                $productVendorsQuery->where('organization_id', auth()->user()->organization_id);
            }
            $categories = $categoriesQuery->get();
            $taxTypes = $taxTypesQuery->get();
            $productVendors = $productVendorsQuery->get();
            return view('products.edit', compact('product', 'categories', 'taxTypes', 'productVendors'));
        }
        abort(403);
    }

    /**
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $request->id,
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer',
            'status' => 'required|boolean',
            'product_vendor_id' => 'nullable|exists:product_vendors,id',
            'discount_type' => 'nullable|in:fixed_amount,percentage',
            'discount' => 'nullable|numeric|min:0',
        ]);

        // Check if the request has a file
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');

            $image = app('image')->resize($imageFile);

            $filename = time() . '_' . $imageFile->getClientOriginalName();
            $path = public_path('storage/products/' . $filename);

            app('image')->save($image, $path);

            $product->image = $filename;
            $product->save();

        }

        $product->fill($request->only(['name', 'category_id', 'tax_type_id', 'description', 'barcode', 'cost', 'price', 'quantity', 'status', 'type', 'discount_type', 'discount', 'product_vendor_id']));

        if (!$product->save()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Sorry, something went wrong while updating the product.',
                ],
                500,
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Success! Product has been updated.',
            'product' => $product,
        ]);
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete($product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function toggleSuggestedAddon(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'suggested_addon' => 'required|boolean',
        ]);

        $product = Product::findOrFail($request->id);
        $product->suggested_addon = $request->suggested_addon;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Suggested Add-on status updated successfully!',
        ]);
    }
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv'
        ]);
    
        $collection = \Maatwebsite\Excel\Facades\Excel::toCollection(null, $request->file('excel_file'))[0];
    
        foreach ($collection->skip(1) as $row) {
            if (!isset($row[3]) || empty($row[3])) continue; // Ensure "Name" is present
    
            $categoryName = isset($row[2]) && trim($row[2]) ? trim($row[2]) : 'Other';
    
            $category = \App\Models\Category::firstOrCreate(
                ['name' => $categoryName],
                [
                    'status' => 1,
                    'organization_id' => auth()->user()->organization_id
                ]
            );
    
            Product::updateOrCreate(
                ['barcode' => $row[5] ?? null], // UPC
                [
                    'category_id' => $category->id,
                    'name' => $row[3] ?? null,                // Name
                    'description' => $row[4] ?? null,         // Description
                    'barcode' => $row[5] ?? null,             // UPC
                    'account' => $row[6] ?? null,             // Account
                    'price' => is_numeric($row[7]) ? $row[7] : 0, // Price
                    'cost' => is_numeric($row[8]) ? $row[8] : 0,  // Cost
                    'markup' => $row[9] ?? null,              // Markup %
                    'profit' => $row[10] ?? null,             // Profit
                    'quantity' => is_numeric($row[11]) ? $row[11] : 0, // Quantity
                    'status' => isset($row[12]) && strtolower($row[12]) == 'true' ? 1 : 0, // IsActive
                    'suggested_addon' => 0,
                    'organization_id' => auth()->user()->organization_id
                ]
            );
        }
    
        return redirect()->back()->with('success', 'Products imported successfully.');
    }
    
    public function toggleStatus(Product $product) 
    {
        $product->status = !$product->status;
        $product->save();

        return response()->json([
            'success' => true,
            'status' => $product->status,
            'message' => 'Product status updated successfully.'

        ]);
    }    
    public function toggleQuickPick(Product $product)
    {
        $product->quick_pick = !$product->quick_pick;
        $product->save();

        return response()->json([
            'success' => true, 
            'quick_pick' => $product->quick_pick
        ]);
    }

    public function toggleShowCategory(Product $product)
    {
        $product->show_in_category = !$product->show_in_category;
        $product->save();

        return response()->json([
            'success' => true,
            'show_in_category' => $product->show_in_category
        ]);
    }
}
