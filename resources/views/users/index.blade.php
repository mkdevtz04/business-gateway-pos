@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Users</h1>
        <p class="page-subtitle">Manage staff accounts and access roles</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus text-xs"></i>
        Add User
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
        <div class="search-wrapper flex-1 max-w-xs">
            <i class="fas fa-search search-icon"></i>
            <input id="table-search" type="text" class="form-input" placeholder="Search users…">
        </div>
        <span id="row-count" class="text-sm text-gray-400 ml-auto"></span>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table" id="users-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $user->role) }}">
                    <td>
                        <div class="flex items-center gap-3">
                            @php
                                $avatarClass = match($user->role) {
                                    'admin' => 'bg-red-100 text-red-600',
                                    'owner' => 'bg-blue-100 text-blue-600',
                                    default => 'bg-emerald-100 text-emerald-600',
                                };
                            @endphp
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0 {{ $avatarClass }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                @if($user->id === auth()->id())
                                    <span class="text-xs text-blue-500 font-medium">You</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-gray-600">{{ $user->email }}</td>
                    <td>
                        @if($user->role === 'admin')
                            <span class="badge badge-red">Admin</span>
                        @elseif($user->role === 'owner')
                            <span class="badge badge-blue">Owner</span>
                        @else
                            <span class="badge badge-green">Clerk</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('users.edit', $user) }}"
                               class="btn btn-secondary btn-sm btn-icon" title="Edit">
                                <i class="fas fa-pencil-alt text-xs"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                  data-confirm="Delete user '{{ $user->name }}'? This cannot be undone.">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                            @else
                            <span class="btn btn-secondary btn-sm btn-icon opacity-30 cursor-not-allowed" title="Cannot delete yourself">
                                <i class="fas fa-trash text-xs"></i>
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-12 text-gray-400">
                        <i class="fas fa-users text-3xl mb-3 block text-gray-200"></i>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const search  = document.getElementById('table-search');
    const rows    = document.querySelectorAll('#users-table tbody tr[data-search]');
    const countEl = document.getElementById('row-count');
    const total   = rows.length;
    function update() {
        const q = search.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach(r => {
            const match = !q || r.dataset.search.includes(q);
            r.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        countEl.textContent = `${visible} / ${total} users`;
    }
    search.addEventListener('input', update);
    update();
})();
</script>
@endpush
@endsection
