<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          =>  'required|string|max:15',
            'category_id'   =>  'required',
            'description'   =>  'nullable|string',
            'image'         =>  'nullable|image',
            'barcode'       =>  'nullable|string|max:50|unique:products',
            'price'         =>  'required|regex:/^\d+(\.\d{1,2})?$/',
            'quantity'      =>  'required|string|max:255',
            'status'        =>  'required|boolean',
            'product_vendor_id' => 'nullable|exists:product_vendors,id'

        ];
    }

    public function messages()
    {
        return [
            'category_id.required'  =>  'Category is required!',
        ];
    }
}
