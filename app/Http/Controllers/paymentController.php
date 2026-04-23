<?php

namespace App\Http\Controllers;

use App\Models\order;
use App\Models\payment;
use Illuminate\Http\Request;

class paymentController extends Controller
{
    public function index(Request $request)
    {
        $allOrders = order::with('menu')->orderBy('created_at', 'desc')->get();

        $query = payment::with('order');

        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }
        if ($request->filled('customer_name')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->customer_name . '%');
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);

        return view('payment.payment', compact('payments', 'allOrders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount_given' => 'required|numeric|min:0.01',
        ]);

        $order = order::findOrFail($validated['order_id']);
        $remainingBalance = $order->remaining_balance;
        $amountGiven = $validated['amount_given'];
        $change = 0;
        $amountToApply = $amountGiven;
        if ($amountGiven > $remainingBalance) {
            $change = $amountGiven - $remainingBalance;
            $amountToApply = $remainingBalance;
        }

        $payment = payment::create([
            'order_id' => $order->id,
            'amount_paid' => $amountToApply,
            'change_given' => $change,
        ]);

        $order->paid_amount += $amountToApply;
        $order->save();
        $order->updatePaymentStatus();

        return redirect()->route('payments.index')->with('success', 'Payment recorded.');
    }

    public function edit(payment $payment)
    {
        $payment->load('order');
        return view('payment.editpayment', compact('payment'));
    }

    public function update(Request $request, payment $payment)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'change_given' => 'nullable|numeric|min:0',
        ]);

        $oldAmount = $payment->amount_paid;
        $newAmount = $validated['amount_paid'];
        $diff = $newAmount - $oldAmount;

        $order = $payment->order;
        $newPaidAmount = $order->paid_amount + $diff;
        if ($newPaidAmount < 0) {
            return back()->withErrors(['amount_paid' => 'Total paid cannot be negative.']);
        }
        if ($newPaidAmount > $order->total_price) {
            return back()->withErrors(['amount_paid' => 'Total paid cannot exceed order total.']);
        }

        $payment->update([
            'amount_paid' => $newAmount,
            'change_given' => $validated['change_given'] ?? 0,
        ]);

        $order->paid_amount = $newPaidAmount;
        $order->save();
        $order->updatePaymentStatus();

        return redirect()->route('payments.index')->with('success', 'Payment updated.');
    }

    public function destroy(payment $payment)
    {
        $order = $payment->order;
        $order->paid_amount -= $payment->amount_paid;
        if ($order->paid_amount < 0) $order->paid_amount = 0;
        $order->save();
        $order->updatePaymentStatus();

        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }
}