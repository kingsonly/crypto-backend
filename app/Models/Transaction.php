<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function investment()
    {
        return $this->belongsTo(Investments::class);
    }

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class);
    }

    public function earning()
    {
        return $this->belongsTo(Earning::class);
    }
}
