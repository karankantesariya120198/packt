@extends('layout.master')

@section('title', 'Books')

@section('body')
    @php
        $url = route('book.list');
        $baseUrl = url('/');
    @endphp
    <div class="container">

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6"><b>Book Store</b></div>
                    <div class="col col-md-6">
                        <a href="{{ route('book.create') }}" class="btn btn-success btn-sm float-end">Add</a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-2">
                <table class="table table-bordered table-hover pt-5" id="bookTable">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="border">Id</th>
                            <th scope="col" class="border">Image</th>
                            <th scope="col" class="border">Title</th>
                            <th scope="col" class="border">Author</th>
                            <th scope="col" class="border">Genre</th>
                            <th scope="col" class="border">Description</th>
                            <th scope="col" class="border">Isbn</th>
                            <th scope="col" class="border">Published</th>
                            <th scope="col" class="border">Publisher</th>
                            <th scope="col" class="border">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('js/book.js')}}"></script>
    <script>
        var BookListURL = @json($url);
        var BASEURL = @json($baseUrl);
    </script>
@endpush