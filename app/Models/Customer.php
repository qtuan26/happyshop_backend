<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'registration_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }
    public function cart()
    {
        return $this->hasOne(ShoppingCart::class, 'customer_id');
    }
}
