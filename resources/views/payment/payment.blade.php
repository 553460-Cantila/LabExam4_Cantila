<p><a href="{{ route('dashboard') }}">← Back to Dashboard</a></p>

<h2>Payment Management</h2>

<div style="border:1px solid #ccc; padding:15px; margin-bottom:20px;">
    <h3>Process a New Payment</h3>
    <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
        @csrf
        <p>
            <label>Select Order:</label><br>
            <select name="order_id" id="order_id" required>
                <option value="">-- Choose an order --</option>
                @foreach($allOrders as $order)
                    <option value="{{ $order->id }}" data-total="{{ $order->total_price }}" data-paid="{{ $order->paid_amount }}">
                        Order #{{ $order->id }} – {{ $order->customer_name }}
                        (Total: ₱{{ number_format($order->total_price,2) }},
                         Paid: ₱{{ number_format($order->paid_amount,2) }},
                         Remaining: ₱{{ number_format($order->remaining_balance,2) }})
                    </option>
                @endforeach
            </select>
        </p>
        <div id="orderSummary" style="background:#f9f9f9; padding:10px; margin:10px 0; display:none;"></div>
        <p>
            <label>Amount Given (₱):</label><br>
            <input type="number" name="amount_given" id="amount_given" step="0.01" required>
        </p>
        <p>
            <strong>Change to return: </strong> <span id="changeDisplay">₱0.00</span>
        </p>
        <button type="submit">Process Payment</button>
    </form>
</div>

<h3>Payment Transaction Logs</h3>
<form method="GET" action="{{ route('payments.index') }}" style="margin-bottom:10px;">
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <div>
            <label>Filter by Order:</label>
            <select name="order_id">
                <option value="">All Orders</option>
                @foreach($allOrders as $order)
                    <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>
                        Order #{{ $order->id }} – {{ $order->customer_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Filter by Customer Name:</label>
            <input type="text" name="customer_name" value="{{ request('customer_name') }}" placeholder="e.g. Sir Janjan">
        </div>
        <div>
            <button type="submit">Filter</button>
            <a href="{{ route('payments.index') }}">Reset</a>
        </div>
    </div>
</form>

<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width:100%;">
    <thead>
        <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Amount Paid (₱)</th>
            <th>Change (₱)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
        <tr>
            <td><a href="{{ route('orders.show', $payment->order) }}">#{{ $payment->order_id }}</a></td>
            <td>{{ $payment->order->customer_name }}</td>
            <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
            <td>₱{{ number_format($payment->change_given, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $payments->links() }}

<script>
    const orderSelect = document.getElementById('order_id');
    const amountGiven = document.getElementById('amount_given');
    const changeSpan = document.getElementById('changeDisplay');
    const orderSummaryDiv = document.getElementById('orderSummary');

    function updateOrderSummary() {
        const selected = orderSelect.options[orderSelect.selectedIndex];
        if (selected.value) {
            const total = parseFloat(selected.dataset.total) || 0;
            const paid = parseFloat(selected.dataset.paid) || 0;
            const remaining = total - paid;
            orderSummaryDiv.innerHTML = `<strong>Order Summary:</strong> Total: ₱${total.toFixed(2)} | Already Paid: ₱${paid.toFixed(2)} | Remaining: ₱${remaining.toFixed(2)}`;
            orderSummaryDiv.style.display = 'block';
        } else {
            orderSummaryDiv.style.display = 'none';
        }
        calculateChange();
    }

    function calculateChange() {
        const selected = orderSelect.options[orderSelect.selectedIndex];
        if (!selected.value) {
            changeSpan.innerText = '₱0.00';
            return;
        }
        const total = parseFloat(selected.dataset.total) || 0;
        const paid = parseFloat(selected.dataset.paid) || 0;
        const remaining = total - paid;
        const given = parseFloat(amountGiven.value) || 0;
        let change = 0;
        if (given > remaining) {
            change = given - remaining;
        }
        changeSpan.innerText = '₱' + change.toFixed(2);
    }

    orderSelect.addEventListener('change', updateOrderSummary);
    amountGiven.addEventListener('input', calculateChange);
</script>