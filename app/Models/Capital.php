<?php

namespace App\Models;

use App\Models\Cash;
use App\Models\Company;
use App\Models\Investor;

use App\Models\CapitalGroup;
use Spatie\Activitylog\LogOptions;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Capital extends Model
{
    use HasFactory, LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'investor_id',
        'group_id',
        'cash_id',
        'ref_number',
        'date',
        'capital_status',
        'amount',
        'remarks',
    ];

    protected static $logAttributes = [
        'investor_id',
        'group_id',
        'cash_id',
        'ref_number',
        'date',
        'capital_status',
        'amount',
        'remarks',
    ];

    protected static $logOnlyDirty = true;

    protected $hidden = [];

    public function hId() : Attribute
    {
        return Attribute::make(
            get: fn () => HashIds::encode($this->attributes['id'])
        );
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function group()
    {
        return $this->belongsTo(CapitalGroup::class);
    }

    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = Auth::check();
            if ($user) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            $user = Auth::check();
            if ($user) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            $user = Auth::check();
            if ($user) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }
}