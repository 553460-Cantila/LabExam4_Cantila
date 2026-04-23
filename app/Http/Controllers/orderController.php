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
        $menus = menu::where('stock', '>', 0)->orderBy('name')->get();
        return view('order.order', compact('orders', 'menus'));
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

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(order $order)
    {
        $order->load(['menu', 'user', 'payments']);
        return view('order.show', compact('order'));
    }

    public function update(Request $request, order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $oldQuantity = $order->quantity;
        $newQuantity = $validated['quantity'];

        if ($order->menu_id != $validated['menu_id']) {
            $oldMenu = $order->menu;
            $oldMenu->stock += $oldQuantity;
            $oldMenu->save();

            // Check new product stock
            $newMenu = menu::findOrFail($validated['menu_id']);
            if ($newMenu->stock < $newQuantity) {
                return back()->with('error', 'Insufficient stock for new product. Available: ' . $newMenu->stock . ' kg');
            }
            $newMenu->stock -= $newQuantity;
            $newMenu->save();

            $unitPrice = $newMenu->price_per_kilo;
            $totalPrice = $unitPrice * $newQuantity;
            $order->update([
                'customer_name' => $validated['customer_name'],
                'menu_id' => $validated['menu_id'],
                'quantity' => $newQuantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);
        } else {
            // Same product: adjust stock difference
            $menu = $order->menu;
            $diff = $newQuantity - $oldQuantity;
            if ($diff > 0 && $menu->stock < $diff) {
                return back()->with('error', 'Not enough stock. Available: ' . $menu->stock . ' kg');
            }
            $menu->stock -= $diff;
            $menu->save();

            $unitPrice = $menu->price_per_kilo; // use current price
            $totalPrice = $unitPrice * $newQuantity;
            $order->update([
                'customer_name' => $validated['customer_name'],
                'quantity' => $newQuantity,
                'total_price' => $totalPrice,
                'unit_price' => $unitPrice,
            ]);
        }

        $order->updatePaymentStatus();

        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(order $order)
    {
        if ($order->order_status !== 'completed') {
            $order->menu->increaseStock($order->quantity);
        }
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}