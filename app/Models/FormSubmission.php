<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = [
        'type',
        'name',
        'email',
        'phone',
        'company',
        'message',
        'subject',
        'ip_address',
        'user_agent',
        'status',
        'responded_at',
        'response_message',
        'is_read',
        'is_important'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_RESPONDED = 'responded';
    const STATUS_ARCHIVED = 'archived';

    public function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_RESPONDED => 'Responded',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }
}
