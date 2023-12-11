<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Lumen\Auth\Authorizable;

class Address extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $table = 'addresses';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    protected $fillable = [
        "zip_code", "complement", "number", "street", "neighborhood", "city", "uf", "id", "user_id", "is_complete"
    ];

    protected $casts = [
        'is_complete' => 'boolean',
    ];

    protected $hidden = [];
}
