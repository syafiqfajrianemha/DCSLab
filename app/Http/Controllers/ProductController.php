<?php

namespace App\Http\Controllers;

use App\Enums\ProductType;
use App\Services\ProductService;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends BaseController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->productService = $productService;
    }

    public function readProducts(ProductRequest $productRequest)
    {
        $request = $productRequest->validated();

        $search = $request['search'];
        $paginate = $request['paginate'];
        $page = array_key_exists('page', $request) ? abs($request['page']) : 1;
        $perPage = array_key_exists('perPage', $request) ? abs($request['perPage']) : 10;

        $companyId = $request['company_id'];

        $result = $this->productService->read(
            companyId: $companyId,
            isProduct: true,
            isService: false,
            search: $search,
            paginate: $paginate,
            page: $page,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = ProductResource::collection($result);

            return $response;
        }
    }

    public function readServices(ProductRequest $productRequest)
    {
        $request = $productRequest->validated();

        $search = $request['search'];
        $paginate = $request['paginate'];
        $page = array_key_exists('page', $request) ? abs($request['page']) : 1;
        $perPage = array_key_exists('perPage', $request) ? abs($request['perPage']) : 10;

        $companyId = $request['company_id'];

        $result = $this->productService->read(
                    companyId: $companyId, 
                    isProduct: false, 
                    isService: true, 
                    search: $search, 
                    paginate: $paginate, 
                    page: $page,
                    perPage: $perPage);
        
        if (is_null($result)) {
            return response()->error();
        } else {
            $response = ProductResource::collection($result);

            return $response;
        }
    }

    public function store(ProductRequest $productRequest)
    {   
        $request = $productRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];

        $code = $request['code'];
        if ($code == config('const.DEFAULT.KEYWORDS.AUTO')) {
            do {
                $code = $this->productService->generateUniqueCodeForProduct($company_id);
            } while (!$this->productService->isUniqueCodeForProduct($code, $company_id));
        } else {
            if (!$this->productService->isUniqueCodeForProduct($code, $company_id)) {
                return response()->error([
                    'code' => [trans('rules.unique_code')]
                ], 422);
            }
        }
        
        $product_group_id = array_key_exists('product_group_id', $request) ? Hashids::decode($request['product_group_id'])[0]:null;
        $brand_id = Hashids::decode($request['brand_id'])[0];
        $name = $request['name'];

        $remarks = $request['remarks'];
        $point = $request['point'];

        $use_serial_number = array_key_exists('use_serial_number', $request) ? true : false;
        $has_expiry_date = array_key_exists('has_expiry_date', $request) ? true : false;

        $product_type = $request['product_type'];
        $status = $request['status'];

        $taxable_supply = array_key_exists('taxable_supply', $request) ? boolVal($request['taxable_supply']) : false;
        $standard_rated_supply = array_key_exists('standard_rated_supply', $request) ? intVal($request['standard_rated_supply']) : 0;
        $price_include_vat = array_key_exists('price_include_vat', $request) ? boolVal($request['price_include_vat']) : false;

        $product_units = [];
        $count_unit = count($request['unit_id']);
        for ($i = 0; $i < $count_unit; $i++) {
            $is_base = $request['is_base'][$i] == '1' ? true : false;

            $is_primary_unit = $request['is_primary_unit'][$i] == '1' ? true : false;

            array_push($product_units, array (
                'company_id' => $company_id,
                'code' => $code,
                'unit_id' => Hashids::decode($request['unit_id'][$i])[0],
                'conv_value' => $request['conv_value'][$i],
                'is_base' => $is_base,
                'is_primary_unit' => $is_primary_unit,
                'remarks' => ''
            ));
        }

        $result = $this->productService->create(
            $company_id,
            $code, 
            $product_group_id,
            $brand_id,
            $name,
            $taxable_supply,
            $standard_rated_supply,
            $price_include_vat,
            $remarks,
            $point,
            $use_serial_number,
            $has_expiry_date,
            $product_type,
            $status,
            $product_units
        );

        return is_null($result) ? response()->error() : response()->success();
    }

    public function update($id, ProductRequest $productRequest)
    {
        $request = $productRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];

        $code = $request['code'];
        if ($code == config('const.DEFAULT.KEYWORDS.AUTO')) {
            do {
                $code = $this->productService->generateUniqueCodeForProduct($company_id);
            } while (!$this->productService->isUniqueCodeForProduct($code, $company_id, $id));
        } else {
            if (!$this->productService->isUniqueCodeForProduct($code, $company_id, $id)) {
                return response()->error([
                    'code' => [trans('rules.unique_code')]
                ], 422);
            }
        }

        $product_group_id = array_key_exists('product_group_id', $request) ? Hashids::decode($request['product_group_id'])[0]:null;
        $brand_id = Hashids::decode($request['brand_id'])[0];
        $name = $request['name'];
        $taxable_supply = array_key_exists('taxable_supply', $request) ? $request['taxable_supply']:0;
        $standard_rated_supply = array_key_exists('standard_rated_supply', $request) ? $request['standard_rated_supply']:0;
        $price_include_vat = array_key_exists('price_include_vat', $request) ? $request['price_include_vat']:0;
        $remarks = $request['remarks'];
        $point = $request['point'];
        $use_serial_number = array_key_exists('use_serial_number', $request) ? $request['use_serial_number']:0;
        $has_expiry_date = $request['has_expiry_date'];
        $product_type = $request['product_type'];
        $status = $request['status'];

        $product_units = [];
        $count_unit = count($request['unit_id']);
        for ($i = 0; $i < $count_unit; $i++) {
            $product_unit_id = $request['product_units_hId'][$i] == 0 ? 0 : Hashids::decode($request['product_units_hId'][$i])[0];
            $code = $request['product_units_code'][$i];

            $is_base = is_null($request['is_base'][$i]) ? 0 : $request['is_base'][$i];
            $is_base = is_numeric($is_base) ? $is_base : 0;

            $is_primary_unit = is_null($request['is_primary_unit'][$i]) ? 0 : $request['is_primary_unit'][$i];
            $is_primary_unit = is_numeric($is_primary_unit) ? $is_primary_unit : 0;

            $use_serial_number = is_null($use_serial_number) ? 0 : $use_serial_number;
            $use_serial_number = is_numeric($use_serial_number) ? $use_serial_number : 0;
            
            $has_expiry_date = is_null($has_expiry_date) ? 0 : $has_expiry_date;
            $has_expiry_date = is_numeric($has_expiry_date) ? $has_expiry_date : 0;

            array_push($product_units, array (
                'id' => $product_unit_id,
                'company_id' => $company_id,
                'product_id' => $id,             
                'code' => $code,
                'unit_id' => Hashids::decode($request['unit_id'][$i])[0],
                'conv_value' => $request['conv_value'][$i],
                'is_base' => $is_base,
                'is_primary_unit' => $is_primary_unit,
                'remarks' => $request['remarks']
            ));
        }

        $product = $this->productService->update(
            $id,
            $company_id,
            $code, 
            $product_group_id,
            $brand_id,
            $name,
            $taxable_supply,
            $standard_rated_supply,
            $price_include_vat,    
            $remarks,
            $point,
            $use_serial_number,
            $has_expiry_date,
            $product_type,
            $status,
            $product_units
        );

        return is_null($product) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->productService->delete($id);

        return !$result ? response()->error() : response()->success();
    }

    public function getProductType(Request $request)
    {
        if ($request->has('type') && $request['type'] == 'products') {
            return [
                ['name' => 'components.dropdown.values.productTypeDDL.raw', 'code' => ProductType::RAW_MATERIAL->name],
                ['name' => 'components.dropdown.values.productTypeDDL.wip', 'code' => ProductType::WORK_IN_PROGRESS->name],
                ['name' => 'components.dropdown.values.productTypeDDL.fg', 'code' => ProductType::FINISHED_GOODS->name],
            ];
        } else if ($request->has('type') && $request['type'] == 'service'){
            return [
                ['name' => 'components.dropdown.values.productTypeDDL.svc', 'code' => ProductType::SERVICE->name]
            ];
        } else {
            return [
                ['name' => 'components.dropdown.values.productTypeDDL.raw', 'code' => ProductType::RAW_MATERIAL->name],
                ['name' => 'components.dropdown.values.productTypeDDL.wip', 'code' => ProductType::WORK_IN_PROGRESS->name],
                ['name' => 'components.dropdown.values.productTypeDDL.fg', 'code' => ProductType::FINISHED_GOODS->name],
                ['name' => 'components.dropdown.values.productTypeDDL.svc', 'code' => ProductType::SERVICE->name]
            ];
        }
    }
}
