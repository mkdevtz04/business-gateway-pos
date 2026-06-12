@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">My Profile</h1>
        <p class="page-subtitle">Manage your account information and password</p>
    </div>
</div>

<div class="max-w-2xl mx-auto space-y-5">

    {{-- Profile info --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-1">Profile Information</h2>
        <p class="text-xs text-gray-400 mb-5">Update your name and email address.</p>
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Password --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-1">Change Password</h2>
        <p class="text-xs text-gray-400 mb-5">Use a long, random password to stay secure.</p>
        @include('profile.partials.update-password-form')
    </div>

    {{-- Delete account --}}
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-red-700 mb-1">Delete Account</h2>
        <p class="text-xs text-gray-400 mb-5">Permanently delete your account and all associated data.</p>
        @include('profile.partials.delete-user-form')
    </div>

</div>
@endsection
