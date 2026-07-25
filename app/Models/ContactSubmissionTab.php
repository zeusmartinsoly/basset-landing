<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactSubmissionTab extends Model
{
    /** @use HasFactory<\Database\Factories\ContactSubmissionTabFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /** @return HasMany<ContactSubmission, $this> */
    public function submissions(): HasMany
    {
        return $this->hasMany(ContactSubmission::class);
    }
}
