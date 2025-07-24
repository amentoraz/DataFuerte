<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Imports the User model

class Element extends Model
{
    use HasFactory;

    protected $table = 'elements'; 
    protected $keyType = 'string'; 
    protected $primaryKey = 'uuid'; 
    public $incrementing = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'key',
        'content',
        'iv',
        'salt',
        'hmac',
    ];

    /**
     * Get the user that owns the password.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}