@extends('adminlte::page')

@section('title', 'User Profile')

@section('content_header')
    <h1>User Profile</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form id="profile-form">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
            </div>
            <div class="form-group mt-3">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="form-group mt-3">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="administrator" {{ $user->role === 'administrator' ? 'selected' : '' }}>Administrator</option>
                    <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                    <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Client</option>
                </select>
            </div>
            @endif
            <div class="form-group mt-3">
                <label for="password">New Password (leave blank to keep current)</label>
                <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
            </div>
            <div class="form-group mt-3">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
        </form>
        <div id="message" class="mt-3"></div>
    </div>
</div>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/profile.js') }}"></script>
@stop
