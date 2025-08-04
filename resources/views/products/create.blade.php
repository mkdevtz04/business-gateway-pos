@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Create Product</h2>
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Name</label>
                <input id="name" name="name" type="text" class="w-full p-3 border rounded" required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700">Category</label>
                <select id="category_id" name="category_id" class="w-full p-3 border rounded">
                    <option value="">None</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="quantity_available" class="block text-gray-700">Quantity</label>
                <input id="quantity_available" name="quantity_available" type="number" min="0"
                    class="w-full p-3 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="size" class="block text-gray-700">Size (Optional)</label>
                <input id="size" name="size" type="text" class="w-full p-3 border rounded">
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700">Price</label>
                <input id="price" name="price" type="number" step="0.01" min="0"
                    class="w-full p-3 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="tax_rate" class="block text-gray-700">Tax Rate (%)</label>
                <input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="100"
                    class="w-full p-3 border rounded" value="0">
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700">Product Image</label>
                <input id="image" name="image" type="file" accept="image/*" class="w-full p-3 border rounded">
                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700">Save</button>
        </form>
    </div>
@endsection
