<?php

namespace App\Models\Package;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackagePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'currency',
        'amount',
        'is_active',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}

