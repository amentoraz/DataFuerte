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
        'file_path',
        'file_name',
        'file_size',
        'file_mime',
        'element_type_id',
        'parent',
        'iterations',
    ];

    protected $appends = ['is_file', 'file_url'];

    /**
     * Get the URL to access the encrypted file
     */
    public function getFileUrlAttribute()
    {
        if ($this->element_type_id === 3 && $this->file_path) {
            return route('element.file.download', $this->uuid);
        }
        return null;
    }

    /**
     * Check if the element is a file
     */
    public function getIsFileAttribute()
    {
        return $this->element_type_id === 3;
    }

    /**
     * Get the user that owns the password.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}