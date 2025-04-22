<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table='expenses';
    protected $fillable = [
        'name',
        'amount',
        'date',
        'group_id'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
