@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Products</h2>

        <a href="{{ route('products.create') }}" class="bg-blue-600 blue-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">Add Product</a>

    <table class="w-full border">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3 text-left">Image</th>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Category</th>
                <th class="p-3 text-left">Quantity</th>
                <th class="p-3 text-left">Size</th>
                <th class="p-3 text-left">Price</th>
                <th class="p-3 text-left">Tax Rate</th>
                
                    <th class="p-3 text-left">Actions</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr @if ($product->quantity_available < 10) class="bg-yellow-50" @endif>
                    <td class="p-3">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-16 h-16 object-cover rounded">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-gray-500">No image</span>
                            </div>
                        @endif
                    </td>
                    <td class="p-3">{{ $product->name }}</td>
                    <td class="p-3">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                    <td class="p-3">{{ $product->quantity_available }}</td>
                    <td class="p-3">{{ $product->size ?? 'N/A' }}</td>
                    <td class="p-3">${{ number_format($product->price, 2) }}</td>
                    <td class="p-3">{{ $product->tax_rate }}%</td>
                    
                        <td class="p-3">
                            <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection