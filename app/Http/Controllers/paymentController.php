<?php

namespace App\Http\Controllers;

use App\Models\order;
use App\Models\payment;
use Illuminate\Http\Request;

class paymentController extends Controller
{
    public function index(Request $request)
    {
        $query = payment::with('order');

        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);
        $orders = order::select('id', 'customer_name')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payments.index', compact('payments', 'orders'));
    }

    public function create(order $order)
    {
        return view('payments.create', compact('order'));
    }

    public function store(Request $request, order $order)
    {
        $validated = $request->validate([
            'amount_given' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $remainingBalance = $order->remaining_balance;

        if ($validated['amount_given'] <= 0) {
            return back()->with('error', 'Amount must be greater than zero.');
        }

        $change = 0;
        $amountToApply = $validated['amount_given'];

        if ($amountToApply > $remainingBalance) {
            $change = $amountToApply - $remainingBalance;
            $amountToApply = $remainingBalance;
        }

        payment::create([
            'order_id' => $order->id,
            'amount_paid' => $amountToApply,
            'change_given' => $change,
            'notes' => $validated['notes'] ?? null,
        ]);

        $order->paid_amount += $amountToApply;
        $order->save();
        $order->updatepaymentStatus();

        $message = "payment of ₱" . number_format($amountToApply, 2) . " processed successfully.";
        if ($change > 0) {
            $message .= " Change: ₱" . number_format($change, 2);
        }
        if ($order->payment_status === 'paid') {
            $message .= " order is now fully paid.";
        }

        return redirect()->route('payments.index')
            ->with('success', $message);
    }
}