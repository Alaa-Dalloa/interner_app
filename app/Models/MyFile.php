<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyFile extends Model
{
    use HasFactory;
     protected $fillable = [
        'link',
        'status',
        'group_id',
        'file_name'
        
    ];
    public function group()
{
    return $this->belongsTo(Group::class);
}
}
