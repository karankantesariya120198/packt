<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\BookRequest;
use Alert;

class BookController extends Controller
{

    /**
        * @Author : Developer
        * @Desc   : Display a listing of the resource.
        * @Input  : 
        * @Output : \Illuminate\Http\Response
        * @Date   : 22-Oct-2023
    */

    public function index(): View
    {
        try {
            return view('bookstore.list');
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController index function.");
            Log::error(config('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);
        }
    }

    /**
        * @Author : Developer
        * @Desc   : Display a listing of the books.
        * @Input  : 
        * @Output : \Illuminate\Http\Response
        * @Date   : 22-Oct-2023
    */

    public function bookList(Request $request)
    {
        try {
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');
            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $columnSortOrder = $order_arr[0]['dir'];
            $searchValue = $search_arr['value'];

            $totalRecords = Book::select('count(*) as allcount')
                                ->count();
            
            $totalRecordswithFilter = Book::select('count(*) as allcount')
                                            ->where(function($query) use($searchValue) {
                                                if(!is_null($searchValue)) {
                                                    $query->Where('title', 'like', '%' .$searchValue . '%')
                                                            ->orWhere('author', 'like', '%' .$searchValue . '%')
                                                            ->orWhere('genre', 'like', '%' .$searchValue . '%')
                                                            ->orWhere('isbn', 'like', '%' .$searchValue . '%')
                                                            ->orWhere('published', 'like', '%' .$searchValue . '%')
                                                            ->orWhere('publisher', 'like', '%' .$searchValue . '%');
                                                }
                                            })
                                            ->count();
            
            $records = Book::where(function($query) use($searchValue) {
                                if(!is_null($searchValue)) {
                                    $query->Where('title', 'like', '%' .$searchValue . '%')
                                            ->orWhere('author', 'like', '%' .$searchValue . '%')
                                            ->orWhere('genre', 'like', '%' .$searchValue . '%')
                                            ->orWhere('isbn', 'like', '%' .$searchValue . '%')
                                            ->orWhere('published', 'like', '%' .$searchValue . '%')
                                            ->orWhere('publisher', 'like', '%' .$searchValue . '%');
                                }
                            })
                            ->skip($start)
                            ->take($rowperpage)
                            ->get();
            
            $data_arr = array();
            $sno = $start+1;

            foreach($records as $key => $record){
                $id = $key+1;
                $title = $record->title;
                $author = $record->author;
                $genre = $record->genre;
                $description = $record->description;
                $isbn = $record->isbn;
                $published = $record->published;
                $publisher = $record->publisher;
                if(!empty($record->image_name)) {
                    $filePath = Storage::url(Config::get('constant.BOOKS_IMAGE_PATH').$record->image_name);
                    $imageUrl = "<img src='".$filePath."' width='100' height='100' />";
                } else {
                    $imageUrl = "";
                }

                if(!empty($record->image)) {
                    $imageUrl = "<img src='".$record->image."' width='100' height='100' />";
                }

                $image = $imageUrl;
                $action = '<a href="'.url('/').'/admin/books/edit/'.$record->id.'" class="btn btn-warning btn-sm mb-2">Edit</a><br/><a class="btn btn-danger btn-sm" id="deleteBook" href="#" data-id="'.$record->id.'">Delete</a>';
                
                $data_arr[] = array(
                    "id" => $id,
                    "title" => $title,
                    "author" => $author,
                    "genre" => $genre,
                    "description" => $description,
                    "isbn" => $isbn,
                    "image" => $image,
                    "published" => $published,
                    "publisher" => $publisher,
                    "action" => $action,
                );
                
            }
            
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                "aaData" => $data_arr
            ); 
            
            DB::commit();
            return json_encode($response);
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController bookList function.");
            Log::error(Config::get('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);
        }
    }

    /**
        * @Author : Developer
        * @Desc   : Show the form for creating a new resource.
        * @Input  : 
        * @Output : \Illuminate\Http\Response
        * @Date   : 22-Oct-2023
    */

    public function create(): View
    {
        try {
            return view('bookstore.manage');
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController create function.");
            Log::error(Config::get('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);
        }
    }

    /**
        * @Author : Developer
        * @Desc   : Store a newly created resource in storage.
        * @Input  : 
        * @Output : \Illuminate\Http\Response
        * @Date   : 22-Oct-2023
    */

    public function store(BookRequest $request)
    {
        try {
            DB::beginTransaction();
            
            if($request->hasFile('image')) {
                $originalName = Str::random(10).'.'.$request->image->getClientOriginalExtension();
                $path = Config::get('constant.BOOKS_IMAGE_PATH').$originalName;
                Storage::put($path, file_get_contents($request->image), 'public');
            }

            $book = new Book();
            $book->title = $request->title;
            $book->author = $request->author;
            $book->description = $request->description;
            $book->isbn = $request->isbn;
            $book->genre = $request->genre;
            if($request->hasFile('image')) {
                $book->image = NULL;
                $book->image_name = $originalName;
            }
            $book->published = $request->published;
            $book->publisher = $request->publisher;
            $book->save();

            if($book) {
                DB::commit();
                return redirect()->route('book.index');
            }
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController store function.");
            Log::error(Config::get('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);
        }
    }

    /**
        * @Author : Developer
        * @Desc   : Display the specified resource.
        * @Input  : \Illuminate\Http\Request $id
        * @Output : \Illuminate\Http\Response
        * @Date   : 22-Oct-2023
    */

    public function show($id): View
    {
        try {
            $book = Book::find($id);
            return view('bookstore.manage', compact('book'));
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController show function.");
            Log::error(Config::get('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);
        }     
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        //
    }

    /**
        * @Author : Developer
        * @Desc   : Update the specified resource in storage.
        * @Input  : \Illuminate\Http\Request $request, $id
        * @Output : \Illuminate\Http\Response
        * @Date   : 22-Oct-2023
    */

    public function update(BookRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            
            if($request->hasFile('image')) {
                $originalName = Str::random(10).'.'.$request->image->getClientOriginalExtension();
                $path = Config::get('constant.BOOKS_IMAGE_PATH').$originalName;
                Storage::put($path, file_get_contents($request->file('image')), 'public');
            }

            $book = Book::find($id);
            $book->title = $request->title;
            $book->author = $request->author;
            $book->description = $request->description;
            $book->isbn = $request->isbn;
            $book->genre = $request->genre;
            if($request->hasFile('image')) {
                $book->image = NULL;
                $book->image_name = $originalName;
            }
            $book->published = $request->published;
            $book->publisher = $request->publisher;
            $book->save();

            if($book) {
                DB::commit();
                return redirect()->route('book.index');
            }
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController update function.");
            Log::error(Config::get('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);
        }
    }

    /**
        * @Author : Developer
        * @Desc   : Remove the specified resource from storage.
        * @Input  : \Illuminate\Http\Request $request, $id
        * @Output : \Illuminate\Http\Response
        * @Date   : 22-Oct-2023
    */

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $book = Book::find($id);

            if(!empty($book->image_name)) {
                if(Storage::exists(Config::get('constant.BOOKS_IMAGE_PATH').$book->image_name)){
                    Storage::delete(Config::get('constant.BOOKS_IMAGE_PATH').$book->image_name);
                }
            }

            $book->delete();

            if($book) {
                DB::commit();
                return true;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController destroy function.");
            Log::error(Config::get('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);
        }    
    }

}
