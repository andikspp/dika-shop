@extends('back.layout.login-layout')

@section('title', 'Forgot Password')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card">
            <div class="card-body">
                <h2 class="text-center mb-4">Forgot Password</h2>
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="form-control">
                    </div>

                    @error('email')
                        <span role="alert" class="text-danger">{{ $message }}</span>
                    @enderror

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary text-white">Send Password Reset Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
