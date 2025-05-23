<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
class PageController extends Controller
{
    //

    public function index(Request $request)
    {
        $type = $request->query('type', 'page');
        $pages = Page::where('type', $type);
        return view('blogs.index', compact('pages', 'type'));
    }
}
