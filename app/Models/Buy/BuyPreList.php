<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Branch;
use App\Models\Setting\Category;

class BuyPreList extends Model
{
    protected $table = 'bought_item_pre_lists';
    protected $fillable=['name','category_id'];

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
