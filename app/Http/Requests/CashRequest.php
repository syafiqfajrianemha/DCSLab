<?php

namespace App\Http\Requests;

use App\Rules\uniqueCode;
use App\Enums\ActiveStatus;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class CashRequest extends FormRequest
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
            'is_bank' => 'nullable',
        ];

        $currentRouteMethod = $this->route()->getActionMethod();
        switch($currentRouteMethod) {
            case 'store':
                $rules_store = [
                    'company_id' => ['required', 'bail'],
                    'code' => ['required', 'max:255', new uniqueCode(table: 'cashes', companyId: $companyId)],
                    'name' => 'required|max:255',
                    'status' => [new Enum(ActiveStatus::class)]
                ];
                return array_merge($rules_store, $nullableArr);
            case 'update':
                $rules_update = [
                    'company_id' => ['required', 'bail'],
                    'code' => new uniqueCode(table: 'cashes', companyId: $companyId, exceptId: $this->route('id')),
                    'name' => 'required|max:255',
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
