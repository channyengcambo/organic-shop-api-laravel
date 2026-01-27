<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'method',
        'url',
        'route',
        'ip_address',
        'user_agent',
        'request_payload',
        'response_payload',
        'status_code',
        'duration_ms',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];
}
