<?php

namespace App\Models;

use App\Traits\ScopeableByCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\SupplierProduct;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Vinkla\Hashids\Facades\Hashids;

class Supplier extends Model
{
    use HasFactory, LogsActivity;
    use SoftDeletes;

    use ScopeableByCompany;

    protected $fillable = [
        'code',
        'name',
        'contact',
        'address',
        'city',
        'payment_term_type',
        'payment_term',
        'taxable_enterprise',
        'tax_id',
        'remarks',
        'status'
    ];

    protected static $logAttributes = ['code', 'name', 'payment_term_type', 'payment_term', 'contact', 'address', 'city', 'tax_id', 'taxable_enterprise', 'remarks', 'status'];

    protected static $logOnlyDirty = true;

    protected $hidden = [
        'id',
        'company_id',
        'user_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot'
    ];

    protected $appends = ['hId', 'selectedProducts', 'mainProducts'];

    public function getHIdAttribute() : string
    {
        return HashIds::encode($this->attributes['id']);
    }

    public function getSelectedProductsAttribute() : string
    {
        return $this->supplierProducts()->get()->pluck('hId');
    }

    public function getMainProductsAttribute() : string
    {
        return $this->supplierProducts()->where('main_product', '=', 1)->get()->pluck('hId');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
