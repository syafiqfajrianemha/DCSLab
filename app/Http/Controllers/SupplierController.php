<?php

namespace App\Http\Controllers;

use App\Enums\PaymentTerm;
use App\Enums\PaymentTermType;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Services\SupplierService;
use Illuminate\Http\Request;

use Vinkla\Hashids\Facades\Hashids;

class SupplierController extends BaseController
{
    private $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->supplierService = $supplierService;
    }

    public function read(SupplierRequest $supplierRequest)
    {
        $request = $supplierRequest->validated();

        $search = $request['search'];
        $paginate = $request['paginate'];
        $page = array_key_exists('page', $request) ? abs($request['page']) : 1;
        $perPage = array_key_exists('perPage', $request) ? abs($request['perPage']) : 10;

        $companyId = $request['company_id'];

        $result = $this->supplierService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            page: $page,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = SupplierResource::collection($result);

            return $response;
        }
    }

    public function store(SupplierRequest $supplierRequest)
    {
        $request = $supplierRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];

        $code = $request['code'];
        if ($code == config('const.DEFAULT.KEYWORDS.AUTO')) {
            do {
                $code = $this->supplierService->generateUniqueCode($company_id);
            } while (!$this->supplierService->isUniqueCode($code, $company_id));
        } else {
            if (!$this->supplierService->isUniqueCode($code, $company_id)) {
                return response()->error([
                    'code' => [trans('rules.unique_code')]
                ], 422);
            }
        }

        $taxable_enterprise = array_key_exists('taxable_enterprise', $request);

        $poc = [
            'name' => $request['poc_name'],
            'email' => $request['email'], 
        ];

        $supplier_products = [];
        if (!empty($request['productIds'])) {
            for ($i = 0; $i < count($request['productIds']); $i++) {
                array_push($supplier_products, array (
                    'company_id' => $company_id,
                    'product_id' => Hashids::decode($request['productIds'][$i])[0],
                    'main_product' => in_array($request['productIds'][$i], $request['mainProducts']) ? 1 : 0
                ));
            }
        }

        $contact = array_key_exists('contact', $request) ? $request['contact'] : '';

        $result = $this->supplierService->create(
            $company_id,
            $code,
            $request['name'],
            $request['payment_term_type'],
            $request['payment_term'],
            $request['contact'],
            $request['address'],
            $request['city'],
            $taxable_enterprise,
            $request['tax_id'],
            $request['remarks'],
            $request['status'],
            $poc,
            $supplier_products
        );

        return is_null($result) ? response()->error() : response()->success();
    }

    public function update($id, SupplierRequest $supplierRequest)
    {
        $request = $supplierRequest->validated();
        $company_id = Hashids::decode($request['company_id'])[0];

        $code = $request['code'];
        if ($code == config('const.DEFAULT.KEYWORDS.AUTO')) {
            do {
                $code = $this->supplierService->generateUniqueCode($company_id);
            } while (!$this->supplierService->isUniqueCode($code, $company_id, $id));
        } else {
            if (!$this->supplierService->isUniqueCode($code, $company_id, $id)) {
                return response()->error([
                    'code' => [trans('rules.unique_code')]
                ], 422);
            }
        }

        $taxable_enterprise = array_key_exists('taxable_enterprise', $request);

        $poc = [

        ];

        $products = [

        ];

        $result = $this->supplierService->update(
            $id,
            $company_id,
            $request['code'],
            $request['name'],
            $request['payment_term_type'],
            $request['payment_term'],
            $request['contact'],
            $request['address'],
            $request['city'],
            $taxable_enterprise,
            $request['tax_id'],
            $request['remarks'],
            $request['status'],
            $poc,
            $products
        );

        return is_null($result) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->supplierService->delete($id);

        return !$result ? response()->error() : response()->success();
    }

    public function getPaymentTermType()
    {
        return [
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.pia', 'code' => PaymentTermType::PAYMENT_IN_ADVANCE->name],
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.net', 'code' => PaymentTermType::X_DAYS_AFTER_INVOICE->name],
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.eom', 'code' => PaymentTermType::END_OF_MONTH->name],
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.cod', 'code' => PaymentTermType::CASH_ON_DELIVERY->name],
            ['name' => 'components.dropdown.values.paymentTermTypeDDL.cnd', 'code' => PaymentTermType::CASH_ON_NEXT_DELIVERY->name]
        ];
    }
}
