<?php

namespace App\Jobs;

use App\Services\Audit\AuditLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $logData
    )
    {
    }

    public $tries = 3;
    public $backoff = 5;

    public function handle()
    {
        AuditLogger::logToDatabase($this->logData);
    }
}

