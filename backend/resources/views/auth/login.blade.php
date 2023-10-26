@extends('layout.master')

@section('title', 'Login')

@section('body')

<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img 
                    src="{{ asset('images/bookstore-logo.png') }}"
                    class="img-fluid" 
                    alt="Bookstore Logo"
                >
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <div class="card">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card-header">
                        <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                            <p class="lead fw-normal mb-0 me-3">
                                Sign in with
                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form
                            method="POST"
                            name="loginForm"
                            id="loginForm"
                            action="{{ route('login.save') }}"
                        >
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="form-outline mb-3">
                                        <label class="form-label" for="email">Email address</label>
                                        <input 
                                            type="email"
                                            name="email" 
                                            id="email" 
                                            class="form-control"
                                            placeholder="Enter a valid email address"
                                            required 
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-outline mb-3">
                                        <label class="form-label" for="password">Password</label>
                                        <input 
                                            type="password"
                                            name="password" 
                                            id="password" 
                                            class="form-control"
                                            placeholder="Enter password"
                                            required 
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="float-end">
                                        <button 
                                            type="submit"
                                            name="btnLogin"
                                            id="btnLogin" 
                                            class="btn btn-primary btnLogin"
                                        >
                                            Login
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('js')
    <script src = "{{ asset('js/auth.js') }}"></script>
@endpush

@endsection()