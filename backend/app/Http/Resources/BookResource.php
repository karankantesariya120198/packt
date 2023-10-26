<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   
        if(!empty($this->image)) {
            $imageExists = Storage::exists(Config::get('constant.api.BOOKS_IMAGE_PATH').$this->image);
            if($imageExists) {
                $image = Storage::url(Config::get('constant.api.BOOKS_IMAGE_PATH').$this->image, 'public');
            } else {
                $image = "";
            }
        } else {
            $image = "";
        }

        return [
            "id" => $this->id,
            "title" => $this->title,
            "author" => $this->author,
            "genre" => $this->genre,
            "description" => $this->description,
            "isbn" => $this->isbn,
            "image" => $image,
            "published" => $this->published,
            "publisher" => $this->publisher
        ];
    }
}
