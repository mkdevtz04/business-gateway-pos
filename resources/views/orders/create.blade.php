@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow flex space-x-6">
        <div class="w-2/3">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Create Order</h2>
            <form method="POST" action="{{ route('orders.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="product_search" class="block text-gray-700">Search Product</label>
                    <input id="product_search" type="text" class="w-full p-3 border rounded" placeholder="Search by Name"
                        autocomplete="off">
                    <select id="product_id" name="product_id" class="w-full p-3 border rounded mt-2" required>
                        <option value="">Select a product</option>
                    </select>
                    @error('product_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block text-gray-700">Quantity</label>
                    <input id="quantity" name="quantity" type="number" min="1" class="w-1/2 p-3 border rounded"
                        value="1" required>
                    @error('quantity')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="discount" class="block text-gray-700">Discount</label>
                    <select id="discount" name="discount" class="w-1/2 p-3 border rounded">
                        <option value="0">None</option>
                        <option value="5">5%</option>
                        <option value="10">10%</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="payment_method" class="block text-gray-700">Payment Method (Optional)</label>
                    <select id="payment_method" name="payment_method" class="w-1/2 p-3 border rounded">
                        <option value="">None</option>
                        <option value="cash">Cash</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="customer_name" class="block text-gray-700">Customer Name (Optional)</label>
                    <input id="customer_name" name="customer_name" type="text" class="w-full p-3 border rounded">
                </div>
                <div class="mb-4">
                    <label for="customer_contact" class="block text-gray-700">Customer Contact (Optional)</label>
                    <input id="customer_contact" name="customer_contact" type="text" class="w-full p-3 border rounded">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Complete
                    Sale</button>
            </form>
        </div>
        <div class="w-1/3">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Order Summary</h2>
            <div id="order_summary" class="bg-gray-100 p-4 rounded">
                <p id="no_items" class="text-gray-700">No items selected.</p>
                <div id="summary_details" class="hidden">
                    <p><strong>Product:</strong> <span id="summary_product"></span></p>
                    <p><strong>Quantity:</strong> <span id="summary_quantity"></span></p>
                    <p><strong>Subtotal:</strong> $<span id="summary_subtotal"></span></p>
                    <p><strong>Discount:</strong> <span id="summary_discount"></span>%</p>
                    <p><strong>Tax:</strong> $<span id="summary_tax"></span></p>
                    <p><strong>Total:</strong> $<span id="summary_total"></span></p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const productSearch = document.getElementById('product_search');
            const productSelect = document.getElementById('product_id');
            const quantityInput = document.getElementById('quantity');
            const discountSelect = document.getElementById('discount');
            const summaryDetails = document.getElementById('summary_details');
            const summaryProduct = document.getElementById('summary_product');
            const summaryQuantity = document.getElementById('summary_quantity');
            const summarySubtotal = document.getElementById('summary_subtotal');
            const summaryDiscount = document.getElementById('summary_discount');
            const summaryTax = document.getElementById('summary_tax');
            const summaryTotal = document.getElementById('summary_total');
            const noItems = document.getElementById('no_items');
            productSearch.addEventListener('input', async function(e) {
                const query = e.target.value.trim();

                if (query.length < 2) {
                    productSelect.innerHTML = '<option value="">Select a product</option>';
                    updateSummary();
                    return;
                }

                try {
                    const response = await fetch(`{{ route('products.search') }}?q=${encodeURIComponent(query)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

                    const products = await response.json();

                    productSelect.innerHTML = '<option value="">Select a product</option>';
                    products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.text = `${product.name} ($${parseFloat(product.price).toFixed(2)})`;
                        option.dataset.price = product.price;
                        option.dataset.taxRate = product.tax_rate || 0;
                        productSelect.appendChild(option);
                    });

                    if (products.length === 0) {
                        productSelect.innerHTML = '<option value="">No products found</option>';
                    }

                    updateSummary();
                } catch (error) {
                    console.error('Fetch error:', error);
                    productSelect.innerHTML = '<option value="">Error fetching products</option>';
                }
            });


            function updateSummary() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const quantity = parseInt(quantityInput.value) || 1;
                const discount = parseInt(discountSelect.value) || 0;

                if (selectedOption && selectedOption.value && selectedOption.value !== '') {
                    const price = parseFloat(selectedOption.dataset.price);
                    const taxRate = parseFloat(selectedOption.dataset.taxRate);
                    const subtotal = price * quantity;
                    const discountAmount = (discount / 100) * subtotal;
                    const tax = (taxRate / 100) * subtotal;
                    const total = subtotal - discountAmount + tax;

                    summaryProduct.textContent = selectedOption.text;
                    summaryQuantity.textContent = quantity;
                    summarySubtotal.textContent = subtotal.toFixed(2);
                    summaryDiscount.textContent = discount;
                    summaryTax.textContent = tax.toFixed(2);
                    summaryTotal.textContent = total.toFixed(2);
                    summaryDetails.classList.remove('hidden');
                    noItems.classList.add('hidden');
                } else {
                    summaryDetails.classList.add('hidden');
                    noItems.classList.remove('hidden');
                }
            }

            productSelect.addEventListener('change', updateSummary);
            quantityInput.addEventListener('input', updateSummary);
            discountSelect.addEventListener('change', updateSummary);

            // Initialize summary on page load
            updateSummary();
        </script>
    @endpush
@endsection
