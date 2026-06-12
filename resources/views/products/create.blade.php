@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <div class="page-header">
        <div>
            <h1 class="page-title">Add Product</h1>
            <p class="page-subtitle">Fill in the details to add a new product</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left text-xs"></i>
            Back
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group mb-0">
                    <label for="name" class="form-label">Product Name <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text"
                           class="form-input @error('name') border-red-400 @enderror"
                           value="{{ old('name') }}" placeholder="e.g. Wireless Mouse" required>
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group mb-0">
                    <label for="category_id" class="form-label">Category</label>
                    <select id="category_id" name="category_id" class="form-input">
                        <option value="">— None —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group mb-0">
                    <label for="quantity_available" class="form-label">Stock Quantity <span class="text-red-500">*</span></label>
                    <input id="quantity_available" name="quantity_available" type="number" min="0"
                           class="form-input @error('quantity_available') border-red-400 @enderror"
                           value="{{ old('quantity_available', 0) }}" required>
                    @error('quantity_available')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group mb-0">
                    <label for="size" class="form-label">Size <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input id="size" name="size" type="text"
                           class="form-input"
                           value="{{ old('size') }}" placeholder="e.g. Large, XL, 500ml">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group mb-0">
                    <label for="price" class="form-label">Price ($) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">$</span>
                        <input id="price" name="price" type="number" step="0.01" min="0"
                               class="form-input pl-7 @error('price') border-red-400 @enderror"
                               value="{{ old('price') }}" placeholder="0.00" required>
                    </div>
                    @error('price')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group mb-0">
                    <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                    <div class="relative">
                        <input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="100"
                               class="form-input pr-7"
                               value="{{ old('tax_rate', 0) }}">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                    </div>
                </div>
            </div>

            {{-- Image upload --}}
            <div class="form-group mb-0">
                <label class="form-label">Product Image</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-colors" id="drop-zone">
                    <input id="image" name="image" type="file" accept="image/*" class="hidden" onchange="previewImage(this)">
                    <div id="upload-placeholder">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2 block"></i>
                        <label for="image" class="text-sm text-blue-600 font-medium cursor-pointer hover:underline">Choose image</label>
                        <span class="text-sm text-gray-400"> or drag and drop</span>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                    </div>
                    <img id="image-preview" class="hidden mx-auto max-h-40 rounded-lg" alt="Preview">
                </div>
                @error('image')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-primary flex-1 justify-center py-2.5">
                    <i class="fas fa-save text-xs"></i>
                    Save Product
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary flex-1 justify-center py-2.5">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('upload-placeholder').classList.add('hidden');
        const preview = document.getElementById('image-preview');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
@endsection
