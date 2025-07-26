<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Imports the User model

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs'; 


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'loggable_type',
        'loggable_id',
        'data',
        'ip_address',
        'user_agent',
        'severity',
    ];

    /**
     * Get the user that owns the password.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}