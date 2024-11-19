<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;


class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    static array $extansions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'mp3', 'wav', 'mp4','mkv',
        'doc', 'docx', 'pdf', 'csv', 'xls', 'xlsx',
        'zip', '7zip','txt'
    ];
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
            'body' => ['nullable', 'string'],
            'attachments' => [
                'nullable', 'array', 'max:30',
                function ($attribute, $value, $fail) {
                    $totalSize = collect($value)->sum(fn (UploadedFile $file) => $file->getSize());
                    if($totalSize >1*1024*1024*1024){
                        $fail("Total size must not exceed 1GB");

                    }
                }
            ],
            'attachments.*' => [
                'file',
                File::types(self::$extansions)->max(500 * 1024 *1024)
            ],
            'user_id' => ['numeric']
        ];
    }
    public function prepareForValidation()
    {

        $this->merge([
            'user_id' => Auth::user()->id,
            'body' => $this->input('body') ?: ''
        ]);
    }
    public function messages()
    {
        return [
            'attachments.*.file' => 'Each attachment must be a file',
            'attachments.*.mimes'=>'Invalid file type for attachment',
            'attachments.*.max'=>'Each file must not exceed 500MB'
        ];
    }
}
