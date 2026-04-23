<?php

namespace App\Http\Controllers;

use App\Models\menu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class menuController extends Controller
{
    public function index()
    {
        $menus = menu::orderBy('name')->paginate(10);
        return view('menu.menu', compact('menus'));
    }

    public function create()
    {
        return view('menu.createmenu');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menus',
            'category' => 'required|string|max:100',
            'price_per_kilo' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        menu::create($validated);

        return redirect()->route('menus.index')
            ->with('success', 'Rice product added successfully.');
    }

    public function edit(menu $menu)
    {
        return view('menu.editmenu', compact('menu'));
    }

    public function update(Request $request, menu $menu)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('menus')->ignore($menu->id)],
            'category' => 'required|string|max:100',
            'price_per_kilo' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $menu->update($validated);

        return redirect()->route('menus.index')
            ->with('success', 'Rice product updated successfully.');
    }

    public function destroy(menu $menu)
    {
        if ($menu->orders()->exists()) {
            return back()->with('error', 'Cannot delete product with existing orders.');
        }

        $menu->delete();

        return redirect()->route('menus.index')
            ->with('success', 'Rice product deleted successfully.');
    }
}