@extends('layouts.app')

@push('styles')
<style>
/* ── Product cards ─────────────────────────────── */
.product-card {
    background: white;
    border: 1.5px solid #E5E7EB;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    overflow: hidden;
    position: relative;
    user-select: none;
}
.product-card:hover:not(.out-of-stock) {
    border-color: #2563EB;
    box-shadow: 0 6px 20px rgba(37,99,235,0.13);
    transform: translateY(-3px);
}
.product-card:active:not(.out-of-stock) {
    transform: translateY(0) scale(0.97);
}
.product-card.out-of-stock { opacity: 0.5; cursor: not-allowed; }
.product-card.out-of-stock:hover { transform: none; box-shadow: none; border-color: #E5E7EB; }
.product-card.just-added { animation: pulse-ring 0.4s ease; }
@keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 0 rgba(37,99,235,0.5); }
    70%  { box-shadow: 0 0 0 10px rgba(37,99,235,0); }
    100% { box-shadow: 0 0 0 0 rgba(37,99,235,0); }
}

/* ── Category tabs ─────────────────────────────── */
.cat-tab {
    padding: 6px 18px; border-radius: 99px;
    font-size: 0.8125rem; font-weight: 500;
    cursor: pointer; transition: all 0.15s;
    border: none; white-space: nowrap;
    background: transparent; color: #6B7280;
}
.cat-tab:hover { background: #F3F4F6; color: #374151; }
.cat-tab.active { background: #1E293B; color: #fff; }

/* ── Cart items ────────────────────────────────── */
.cart-item-enter { animation: slideInRight 0.22s ease; }
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(12px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* ── Qty buttons ───────────────────────────────── */
.qty-btn {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem; font-weight: 600; cursor: pointer;
    transition: all 0.15s; border: none; flex-shrink: 0;
}
.qty-btn-minus { background: #F3F4F6; color: #6B7280; }
.qty-btn-minus:hover { background: #E5E7EB; color: #374151; }
.qty-btn-plus  { background: #DBEAFE; color: #1D4ED8; }
.qty-btn-plus:hover  { background: #BFDBFE; }

/* ── Payment toggle ────────────────────────────── */
.pay-opt input { display: none; }
.pay-btn {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 8px 12px; border-radius: 8px; font-size: 0.8125rem; font-weight: 500;
    cursor: pointer; border: 1.5px solid #E5E7EB; color: #6B7280; background: white;
    transition: all 0.15s;
}
.pay-opt input:checked + .pay-btn { background: #EFF6FF; border-color: #2563EB; color: #2563EB; font-weight: 600; }

/* ── Cart badge ────────────────────────────────── */
.cart-count {
    position: absolute; top: -5px; right: -5px;
    background: #2563EB; color: white;
    width: 17px; height: 17px; border-radius: 50%;
    font-size: 0.6rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
}
</style>
@endpush

@section('content')
<div class="flex gap-5" style="height: calc(100vh - 8.5rem);">

    {{-- ======== LEFT: Products ======== --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Page title + search --}}
        <div class="mb-4">
            <h1 class="text-xl font-bold text-gray-900 mb-3">Point of Sale</h1>
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input id="product-search" type="text"
                       class="form-input w-full bg-white"
                       placeholder="Search products…"
                       autocomplete="off">
            </div>
        </div>

        {{-- Category tabs --}}
        <div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-xl overflow-x-auto flex-shrink-0" id="category-tabs">
            <button class="cat-tab active" data-category="">All</button>
            @foreach($products->pluck('category.name')->filter()->unique()->sort() as $cat)
                <button class="cat-tab" data-category="{{ strtolower($cat) }}">{{ $cat }}</button>
            @endforeach
        </div>

        {{-- Products grid --}}
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3" id="products-grid">
                @foreach($products as $product)
                <div class="product-card {{ $product->quantity_available <= 0 ? 'out-of-stock' : '' }}"
                     data-product="{{ json_encode($product) }}"
                     data-name="{{ strtolower($product->name) }}"
                     data-size="{{ strtolower($product->size ?? '') }}"
                     data-category="{{ strtolower(optional($product->category)->name ?? '') }}">

                    {{-- Out of stock overlay --}}
                    @if($product->quantity_available <= 0)
                    <div class="absolute inset-0 bg-white/75 rounded-[12px] flex items-center justify-center z-10">
                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">Out of Stock</span>
                    </div>
                    @endif

                    {{-- Image --}}
                    @if($product->image_path)
                        <div class="w-full h-36 bg-white flex items-center justify-center p-3">
                            <img src="{{ asset('storage/' . $product->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-contain drop-shadow-sm">
                        </div>
                    @else
                        <div class="w-full h-36 bg-white flex items-center justify-center">
                            <i class="fas fa-box text-gray-200 text-4xl"></i>
                        </div>
                    @endif

                    <div class="px-3 pb-3 pt-2">
                        <h3 class="text-sm font-bold text-gray-900 truncate">{{ $product->name }}</h3>
                        @if($product->size)
                            <p class="text-xs text-gray-400 mb-1">{{ $product->size }}</p>
                        @endif
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-base font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                            @if($product->quantity_available > 0 && $product->quantity_available < 10)
                                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">Low: {{ $product->quantity_available }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div id="no-results" class="hidden flex-col items-center justify-center py-16 text-center">
                <i class="fas fa-search text-4xl text-gray-200 mb-3"></i>
                <p class="text-gray-500 font-medium">No products found</p>
                <p class="text-gray-400 text-sm mt-1">Try a different search or category</p>
            </div>
        </div>
    </div>

    {{-- ======== RIGHT: Current Order ======== --}}
    <div class="w-72 xl:w-80 flex-shrink-0 flex flex-col bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <i class="fas fa-shopping-cart text-gray-700 text-lg"></i>
                    <span id="cart-count" class="cart-count hidden">0</span>
                </div>
                <h2 class="font-bold text-gray-900">Current Order</h2>
            </div>
            <button id="clear-cart" class="hidden text-xs text-red-500 hover:text-red-700 font-medium">
                Clear all
            </button>
        </div>

        {{-- Customer --}}
        <div class="px-5 py-3 border-b border-gray-100 space-y-2.5">
            <div>
                <label class="form-label text-xs mb-1">Customer</label>
                <select id="customer-select" class="form-input text-sm">
                    <option value="">Walk-in Customer</option>
                    <option value="new" class="font-semibold">+ Add New Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div id="new-customer-form" class="hidden space-y-2">
                <input id="customer-name"    type="text" class="form-input text-sm" placeholder="Customer name *">
                <input id="customer-contact" type="text" class="form-input text-sm" placeholder="Phone / Email">
            </div>
        </div>

        {{-- Cart items --}}
        <div id="cart-items" class="flex-1 overflow-y-auto px-5 py-3 space-y-2">
            <div id="cart-empty" class="flex flex-col items-center justify-center h-full py-10 text-center">
                <i class="fas fa-shopping-cart text-4xl text-gray-200 mb-3"></i>
                <p class="text-sm text-gray-400 font-medium">No items in cart</p>
                <p class="text-xs text-gray-300 mt-0.5">Click a product to add it</p>
            </div>
        </div>

        {{-- Totals + Checkout --}}
        <div class="border-t border-gray-100 px-5 pt-4 pb-5 bg-gray-50/50">
            <div class="space-y-1.5 mb-3">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span><span id="subtotal">$0.00</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Tax</span><span id="tax">$0.00</span>
                </div>
            </div>

            {{-- Discount row --}}
            <div class="mb-3 bg-white border border-gray-200 rounded-xl p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Discount</span>
                    {{-- Type toggle --}}
                    <div class="flex rounded-lg overflow-hidden border border-gray-200 text-xs">
                        <button id="disc-type-flat" onclick="setDiscountType('flat')"
                                class="px-2.5 py-1 font-semibold bg-gray-800 text-white transition-colors">$</button>
                        <button id="disc-type-pct"  onclick="setDiscountType('percent')"
                                class="px-2.5 py-1 font-semibold bg-white text-gray-500 hover:bg-gray-50 transition-colors">%</button>
                    </div>
                </div>
                <div class="relative">
                    <span id="disc-prefix" class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">$</span>
                    <input id="discount-input" type="number" min="0" step="0.01" placeholder="0"
                           class="form-input pl-7 text-sm py-2"
                           oninput="updateCartDisplay()">
                </div>
                <div class="flex justify-between text-xs mt-2">
                    <span class="text-gray-400">Discount</span>
                    <span id="discount-line" class="font-semibold text-emerald-600">—</span>
                </div>
            </div>

            <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-200 mb-4">
                <span>Total</span><span id="total">$0.00</span>
            </div>

            {{-- Payment method --}}
            <div class="grid grid-cols-2 gap-2 mb-4">
                <label class="pay-opt">
                    <input type="radio" name="payment-method" value="cash" checked>
                    <div class="pay-btn"><i class="fas fa-money-bill-wave text-xs"></i> Cash</div>
                </label>
                <label class="pay-opt">
                    <input type="radio" name="payment-method" value="credit">
                    <div class="pay-btn"><i class="fas fa-credit-card text-xs"></i> Credit</div>
                </label>
            </div>

            <button id="checkout-btn"
                class="w-full flex items-center justify-center gap-2 py-3 bg-gray-800 hover:bg-gray-900 active:bg-black text-white font-semibold rounded-xl transition-colors text-sm shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-check-circle"></i>
                <span>Checkout</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart         = [];
let discountType = 'flat'; // 'flat' | 'percent'

function setDiscountType(type) {
    discountType = type;
    document.getElementById('disc-prefix').textContent = type === 'percent' ? '%' : '$';
    document.getElementById('discount-input').placeholder = type === 'percent' ? '0' : '0.00';
    const flatBtn = document.getElementById('disc-type-flat');
    const pctBtn  = document.getElementById('disc-type-pct');
    if (type === 'flat') {
        flatBtn.classList.add('bg-gray-800', 'text-white');
        flatBtn.classList.remove('bg-white', 'text-gray-500');
        pctBtn.classList.add('bg-white', 'text-gray-500');
        pctBtn.classList.remove('bg-gray-800', 'text-white');
    } else {
        pctBtn.classList.add('bg-gray-800', 'text-white');
        pctBtn.classList.remove('bg-white', 'text-gray-500');
        flatBtn.classList.add('bg-white', 'text-gray-500');
        flatBtn.classList.remove('bg-gray-800', 'text-white');
    }
    updateCartDisplay();
}

document.addEventListener('DOMContentLoaded', function () {
    // Product cards
    document.querySelectorAll('.product-card:not(.out-of-stock)').forEach(card => {
        card.addEventListener('click', function () {
            addToCart(JSON.parse(this.dataset.product));
            this.classList.add('just-added');
            setTimeout(() => this.classList.remove('just-added'), 450);
        });
    });

    // Search
    document.getElementById('product-search').addEventListener('input', filterProducts);

    // Category tabs
    document.querySelectorAll('.cat-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterProducts();
        });
    });

    // Customer select
    document.getElementById('customer-select').addEventListener('change', function () {
        document.getElementById('new-customer-form').classList.toggle('hidden', this.value !== 'new');
    });

    // Clear cart
    document.getElementById('clear-cart').addEventListener('click', function () {
        if (cart.length === 0) return;
        showConfirm('Clear all items from the order?', () => { cart = []; updateCartDisplay(); }, 'Clear');
    });

    // Checkout
    document.getElementById('checkout-btn').addEventListener('click', checkout);
});

// ── Filter ────────────────────────────────────────────────
function filterProducts() {
    const q   = document.getElementById('product-search').value.trim().toLowerCase();
    const cat = document.querySelector('.cat-tab.active')?.dataset.category || '';
    let visible = 0;
    document.querySelectorAll('.product-card').forEach(card => {
        const match = (!q || card.dataset.name.includes(q) || card.dataset.size.includes(q) || card.dataset.category.includes(q))
                   && (!cat || card.dataset.category === cat);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    const noRes = document.getElementById('no-results');
    noRes.classList.toggle('hidden', visible > 0);
    noRes.classList.toggle('flex',   visible === 0);
}

// ── Add to cart ───────────────────────────────────────────
function addToCart(product) {
    const ex = cart.find(i => i.id === product.id);
    if (ex) {
        if (ex.quantity >= product.quantity_available) {
            showToast('Max available stock reached', 'warning'); return;
        }
        ex.quantity++;
    } else {
        cart.push({ id: product.id, name: product.name, price: parseFloat(product.price),
                    quantity: 1, tax_rate: parseFloat(product.tax_rate), max_quantity: product.quantity_available });
    }
    updateCartDisplay();
}

// ── Update display ────────────────────────────────────────
function updateCartDisplay() {
    const cartEl   = document.getElementById('cart-items');
    const emptyEl  = document.getElementById('cart-empty');
    const countEl  = document.getElementById('cart-count');
    const clearBtn = document.getElementById('clear-cart');

    cartEl.querySelectorAll('.cart-item').forEach(el => el.remove());

    if (cart.length === 0) {
        emptyEl.classList.remove('hidden');
        countEl.classList.add('hidden');
        clearBtn.classList.add('hidden');
        document.getElementById('subtotal').textContent = '$0.00';
        document.getElementById('tax').textContent      = '$0.00';
        document.getElementById('total').textContent    = '$0.00';
        return;
    }

    emptyEl.classList.add('hidden');
    clearBtn.classList.remove('hidden');
    const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
    countEl.textContent = totalItems;
    countEl.classList.remove('hidden');

    let subtotal = 0, totalTax = 0;
    cart.forEach(item => {
        const lineTotal = item.price * item.quantity;
        const lineTax   = lineTotal * item.tax_rate / 100;
        subtotal  += lineTotal;
        totalTax  += lineTax;

        const div = document.createElement('div');
        div.className = 'cart-item cart-item-enter flex items-start gap-2 bg-gray-50 rounded-xl p-3';
        div.innerHTML = `
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">${item.name}</p>
                <p class="text-xs text-gray-400 mt-0.5">$${item.price.toFixed(2)} each</p>
                <div class="flex items-center gap-2 mt-2">
                    <button class="qty-btn qty-btn-minus" data-id="${item.id}" data-change="-1">−</button>
                    <span class="text-sm font-bold w-5 text-center">${item.quantity}</span>
                    <button class="qty-btn qty-btn-plus"  data-id="${item.id}" data-change="1">+</button>
                </div>
            </div>
            <div class="flex flex-col items-end gap-2 flex-shrink-0">
                <span class="text-sm font-bold">${'$' + (lineTotal + lineTax).toFixed(2)}</span>
                <button class="remove-btn text-gray-300 hover:text-red-500 transition-colors" data-id="${item.id}">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </div>`;
        cartEl.appendChild(div);
    });

    cartEl.querySelectorAll('.qty-btn').forEach(btn =>
        btn.addEventListener('click', e => { e.stopPropagation(); updateQty(parseInt(btn.dataset.id), parseInt(btn.dataset.change)); }));
    cartEl.querySelectorAll('.remove-btn').forEach(btn =>
        btn.addEventListener('click', e => { e.stopPropagation(); removeItem(parseInt(btn.dataset.id)); }));

    const gross          = subtotal + totalTax;
    const discVal        = parseFloat(document.getElementById('discount-input')?.value) || 0;
    const discountAmount = discVal > 0
        ? (discountType === 'percent' ? Math.min(gross * discVal / 100, gross) : Math.min(discVal, gross))
        : 0;
    const finalTotal     = gross - discountAmount;

    document.getElementById('subtotal').textContent     = `$${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent          = `$${totalTax.toFixed(2)}`;
    document.getElementById('discount-line').textContent = discountAmount > 0 ? `-$${discountAmount.toFixed(2)}` : '—';
    document.getElementById('total').textContent        = `$${finalTotal.toFixed(2)}`;
}

function updateQty(id, change) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    const next = item.quantity + change;
    if (next < 1) { removeItem(id); return; }
    if (next > item.max_quantity) { showToast('Max stock reached', 'warning'); return; }
    item.quantity = next;
    updateCartDisplay();
}
function removeItem(id) { cart = cart.filter(i => i.id !== id); updateCartDisplay(); }

// ── Checkout ──────────────────────────────────────────────
async function checkout() {
    if (!cart.length) { showToast('Cart is empty', 'warning'); return; }
    const customerId   = document.getElementById('customer-select').value;
    const customerName = document.getElementById('customer-name').value.trim();
    if (customerId === 'new' && !customerName) {
        showToast('Please enter a customer name', 'warning');
        document.getElementById('customer-name').focus(); return;
    }
    const btn = document.getElementById('checkout-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing…</span>';
    try {
        const res  = await fetch('{{ route("pos.checkout") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                products:         cart,
                payment_method:   document.querySelector('input[name="payment-method"]:checked').value,
                customer_id:      customerId,
                customer_name:    customerName,
                customer_contact: document.getElementById('customer-contact').value,
                discount_type:    discountType,
                discount_value:   parseFloat(document.getElementById('discount-input').value) || 0,
            })
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Checkout failed');
        if (data.success) {
            showToast('Sale completed!', 'success');
            setTimeout(() => window.location.href = data.redirect_url, 600);
        } else throw new Error(data.message || 'Checkout failed');
    } catch (err) {
        showToast(err.message, 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i><span>Checkout</span>';
    }
}
</script>
@endpush
@endsection
