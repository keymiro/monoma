<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source',
        'owner',
        'created_by'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
