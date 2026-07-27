<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Unit;
use App\Models\Setting\Car;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\User;

class WarehouseReturn extends Model
{
    protected $table = 'warehouse_returns';

    protected $fillable = [
        'return_number',
        'warehouse_item_id',
        'pre_list_id',
        'unit_id',
        'currency_id',
        'car_id',
        'billno',
        'return_date',
        'supplier_account_id',
        'account_id',
        'quantity',
        'unit_price',
        'total',
        'paid_amount',
        'remaining_amount',
        'reason',
        'status',
        'user_id',
        'user_name',
    ];

    protected $casts = [
        'return_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'unit_price_vat' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'status' => 'integer',
    ];

    // =========================================
    // STATUS CONSTANTS
    // =========================================
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_PROCESSED = 3;

    const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_PROCESSED => 'Processed',
    ];

    // =========================================
    // RELATIONSHIPS
    // =========================================

    /**
     * Get the warehouse item associated with the return
     */
    public function warehouseItem()
    {
        return $this->belongsTo(WarehouseItem::class, 'warehouse_item_id');
    }

    /**
     * Get the pre-list item associated with the return
     */
    public function preList()
    {
        return $this->belongsTo(BuyPreList::class, 'pre_list_id');
    }

    /**
     * Get the unit associated with the return
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the currency associated with the return
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

     /**
     * Get the car associated with the return
     */
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    /**
     * Get the supplier account associated with the return
     */
    public function supplier()
    {
        return $this->belongsTo(Account::class, 'supplier_account_id');
    }

    /**
     * Get the user who created the return
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Generate return number
     */
    public static function generateReturnNumber()
    {
        $prefix = 'RET-' . date('Y-m-');
        $last = self::where('return_number', 'LIKE', $prefix . '%')
            ->orderBy('return_number', 'desc')
            ->first();
        
        $lastNum = $last ? intval(substr($last->return_number, -4)) : 0;
        return $prefix . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate total with tax
     */
    public function calculateTotal()
    {
        $subtotal = $this->quantity * $this->unit_price;
        $taxAmount = $subtotal * ($this->tax_percentage / 100);
        return $subtotal + $taxAmount;
    }

    /**
     * Update remaining amount
     */
    public function updateRemaining()
    {
        $this->remaining_amount = $this->total - $this->paid_amount;
        $this->save();
        return $this->remaining_amount;
    }

    /**
     * Check if return can be edited
     */
    public function isEditable()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if return can be deleted
     */
    public function isDeletable()
    {
        return $this->status === self::STATUS_PENDING;
    }
}