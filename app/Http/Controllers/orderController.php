<?php

namespace App\Http\Controllers;

use App\Models\menu;
use App\Models\order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class orderController extends Controller
{
    public function index()
    {
        $orders = order::with(['menu', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $menus = menu::where('stock', '>', 0)->orderBy('name')->get();
        return view('orders.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $menu = menu::findOrFail($validated['menu_id']);

        if ($menu->stock < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock. Available: ' . $menu->stock . ' kg')
                ->withInput();
        }

        $unitPrice = $menu->price_per_kilo;
        $totalPrice = $unitPrice * $validated['quantity'];

        $order = order::create([
            'customer_name' => $validated['customer_name'],
            'menu_id' => $validated['menu_id'],
            'user_id' => Auth::id(),
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'paid_amount' => 0,
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $menu->reduceStock($validated['quantity']);

        return redirect()->route('orders.show', $order)
            ->with('success', 'order created successfully.');
    }

    public function show(order $order)
    {
        $order->load(['menu', 'user', 'payments']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, order $order)
    {
        $validated = $request->validate([
            'order_status' => ['required', Rule::in(['pending', 'processing', 'completed'])],
        ]);

        $order->order_status = $validated['order_status'];
        $order->save();

        return back()->with('success', 'order status updated successfully.');
    }

    public function destroy(order $order)
    {
        if ($order->order_status !== 'completed') {
            $order->menu->increaseStock($order->quantity);
        }

        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'order deleted successfully.');
    }
}