@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Edit Category</h2>
    
    <form method="POST" action="{{ route('categories.update', $category) }}">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input id="name" name="name" type="text" 
                   class="w-full p-3 border rounded" 
                   value="{{ old('name', $category->name) }}" required>
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between">
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
                Update Category
            </button>
            <a href="{{ route('categories.index') }}" 
               class="bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection