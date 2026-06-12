@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('customers.index') }}" class="text-sm text-gray-400 hover:text-blue-600 flex items-center gap-1 mb-1">
            <i class="fas fa-arrow-left text-xs"></i> Customers
        </a>
        <h1 class="page-title">Edit Customer</h1>
    </div>
</div>

<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}"
                       class="form-input @error('name') border-red-400 @enderror" required>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Phone / Email</label>
                <input type="text" name="contact" value="{{ old('contact', $customer->contact) }}"
                       class="form-input" placeholder="Optional">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
