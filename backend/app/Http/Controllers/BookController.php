<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            DB::beginTransaction();
            $searchValue = $request->search;
            $books = Book::where(function($query) use($searchValue) {
                            if(!is_null($searchValue)) {
                                $query->Where('title', 'like', '%' .$searchValue . '%')
                                        ->orWhere('author', 'like', '%' .$searchValue . '%')
                                        ->orWhere('genre', 'like', '%' .$searchValue . '%')
                                        ->orWhere('isbn', 'like', '%' .$searchValue . '%')
                                        ->orWhere('published', 'like', '%' .$searchValue . '%')
                                        ->orWhere('publisher', 'like', '%' .$searchValue . '%');
                            }
                        })->orderBy('id','DESC')->paginate(10);
            
            $bookResource = !empty($books->toArray()) ? BookResource::collection($books) : [];
            DB::commit();

            return successResponse(
                Config::get('constant.api.BOOKS_DATA_FETCHED_SUCCESS'),
                Response::HTTP_OK,
                $books
            );
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController index API.");
            Log::error(config('constant.api.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage() . '  ' . $e->getLine(),
            ]);
            $errorCode = 'ER0000';
            $errorMsg = config('constant.api.DEFAULT_ERROR_MESSAGE');
            return errorResponse(
                $errorMsg,
                $errorCode,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            // Check validation of request parameter
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'author' => 'required',
                'genre' => 'required',
                'description' => 'required',
                'isbn' => 'required',
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2000',
                'published' => 'required|date',
                'publisher' => 'required',
            ]);
            
            if($validator->fails()) {
                $errors = $validator->messages()->all();
                $errorCode = 'ER0001';
                $errorMsg = $errors;
                return errorResponse(
                    $errorMsg,
                    $errorCode,
                    Response::HTTP_BAD_REQUEST
                );
            } else {
                DB::beginTransaction();
                
                if($request->hasFile('image')) {
                    $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
                    Storage::put(Config::get('constant.api.BOOKS_IMAGE_PATH').$imageName, file_get_contents($request->image), 'public');        
                } else {
                    $imageName = NULL;
                }
                
                $book = new Book();
                $book->title = $request->title;
                $book->author = $request->author;
                $book->genre = $request->genre;
                $book->description = $request->description;
                $book->isbn = $request->isbn;
                $book->image = $imageName;
                $book->published = Carbon::parse($request->published)->format('Y-m-d');
                $book->publisher = $request->publisher;
                $book->save();

                if($book) {
                    $bookResource = !empty($book->toArray()) ? new BookResource($book) : "";
                    DB::commit();

                    return successResponse(
                        Config::get('constant.api.BOOK_CREATED_SUCCESS'),
                        Response::HTTP_CREATED,
                        $bookResource
                    );
                }
            }
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController store API.");
            Log::error(config('constant.api.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage() . '  ' . $e->getLine(),
            ]);
            $errorCode = 'ER0000';
            $errorMsg = config('constant.api.DEFAULT_ERROR_MESSAGE');
            return errorResponse(
                $errorMsg,
                $errorCode,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        try{
            DB::beginTransaction();
            $bookResource = !empty($book->toArray()) ? new BookResource($book) : "";
            DB::commit();
            return successResponse(
                Config::get('constant.api.BOOK_DATA_FETCH_SUCCESS'),
                Response::HTTP_OK,
                $bookResource
            );
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController show API.");
            Log::error(config('constant.api.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage() . '  ' . $e->getLine(),
            ]);
            $errorCode = 'ER0000';
            $errorMsg = config('constant.api.DEFAULT_ERROR_MESSAGE');
            return errorResponse(
                $errorMsg,
                $errorCode,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        try{
            // Check validation of request parameter
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'author' => 'required',
                'genre' => 'required',
                'description' => 'required',
                'isbn' => 'required',
                'image' => 'image|mimes:jpg,jpeg,png|max:2000',
                'published' => 'required|date',
                'publisher' => 'required',
            ]);
            
            if($validator->fails()) {
                $errors = $validator->messages();
                $errorCode = 'ER0001';
                $errorMsg = $errors;
                return errorResponse(
                    $errorMsg,
                    $errorCode,
                    Response::HTTP_BAD_REQUEST
                );
            } else {
                DB::beginTransaction();

                if($request->hasFile('image')) {
                    $imageExists = Storage::exists(Config::get('constant.api.BOOKS_IMAGE_PATH').$book->image);
                    if($imageExists) {
                        Storage::delete(Config::get('constant.api.BOOKS_IMAGE_PATH').$book->image);
                    }

                    $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
                    Storage::put(Config::get('constant.api.BOOKS_IMAGE_PATH').$imageName, file_get_contents($request->image), 'public');
                }

                $book->title = $request->title;
                $book->author = $request->author;
                $book->genre = $request->genre;
                $book->description = $request->description;
                $book->isbn = $request->isbn;
                $book->image = $imageName;
                $book->published = Carbon::parse($request->published)->format('Y-m-d');
                $book->publisher = $request->publisher;
                $book->save();

                if($book) {
                    $bookResource = !empty($book->toArray()) ? new BookResource($book) : "";
                    DB::commit();

                    return successResponse(
                        Config::get('constant.api.BOOK_UPDATE_SUCCESSFULLY'),
                        Response::HTTP_OK,
                        $bookResource
                    );
                }
            }
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController update API.");
            Log::error(config('constant.api.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage() . '  ' . $e->getLine(),
            ]);
            $errorCode = 'ER0000';
            $errorMsg = config('constant.api.DEFAULT_ERROR_MESSAGE');
            return errorResponse(
                $errorMsg,
                $errorCode,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        try {
            DB::beginTransaction();
            if($book->image){
                $imageExists = Storage::exists(Config::get('constant.api.BOOKS_IMAGE_PATH').$book->image);
                if($imageExists){
                    Storage::delete(Config::get('constant.api.BOOKS_IMAGE_PATH').$book->image);
                }
            }

            $book->delete();
            DB::commit();

            return successResponse(
                Config::get('constant.api.DELETE_BOOK_DATA'),
                Response::HTTP_OK,
                ""
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController destroy API.");
            Log::error(config('constant.api.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage() . '  ' . $e->getLine(),
            ]);
            $errorCode = 'ER0000';
            $errorMsg = config('constant.api.DEFAULT_ERROR_MESSAGE');
            return errorResponse(
                $errorMsg,
                $errorCode,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
