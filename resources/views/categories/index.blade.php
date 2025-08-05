@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Categories</h2>
        <a href="{{ route('categories.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Add Category
        </a>
    </div>

    <table class="w-full border">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Products Count</th>
                <th class="p-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr class="border-t">
                <td class="p-3">{{ $category->name }}</td>
                <td class="p-3">{{ $category->products_count }}</td>
                <td class="p-3">
                    <div class="flex space-x-2">
                        <a href="{{ route('categories.edit', $category) }}" 
                           class="text-blue-600 hover:text-blue-800">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" 
                              class="inline" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection