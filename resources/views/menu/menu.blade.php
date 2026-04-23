<p>
    <a href="{{ route('dashboard') }}">
        <button type="button">← Back to Dashboard</button>
    </a>
</p>

<h2>Rice Products</h2>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
    <h3 id="formTitle">Add New Rice Product</h3>
    <form method="POST" action="{{ route('menus.store') }}" id="productForm">
        @csrf
        <input type="hidden" name="_method" id="methodField" value="POST">
        <input type="hidden" name="id" id="productId">

        <p>
            <label>Name:</label><br>
            <input type="text" name="name" id="name" required>
        </p>
        <p>
            <label>Category:</label><br>
            <input type="text" name="category" id="category" required>
        </p>
        <p>
            <label>Price per Kilo (₱):</label><br>
            <input type="number" step="0.01" name="price_per_kilo" id="price_per_kilo" required>
        </p>
        <p>
            <label>Stock (kg):</label><br>
            <input type="number" name="stock" id="stock" required>
        </p>
        <button type="submit" id="submitBtn">Save Product</button>
        <button type="button" id="cancelBtn" style="display:none;" onclick="resetForm()">Cancel</button>
    </form>
</div>

<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Price/Kg (₱)</th>
            <th>Stock (kg)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($menus as $menu)
        <tr>
            <td>{{ $menu->name }}</td>
            <td>{{ $menu->category }}</td>
            <td>{{ number_format($menu->price_per_kilo, 2) }}</td>
            <td>{{ $menu->stock }}</td>
            <td>
                <button onclick="editProduct({{ $menu->id }}, '{{ $menu->name }}', '{{ $menu->category }}', {{ $menu->price_per_kilo }}, {{ $menu->stock }})">Edit</button>
                <form action="{{ route('menus.destroy', $menu) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this product?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $menus->links() }}

<script>
    function resetForm() {
        document.getElementById('productForm').action = "{{ route('menus.store') }}";
        document.getElementById('methodField').value = 'POST';
        document.getElementById('productId').value = '';
        document.getElementById('name').value = '';
        document.getElementById('category').value = '';
        document.getElementById('price_per_kilo').value = '';
        document.getElementById('stock').value = '';
        document.getElementById('formTitle').innerText = 'Add New Rice Product';
        document.getElementById('submitBtn').innerText = 'Save Product';
        document.getElementById('cancelBtn').style.display = 'none';
    }

    function editProduct(id, name, category, price, stock) {
        // Set form to update mode
        document.getElementById('productForm').action = "/menus/" + id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('productId').value = id;
        document.getElementById('name').value = name;
        document.getElementById('category').value = category;
        document.getElementById('price_per_kilo').value = price;
        document.getElementById('stock').value = stock;
        document.getElementById('formTitle').innerText = 'Edit Rice Product';
        document.getElementById('submitBtn').innerText = 'Update Product';
        document.getElementById('cancelBtn').style.display = 'inline-block';
    }
</script>