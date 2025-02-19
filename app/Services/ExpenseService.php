<?php

namespace App\Services;

use App\Models\Expense;

interface ExpenseService
{
    public function create(
        int $company_id,
        int $branch_id,
        int $expense_group_id,
        ?int $cash_id = null,
        string $code,
        string $date,
        string $payment_term_type,
        int $amount,
        int $amount_owed,
        ?string $remarks = null,
        int $posted,
    ): ?Expense;

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
        int $branch_id,
        int $expense_group_id,
        ?int $cash_id = null,
        string $code,
        string $date,
        string $payment_term_type,
        int $amount,
        int $amount_owed,
        ?string $remarks = null,
        int $posted,
    ): ?Expense;

    public function delete(int $id): bool;

    public function generateUniqueCode(int $companyId): string;

    public function isUniqueCode(string $code, int $companyId, ?int $exceptId = null): bool;
}