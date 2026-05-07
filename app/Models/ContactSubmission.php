<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\ContactSubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'prefers_whatsapp',
        'contacted',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'prefers_whatsapp' => 'boolean',
            'contacted' => 'boolean',
        ];
    }
}
