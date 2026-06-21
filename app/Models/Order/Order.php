<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Unit;
use App\Models\Setting\Category;
use App\Models\Setting\Account;

class Order extends Model
{
    protected $fillable = [
        "ord_num",
        'pre_list_id',
        'category_id',
        'supplier_id',
        'employee_id',
        'amount',
        'unit_id',
        'iby',
        'idate',
        'state',
        'done_year',
        'done_month',
        'done_day',
        'done_by',
        'times'
    ];

    protected $casts = [
        'idate' => 'date',
        // 'amount' => 'decimal:2',
    ];

    // Relationships
    public function preListRelation()
    {
        return $this->belongsTo(BuyPreList::class, 'pre_list_id');
    }

    public function unitRelation()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function employeeRelation()
    {
        return $this->belongsTo(Account::class, 'employee_id','id');
    }
    public function supplierRelation()
    {
        return $this->belongsTo(Account::class, 'supplier_id','id');
    }
    // Scopes
    public function scopePending($query)
    {
        return $query->where('state', 0);
    }

    public function scopeInProgress($query)
    {
        return $query->where('state', 1);
    }

    public function scopeCompleted($query)
    {
        return $query->where('state', 2);
    }

    public function scopeCancelled($query)
    {
        return $query->where('state', 3);
    }

    // Accessors
    public function getStateLabelAttribute()
    {
        $labels = [
            0 => 'Pending',
            1 => 'In Progress',
            2 => 'Completed',
            3 => 'Cancelled'
        ];
        return $labels[$this->state] ?? 'Unknown';
    }

    public function getStateBadgeAttribute()
    {
        $badges = [
            0 => '<span class="badge badge-warning">Pending</span>',
            1 => '<span class="badge badge-info">In Progress</span>',
            2 => '<span class="badge badge-success">Completed</span>',
            3 => '<span class="badge badge-danger">Cancelled</span>'
        ];
        return $badges[$this->state] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getFormattedDateAttribute()
    {
        return $this->idate ? \Carbon\Carbon::parse($this->idate)->format('Y/m/d') : '-';
    }
}