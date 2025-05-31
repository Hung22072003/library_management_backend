<?php

namespace App\Http\Requests;

use App\Traits\APIResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BookStoreRequest extends FormRequest
{
    use APIResponse;
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publication_year' => 'nullable|integer',
            'isbn13' => 'nullable|string|unique:books,isbn13',
            'isbn10' => 'nullable|string|unique:books,isbn10',
            'language' => 'nullable|string|max:50',
            'authors' => 'nullable|string|max:255',
            'num_pages' => 'nullable|integer',
            'total_copies' => 'nullable|integer',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'array',
            'categories.*' => 'string|exists:categories,id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        return $this->responseErrorValidate($errors, $validator);
    }
}
