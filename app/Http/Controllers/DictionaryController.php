<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\DictionaryTable;

class DictionaryController extends Controller
{
    public function index(): Factory|View|Application
    {
        $dictionaries = DictionaryTable::all();
        return view('admin.field_dictionary.index', compact('dictionaries'));
    }

    public function create(): Factory|View|Application
    {
        return view('admin.field_dictionary.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        DictionaryTable::create($request->all());
        return redirect()->route('dictionary.index');
    }

    public function edit($id): Factory|View|Application
    {
        $dictionary = DictionaryTable::findOrFail($id);
        return view('admin.field_dictionary.edit', compact('dictionary'));
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $dictionary = DictionaryTable::findOrFail($id);
        $dictionary->update($request->all());
        return redirect()->route('dictionary.index');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        DictionaryTable::destroy($id);
        return redirect()->route('dictionary.index');
    }
}
