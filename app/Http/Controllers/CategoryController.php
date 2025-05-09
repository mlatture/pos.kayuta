<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $object;

    public function __construct()
    {
        $this->middleware('admin_has_permission:'.config('constants.role_modules.list_categories.value'))->only(['index']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.create_categories.value'))->only(['create','store']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.edit_categories.value'))->only(['edit','update']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.delete_categories.value'))->only(['destroy']);
        $this->object   =   new BaseController;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::query();
        if ($request->search) {
            $categories = $categories->where('name', 'LIKE', "%{$request->search}%");
        }
        if(auth()->user()->organization_id) {
            $categories->where('organization_id',auth()->user()->organization_id);
        }
        $categories = $categories->latest()->get();
        return view('categories.index')->with('categories', $categories);
    }

    public function getAllCategories()
    {
        $categories = Category::latest()->get();
        return $this->object->respond($categories, [], true, 'success!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request)
    {
        try {
            $category   =   Category::create([
                'organization_id' => auth()->user()->organization_id,
                'name'  =>  $request->name,
                'status' =>  $request->status
            ]);

            if (!$category) {
                return redirect()->back()->with('error', 'Sorry, Something went wrong while creating category.');
            }
            return redirect()->route('categories.index')->with('success', 'Success, New category has been added successfully!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        if(auth()->user()->organization_id == $category->organization_id || auth()->user()->admin_role_id == 1) {
            return view('categories.edit')->with('category', $category);
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        try {
            $category       =   $category->update([
                'name'      =>  $request->name,
                'status'    =>  $request->status
            ]);

            if (!$category) {
                return redirect()->back()->with('error', 'Sorry, Something went wrong while updating category.');
            }
            return redirect()->route('categories.index')->with('success', 'Success, category has been updated successfully!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true
        ]);
    }
    
    public function toggleShowInPOS(Category $category)
    {
        
        $category->show_in_pos = !$category->show_in_pos;
        $category->save();

        return response()->json([
            'success' => true,
            'show_in_pos' => $category->show_in_pos
        ]);
    }
}
