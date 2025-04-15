<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Member;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'products_id',
        'members_id',
        'users_id',
        'tanggal_penjualan',
        'total_barang',
        'total_harga',
        'customer_pay',
        'customer_return',
        'member_point_used',
        'total_harga_after_point',
        'created_at',
    ];

    protected $casts = [
        'products_id' => 'array',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'members_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'order_product', 'order_id', 'product_id');
    }



    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }


    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('qty'); 
    }

}
