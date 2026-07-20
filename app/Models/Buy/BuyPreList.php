<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Category;
use App\Models\Setting\Account;

class BuyPreList extends Model
{
    protected $table = 'bought_item_pre_lists';
    protected $fillable=['name','category_id','supplier_id','unit_id','unit_name'];

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function supplier()
    {
        return $this->belongsTo(Account::class, 'supplier_id','id');
    }
}
