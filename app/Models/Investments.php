<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investments extends Model
{
    use HasFactory;

    public function earning()
    {
        return $this->hasMany(Earning::class, 'investment_id');
    }

    public function package()
    {
        return $this->hasOne(Package::class, 'id', 'package_id');
    }
}
