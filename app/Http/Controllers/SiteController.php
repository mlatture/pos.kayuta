<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Site;
use App\Models\SiteHookup;
use App\Models\RigTypes;
use App\Models\SiteClass;
use App\Models\Amenities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_has_permission:' . config('constants.role_modules.list_sites_management.value'))->only(['index']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.create_sites_management.value'))->only(['create', 'store']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.edit_sites_management.value'))->only(['edit', 'update']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.delete_sites_management.value'))->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sites = Site::query();
        if (auth()->user()->organization_id) {
            $sites->where('organization_id', auth()->user()->organization_id);
        }
        if ($request->search) {
            $sites = $sites->where('name', 'LIKE', "%{$request->search}%");
        }
        $sites = $sites->latest()->get();
        return view('sites.index')->with('sites', $sites);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sites.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        $image_path = '';

        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image_path,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'status' => $request->status,
        ]);

        if (!$product) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating product.');
        }
        return redirect()->route('products.index')->with('success', 'Success, New product has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        if (auth()->user()->admin_role_id == 1) {
            $siteHookup = SiteHookup::all();
            $rigTypes = RigTypes::all();
            $siteClass = SiteClass::all();
            $amenities = Amenities::all();

            return view('sites.edit')->with([
                'site' => $site,
                'siteHookup' => $siteHookup,
                'rigTypes' => $rigTypes,
                'siteClass' => $siteClass,
                'amenities' => $amenities,
            ]);
        }
    }

    public function update(Request $request, Site $site)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'virtual_link' => 'nullable|url',
                'hookup' => 'nullable|array',
                'availableonline' => 'nullable|boolean',
                'available' => 'nullable|boolean',
                'seasonal' => 'nullable|boolean',
                'maxlength' => 'nullable|integer',
                'minlength' => 'nullable|integer',
                'rigtypes' => 'nullable|array',
                'siteclass' => 'nullable|array',
                'coordinates' => 'nullable|string',
                'attributes' => 'nullable|string',
                'amenities' => 'nullable|array',
                'ratetier' => 'nullable|string',
                'tax' => 'nullable|string',
                'minimumstay' => 'nullable|integer',
                'sitesection' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $site->name = $validatedData['name'];
            $site->description = $validatedData['description'] ?? null;
            $site->virtual_link = $validatedData['virtual_link'] ?? null;
            $site->hookup = $validatedData['hookup'] ?? null;
            $site->availableonline = $validatedData['availableonline'] ?? 0;
            $site->available = $validatedData['available'] ?? 0;
            $site->seasonal = $validatedData['seasonal'] ?? 0;
            $site->maxlength = $validatedData['maxlength'] ?? null;
            $site->minlength = $validatedData['minlength'] ?? null;
            $site->rigtypes = isset($validatedData['rigtypes']) ? json_encode($validatedData['rigtypes']) : null;
            $site->siteclass = isset($validatedData['siteclass']) ? implode(',', $validatedData['siteclass']) : null;
            $site->coordinates = $validatedData['coordinates'] ?? null;
            $site->attributes = $validatedData['attributes'] ?? null;
            $site->amenities = isset($validatedData['amenities']) ? json_encode($validatedData['amenities']) : null;
            $site->ratetier = $validatedData['ratetier'] ?? null;
            $site->tax = $validatedData['tax'] ?? null;
            $site->minimumstay = $validatedData['minimumstay'] ?? null;
            $site->sitesection = $validatedData['sitesection'] ?? null;

            if ($request->hasFile('image')) {
                if ($site->image) {
                    $oldImagePath = public_path('storage/sites/' . basename($site->image));
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $filename = Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();

                $imagePath = $request->file('image')->move(public_path('storage/sites'), $filename);

                $site->image = 'storage/sites/' . $filename;
            }

            // Save the site model
            if (!$site->save()) {
                return redirect()->back()->with('error', 'Sorry, something went wrong while updating the site.');
            }

            return redirect()->route('sites.index')->with('success', 'Success, site has been updated.');
        } catch (Exception $e) {
            Log::error('Update Error:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Sorry, something went wrong while updating the site.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function view($id)
    {
        $site = Site::find($id);

        return view('sites.view')->with('site', $site);
    }

    public function addImage($id)
    {
        $site = Site::find($id);

        return view('sites.add-image')->with('site', $site);
    }
    public function uploadImages(Request $request, $id)
    {
        $site = Site::findOrFail($id);
        $uploadedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

                    $image = app('image')->resize($file);

                    $path = public_path('storage/sites/' . $filename);

                    app('image')->save($image, $path);

                    $uploadedImages[] = $filename;
                }
            }
        }

        $existing = $site->images;

        if (is_string($existing)) {
            $existing = json_decode($existing, true);
        }
        $existing = is_array($existing) ? $existing : [];

        $allImages = array_merge($existing, $uploadedImages);
        $site->images = json_encode($allImages);
        $site->save();
        $site->refresh();

        return redirect()->back()->with('success', 'Images uploaded successfully!');
    }

    public function deleteImage($siteId, $filename)
    {
        $site = Site::findOrFail($siteId);
        $images = is_array($site->images) ? $site->images : json_decode($site->images, true);

        $imagePath = public_path('shared_storage/sites/' . $filename);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $images = array_filter($images, fn($img) => $img !== $filename);
        $site->images = json_encode(array_values($images));
        $site->save();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        $sites = Site::where('siteid', 'LIKE', "%{$term}%")
            ->orWhere('sitename', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get();

        $result = $sites->map(function ($site) {
            return [
                'label' => "{$site->siteid} - {$site->sitename}",
                'value' => $site->siteid,
            ];
        });

        return response()->json($result);
    }
}
