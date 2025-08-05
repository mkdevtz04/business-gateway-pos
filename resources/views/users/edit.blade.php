@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Edit User</h2>
    
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        
        <!-- Name and Email -->
        <div class="flex gap-4 mb-4">
            <div class="flex-1">
                <label for="name" class="block text-gray-700">Name</label>
                <input id="name" name="name" type="text" 
                    class="w-full p-3 border rounded" 
                    value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex-1">
                <label for="email" class="block text-gray-700">Email</label>
                <input id="email" name="email" type="email" 
                    class="w-full p-3 border rounded" 
                    value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Password and Confirmation (Optional) -->
        <div class="flex gap-4 mb-4">
            <div class="flex-1">
                <label for="password" class="block text-gray-700">
                    New Password (leave blank to keep current)
                </label>
                <input id="password" name="password" type="password" 
                    class="w-full p-3 border rounded">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex-1">
                <label for="password_confirmation" class="block text-gray-700">
                    Confirm New Password
                </label>
                <input id="password_confirmation" name="password_confirmation" 
                    type="password" class="w-full p-3 border rounded">
            </div>
        </div>

        <!-- Role -->
        <div class="mb-6">
            <label for="role" class="block text-gray-700">Role</label>
            <select id="role" name="role" class="w-full p-3 border rounded" required>
                @foreach($roles as $role)
                    <option value="{{ $role }}" 
                        {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                        {{ ucfirst($role) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit" 
                class="flex-1 bg-blue-600 text-white p-3 rounded hover:bg-blue-700">
                Update User
            </button>
            <a href="{{ route('users.index') }}" 
                class="flex-1 bg-gray-500 text-white p-3 rounded hover:bg-gray-600 text-center">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection