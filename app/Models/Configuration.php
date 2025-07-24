<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Imports the User model

class Configuration extends Model
{
    use HasFactory;

    protected $table = 'configuration'; 


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'parameter',
        'value',
    ];

    /**
     * Get the user that owns the password.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}