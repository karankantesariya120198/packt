<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'author' => 'required',
            'genre' => 'required',
            'description' => 'required',
            'isbn' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:2097152',
            'published' => 'required',
            'publisher' => 'required' 
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please enter book title.',
            'author.required' => 'Please enter book author.',
            'genre.required' => 'Please enter book genre.',
            'description.required' => 'Please enter book description.',
            'isbn.required' => 'Please enter book isbn.',
            'image.required' => 'Please select book image.',
            'image.mimes' => 'Please select provided extansion image.',
            'image.size' => 'Image size must be less than 2 MB.',
            'published.required' => 'Please select book published date.',
            'publisher.required' => 'Please enter book publisher.',
        ];
    }
}
