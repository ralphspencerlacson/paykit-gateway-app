<?php

namespace App\Models\Package;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function prices()
    {
        return $this->hasMany(PackagePrice::class);
    }

    public function activePrice()
    {
        return $this->hasOne(PackagePrice::class)->where('is_active', true);
    }
}

