@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Edit Product</h2>
    
    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Name Field -->
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input id="name" name="name" type="text" class="w-full p-3 border rounded" 
                value="{{ old('name', $product->name) }}" required>
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Category Field -->
        <div class="mb-4">
            <label for="category_id" class="block text-gray-700">Category</label>
            <select id="category_id" name="category_id" class="w-full p-3 border rounded">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Quantity Field -->
        <div class="mb-4">
            <label for="quantity_available" class="block text-gray-700">Quantity Available</label>
            <input id="quantity_available" name="quantity_available" type="number" 
                class="w-full p-3 border rounded" 
                value="{{ old('quantity_available', $product->quantity_available) }}" required>
            @error('quantity_available')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Size Field -->
        <div class="mb-4">
            <label for="size" class="block text-gray-700">Size</label>
            <input id="size" name="size" type="text" class="w-full p-3 border rounded" 
                value="{{ old('size', $product->size) }}">
            @error('size')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Price Field -->
        <div class="mb-4">
            <label for="price" class="block text-gray-700">Price</label>
            <input id="price" name="price" type="number" step="0.01" 
                class="w-full p-3 border rounded" 
                value="{{ old('price', $product->price) }}" required>
            @error('price')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tax Rate Field -->
        <div class="mb-4">
            <label for="tax_rate" class="block text-gray-700">Tax Rate (%)</label>
            <input id="tax_rate" name="tax_rate" type="number" step="0.01" 
                class="w-full p-3 border rounded" 
                value="{{ old('tax_rate', $product->tax_rate) }}">
            @error('tax_rate')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Image Upload Field -->
        <div class="mb-4">
            <label for="image" class="block text-gray-700">Product Image</label>
            @if($product->image_path)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $product->image_path) }}" 
                        alt="Current product image" 
                        class="w-32 h-32 object-cover rounded">
                </div>
            @endif
            <input id="image" name="image" type="file" accept="image/*" 
                class="w-full p-3 border rounded">
            @error('image')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex justify-between">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
                Update Product
            </button>
            <a href="{{ route('products.index') }}" 
                class="bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection