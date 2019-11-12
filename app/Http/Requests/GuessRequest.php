<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GuessRequest extends ApiRequest
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
            'answer'        => 'required',
            'time'          => 'required|integer',
            'is_correct'    => 'required|boolean',
            'last_score'    => Rule::requiredIf(function () {
                return !Auth::guard('api')->user();
            })
        ];
    }
}
