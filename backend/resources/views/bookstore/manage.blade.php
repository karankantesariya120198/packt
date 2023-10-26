@extends('layout.master')

@if(!empty($book))
    @section('title', 'Book Edit')
@else
    @section('title', 'Book Add')
@endif

@section('body')
    @php
        $url = route('book.list');
        $baseUrl = url('/');
    @endphp
    <div class="container">
        <div class="card">
            <div class="card-header">
                Add Book
            </div>
            <div class="card-body">
                <form
                    method="POST"
                    name="editBookForm"
                    id="editBookForm"
                    enctype="multipart/form-data"
                    action="{{ !empty($book) ? route('book.update', $book->id) : route('book.store') }}"
                >
                    @if(!empty($book))
                        @method('PUT')
                    @endif
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="row mb-4">
                        <div class="col-5">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="title" 
                                    id="title"
                                    placeholder="Enter your book title"
                                    value="{{ !empty($book) ? $book->title : old('title') }}"
                                    required
                                >
                            </div>
                        </div>
                        <div class="col-2"></div>
                        <div class="col-5">
                            <div class="form-group">
                                <label for="title">Author</label>
                                <input 
                                    type="text" 
                                    class="form-control"
                                    name="author" 
                                    id="author" 
                                    placeholder="Enter your book author"
                                    value="{{ !empty($book) ? $book->author : old('author') }}"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-5">
                            <div class="form-group">
                                <label for="genre">Genre</label>
                                <input 
                                    type="text" 
                                    class="form-control"
                                    name="genre" 
                                    id="genre" 
                                    placeholder="Enter your book genre"
                                    value="{{ !empty($book) ? $book->genre : old('genre') }}"
                                    required
                                >
                            </div>
                        </div>
                        <div class="col-2"></div>
                        <div class="col-5">
                            <div class="form-group">
                                <label for="description">description</label>
                                <textarea 
                                    rows="4" 
                                    cols="50" 
                                    class="form-control" 
                                    name="description" 
                                    id="description" 
                                    placeholder="Enter your book Description"
                                    required
                                >{{ !empty($book) ? $book->description : old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-5">
                            <div class="form-group">
                                <label for="isbn">Isbn</label>
                                <input 
                                    type="text" 
                                    class="form-control"
                                    name="isbn" 
                                    id="isbn" 
                                    placeholder="Enter your book isbn"
                                    value="{{ !empty($book) ? $book->isbn : old('isbn') }}"
                                    required
                                >
                            </div>
                        </div>
                        <div class="col-2"></div>
                        <div class="col-5">
                            <div class="form-group">
                                <label for="image">Image</label>
                                <input 
                                    type="file" 
                                    class="form-control" 
                                    id="image"
                                    name="image"
                                    size="2" 
                                    accept="image/jpg,image/png,image/jpeg"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-5">
                            <div class="form-group">
                                <label for="published">Published</label>
                                <input 
                                    type="date" 
                                    class="form-control" 
                                    id="published"
                                    name="published"
                                    value="{{ !empty($book) ? $book->published : old('published') }}"
                                    required
                                >
                            </div>
                        </div>
                        <div class="col-2"></div>
                        <div class="col-5">
                            <div class="form-group">
                                <label for="publisher">Publisher</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="publisher"
                                    name="publisher" 
                                    placeholder="Enter your book publisher"
                                    value="{{ !empty($book) ? $book->publisher : old('publisher') }}"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-group float-end">
                                <button 
                                    class="btn btn-primary"
                                    type="submit" 
                                    class="form-control" 
                                    id="btnBookEdit"
                                    name="btnBookEdit"
                                >
                                    @if(!empty($book))
                                        Update
                                    @else
                                        Submit
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script src="{{ asset('js/book.js') }}"></script>
    <script>
        var BookListURL = @json($url);
        var BASEURL = @json($baseUrl);
    </script>
@endpush