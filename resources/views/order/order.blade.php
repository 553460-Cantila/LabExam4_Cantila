<p><a href="{{ route('dashboard') }}">← Back to Dashboard</a></p>

<h2>Order Management</h2>

<div style="border:1px solid #ccc; padding:15px; margin-bottom:20px;">
    <h3 id="formTitle">Create New Order (POS)</h3>
    <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
        @csrf
        <input type="hidden" name="_method" id="methodField" value="POST">
        <input type="hidden" name="order_id" id="orderId">

        <p>
            <label>Customer Name:</label><br>
            <input type="text" name="customer_name" id="customer_name" required>
        </p>
        <p>
            <label>Rice Product:</label><br>
            <select name="menu_id" id="menu_id" required>
                <option value="">Select product</option>
                @foreach($menus as $menu)
                    <option value="{{ $menu->id }}" data-price="{{ $menu->price_per_kilo }}">
                        {{ $menu->name }} – ₱{{ number_format($menu->price_per_kilo,2) }}/kg (Stock: {{ $menu->stock }} kg)
                    </option>
                @endforeach
            </select>
        </p>
        <p>
            <label>Quantity (kg):</label><br>
            <input type="number" name="quantity" id="quantity" step="1" min="1" required>
        </p>
        <p>
            <strong>Total Cost: </strong> <span id="totalCost">₱0.00</span>
        </p>

        <button type="submit" id="submitBtn">Create Order</button>
        <button type="button" id="cancelBtn" style="display:none;" onclick="resetForm()">Cancel</button>    </form>
</div>

<h3>Order Summary</h3>
<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width:100%;">
    <thead>
        <tr>
            <th>Order #</th><th>Date</th><th>Customer</th><th>Product</th><th>Qty (kg)</th>
            <th>Total</th><th>Paid</th><th>Order Status</th><th>Payment Status</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>#{{ $order->id }}</td>
            <td>{{ $order->created_at->format('Y-m-d') }}</td>
            <td>{{ $order->customer_name }}</td>
            <td>{{ $order->menu->name }}</td>
            <td>{{ $order->quantity }} kg</td>
            <td>₱{{ number_format($order->total_price, 2) }}</td>
            <td>₱{{ number_format($order->paid_amount, 2) }}</td>
            <td>{{ ucfirst($order->order_status) }}</td>
            <td>{{ ucfirst($order->payment_status) }}</td>
            <td>
                <button type="button" onclick="editOrder({{ $order->id }}, '{{ addslashes($order->customer_name) }}', {{ $order->menu_id }}, {{ $order->quantity }})">Edit</button>
                <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete order?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $orders->links() }}

<script>
    const menuSelect = document.getElementById('menu_id');
    const qtyInput = document.getElementById('quantity');
    const totalSpan = document.getElementById('totalCost');

    function updateTotal() {
        let price = 0;
        if (menuSelect.selectedIndex > 0) {
            price = parseFloat(menuSelect.options[menuSelect.selectedIndex].dataset.price) || 0;
        }
        let qty = parseFloat(qtyInput.value) || 0;
        totalSpan.innerText = '₱' + (price * qty).toFixed(2);
    }

    menuSelect.addEventListener('change', updateTotal);
    qtyInput.addEventListener('input', updateTotal);

    function resetForm() {
        document.getElementById('orderForm').action = "{{ route('orders.store') }}";
        document.getElementById('methodField').value = 'POST';
        document.getElementById('orderId').value = '';
        document.getElementById('customer_name').value = '';
        document.getElementById('menu_id').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('formTitle').innerText = 'Create New Order (POS)';
        document.getElementById('submitBtn').innerText = 'Create Order';
        document.getElementById('cancelBtn').style.display = 'none';
        updateTotal();
    }

    function editOrder(id, customerName, menuId, quantity) {
        const form = document.getElementById('orderForm');
        form.action = "/orders/" + id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('orderId').value = id;
        document.getElementById('customer_name').value = customerName;
        document.getElementById('menu_id').value = menuId;
        document.getElementById('quantity').value = quantity;
        document.getElementById('formTitle').innerText = 'Edit Order #' + id;
        document.getElementById('submitBtn').innerText = 'Update Order';
        document.getElementById('cancelBtn').style.display = 'inline-block';
        updateTotal();
    }

    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
        });
    }
</script>