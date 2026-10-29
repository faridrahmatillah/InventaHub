@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mt-5">
            <div class="card-header bg-white py-3 text-center">
                <h5 class="mb-0 text-primary fw-bold">Login Sistem</h5>
            </div>
            <div class="card-body px-4 py-4">
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Masuk</button>
                </form>
                <div class="text-center mt-3">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-decoration-none">Daftar di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection