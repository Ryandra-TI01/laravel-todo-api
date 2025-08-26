<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'is_completed', 'due_date', 'user_id','priority'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
