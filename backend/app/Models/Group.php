<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table='groups';

    protected $fillable = [
        'name'
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($group) {
            $group->expenses()->delete();
        });
    }
}
