@extends('layouts.app')

@section('content')
<div class="flex h-full">
    <!-- Products List -->
    <div class="w-2/3 p-6 bg-white rounded-lg shadow mr-4">
        <h2 class="text-xl font-bold mb-4">Products</h2>
        <div class="grid grid-cols-3 gap-4">
            @foreach($products as $product)
            <div class="border p-4 rounded-lg cursor-pointer hover:shadow-lg transition-shadow product-card"
                 data-product="{{ json_encode($product) }}">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" 
                         alt="{{ $product->name }}"
                         class="w-full h-32 object-cover rounded mb-2">
                @else
                    <div class="w-full h-32 bg-gray-200 rounded flex items-center justify-center mb-2">
                        <span class="text-gray-500">No image</span>
                    </div>
                @endif
                <h3 class="font-semibold">{{ $product->name }}</h3>
                <p class="text-gray-600">Price: ${{ number_format($product->price, 2) }}</p>
                <p class="text-sm {{ $product->quantity_available < 10 ? 'text-red-600' : 'text-gray-500' }}">
                    Stock: {{ $product->quantity_available }}
                </p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Cart -->
    <div class="w-1/3 bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">Cart</h2>
        <div id="cart-items" class="mb-4 space-y-2"></div>
        
        <div class="border-t pt-4">
            <div class="flex justify-between mb-2">
                <span>Subtotal:</span>
                <span id="subtotal">$0.00</span>
            </div>
            <div class="flex justify-between mb-2">
                <span>Tax:</span>
                <span id="tax">$0.00</span>
            </div>
            <div class="flex justify-between mb-4 font-bold">
                <span>Total:</span>
                <span id="total">$0.00</span>
            </div>

            <select id="payment-method" class="w-full p-2 border rounded mb-4">
                <option value="cash">Cash</option>
                <option value="credit">Credit Card</option>
            </select>

            <button id="checkout-btn" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                Checkout
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart = [];

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all product cards
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const product = JSON.parse(this.dataset.product);
            addToCart(product);
        });
    });

    // Add click event listener to checkout button
    document.getElementById('checkout-btn').addEventListener('click', checkout);
});

function addToCart(product) {
    console.log('Adding product:', product); // Debug line

    if (product.quantity_available <= 0) {
        alert('This product is out of stock!');
        return;
    }

    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        if (existingItem.quantity >= product.quantity_available) {
            alert('Cannot add more items than available in stock!');
            return;
        }
        existingItem.quantity++;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            quantity: 1,
            tax_rate: parseFloat(product.tax_rate),
            max_quantity: product.quantity_available
        });
    }
    
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartEl = document.getElementById('cart-items');
    cartEl.innerHTML = '';
    
    let subtotal = 0;
    let totalTax = 0;
    
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        const itemTax = (itemTotal * item.tax_rate) / 100;
        subtotal += itemTotal;
        totalTax += itemTax;
        
        cartEl.innerHTML += `
            <div class="flex justify-between items-center border-b pb-2">
                <div class="flex-1">
                    <h4 class="font-semibold">${item.name}</h4>
                    <div class="flex items-center gap-2">
                        <button class="quantity-btn" data-id="${item.id}" data-change="-1">-</button>
                        <span>${item.quantity}</span>
                        <button class="quantity-btn" data-id="${item.id}" data-change="1">+</button>
                    </div>
                    <p class="text-sm text-gray-600">$${item.price.toFixed(2)} x ${item.quantity}</p>
                </div>
                <button class="remove-btn text-red-600 hover:text-red-800" data-id="${item.id}">
                    Remove
                </button>
            </div>
        `;
    });

    // Add event listeners to new buttons
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = parseInt(btn.dataset.id);
            const change = parseInt(btn.dataset.change);
            updateQuantity(id, change);
        });
    });

    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = parseInt(btn.dataset.id);
            removeFromCart(id);
        });
    });
    
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent = `$${totalTax.toFixed(2)}`;
    document.getElementById('total').textContent = `$${(subtotal + totalTax).toFixed(2)}`;
}

function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        const newQuantity = item.quantity + change;
        if (newQuantity > 0 && newQuantity <= item.max_quantity) {
            item.quantity = newQuantity;
            updateCartDisplay();
        }
    }
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartDisplay();
}

function checkout() {
    if (cart.length === 0) {
        alert('Cart is empty!');
        return;
    }

    // Disable checkout button
    const checkoutBtn = document.getElementById('checkout-btn');
    checkoutBtn.disabled = true;
    checkoutBtn.textContent = 'Processing...';

    fetch('{{ route("pos.checkout") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            products: cart,
            payment_method: document.getElementById('payment-method').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            throw new Error(data.message || 'Checkout failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'An error occurred during checkout');
    })
    .finally(() => {
        // Re-enable checkout button
        checkoutBtn.disabled = false;
        checkoutBtn.textContent = 'Checkout';
    });
}
</script>
@endpush
@endsection