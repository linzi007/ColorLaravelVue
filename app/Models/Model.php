<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    public $timestamps = true;

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}