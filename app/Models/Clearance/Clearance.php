<?php

namespace App\Models\Clearance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;

class Clearance extends Model
{
    use HasFactory;

    protected $casts = [
        'bill_numbers' => 'array',  // Automatically cast to an array
    ];

    protected $fillable = [
        'type',
        'company_account_id',
        'customer_account_id',
        'total',
        'currency_id',
        'branch_id',
        'details',
        'bill_numbers',
        'dates',
        'clearedBy',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'company_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'customer_account_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

}
