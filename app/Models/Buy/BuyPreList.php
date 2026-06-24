<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Branch;
use App\Models\Setting\Category;

class BuyPreList extends Model
{
    protected $table = 'bought_item_pre_lists';
    protected $fillable=['code','name','category_id','is_prev_barcode','times','image_path','barcode_path'];

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
