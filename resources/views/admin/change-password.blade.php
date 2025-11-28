@extends('layouts.admin')

@section('content')
<h2>Change Password</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.password.update') }}">
    @csrf

    <div>
        <label>Current Password</label>
        <input type="password" name="current_password" required>
    </div>

    <div>
        <label>New Password</label>
        <input type="password" name="new_password" required>
    </div>

    <div>
        <label>Confirm New Password</label>
        <input type="password" name="new_password_confirmation" required>
    </div>

    <button type="submit">Change Password</button>
</form>
@endsection
