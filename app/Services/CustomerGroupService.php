<?php

namespace App\Services;

use App\Models\CustomerGroup;
use Ramsey\Uuid\Type\Decimal;

interface CustomerGroupService
{
    public function create(
        int $company_id,
        string $code,
        string $name,
        int $max_open_invoice,
        int $max_outstanding_invoice,
        int $max_invoice_age,
        int $payment_term,
        int $selling_point,
        string $selling_point_multiple,
        ?int $sell_at_cost = null,
        int $price_markup_percent,
        int $price_markup_nominal,
        int $price_markdown_percent,
        int $price_markdown_nominal,
        int $round_on,
        ?int $round_digit = null,
        ?int $remarks = null,
        ?int $cash_id = null,
    ): ?CustomerGroup;

    public function read(
        int $companyId,
        string $search = '',
        bool $paginate = true,
        int $page,
        int $perPage = 10,
        bool $useCache = true
    );

    public function update(
        int $id,
        int $company_id,
        string $code,
        string $name,
        int $max_open_invoice,
        int $max_outstanding_invoice,
        int $max_invoice_age,
        int $payment_term,
        int $selling_point,
        string $selling_point_multiple,
        ?int $sell_at_cost = null,
        int $price_markup_percent,
        int $price_markup_nominal,
        int $price_markdown_percent,
        int $price_markdown_nominal,
        int $round_on,
        ?int $round_digit = null,
        ?int $remarks = null,
        ?int $cash_id = null,
    ): ?CustomerGroup;

    public function delete(int $id): bool;

    public function generateUniqueCode(int $companyId): string;

    public function isUniqueCode(string $code, int $companyId, ?int $exceptId = null): bool;
}