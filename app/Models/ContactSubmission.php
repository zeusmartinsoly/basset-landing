<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'contact_submission_tab_id',
    ];

    protected function casts(): array
    {
        return [
            'prefers_whatsapp' => 'boolean',
            'contacted' => 'boolean',
        ];
    }

    /** @return BelongsTo<ContactSubmissionTab, $this> */
    public function tab(): BelongsTo
    {
        return $this->belongsTo(ContactSubmissionTab::class, 'contact_submission_tab_id');
    }
}
