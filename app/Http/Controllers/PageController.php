<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Blogs;
class PageController extends Controller
{
    //

    public function index(Request $request)
    {
        $type = $request->query('type', 'page');
        $blogs = null;
        $pages = null;

        if ($type === 'blog' || $type === 'blogs') {
            $blogs = Blogs::all();
        } else {
            $pages = Page::where('type', $type)->get();
        }

        return view('blogs.index', compact('pages', 'type', 'blogs'));
    }

    public function create(Request $request)
    {
        $type = $request->type;

        return view('blogs.create', compact('type'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type');
        $data = $request->all();

        if ($type === 'blog') {
            $data['image'] = $request->file('thumbnail');
            Blogs::create($data);

            return redirect()->back()->with('success', 'Blog created!');
        }
    }
}
