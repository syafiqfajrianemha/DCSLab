<?php

namespace App\Http\Requests;

use App\Rules\uniqueCode;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $companyId = $this->has('company_id') ? Hashids::decode($this['company_id'])[0]:null;

        $nullableArr = [
            'address' => 'nullable',
            'city' => 'nullable',
            'contact' => 'nullable',
            'remarks' => 'nullable',
            'is_member' => 'nullable',
            'zone' => 'nullable',
            'tax_id' => 'nullable',
        ];

        $currentRouteMethod = $this->route()->getActionMethod();
        switch($currentRouteMethod) {
            case 'store':
                $rules_store = [
                    'customer_group_id' => ['required', 'bail'],
                    'code' => ['required', 'max:255'],
                    'name' => 'required|min:3|max:255',
                    'max_open_invoice' => 'required|integer|digits_between:1,11',
                    'max_outstanding_invoice' => 'required|numeric|min:0|max:999999999999999',
                    'max_invoice_age' => 'required|integer|digits_between:1,11',
                    'payment_term' => 'required|integer|digits_between:1,11',
                    'status' => [new Enum(ActiveStatus::class)]
                ];
                return array_merge($rules_store, $nullableArr);
            case 'update':
                $rules_update = [
                    'customer_group_id' => ['required', 'bail'],
                    'code' => ['required', 'max:255'],
                    'name' => 'required|min:3|max:255',
                    'max_open_invoice' => 'required|integer|digits_between:1,11',
                    'max_outstanding_invoice' => 'required|numeric|min:0|max:999999999999999',
                    'max_invoice_age' => 'required|integer|digits_between:1,11',
                    'payment_term' => 'required|integer|digits_between:1,11',
                    'status' => [new Enum(ActiveStatus::class)]
                ];
                return array_merge($rules_update, $nullableArr);
            default:
                return [
                    '' => 'required'
                ];
        }
    }

    public function attributes()
    {
        return [
            'company_id' => trans('validation_attributes.company'),
        ];
    }
}
