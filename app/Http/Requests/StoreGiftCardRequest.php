<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGiftCardRequest extends FormRequest
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
            'title'         =>  'required|min:3|max:15',
            'user_email'    =>  'nullable|email|max:50',
            'barcode'       =>  'required|min:3|max:20',
            'discount_type' =>  'required',
            'discount'      =>  'required',
            'start_date'    =>  'required',
            'expire_date'   =>  'required',
            'min_purchase' => 'nullable'
        ];
    }
}
