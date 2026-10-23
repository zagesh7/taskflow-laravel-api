<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProvisionTaskFlow extends Command
{
    protected $signature = 'taskflow:provision-demo';

    protected $description = 'Run database migrations and seed the demo workspace';

    public function handle(): int
    {
        if ($this->call('migrate', ['--force' => true]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        return $this->call('db:seed', ['--force' => true]) === self::SUCCESS
            ? self::SUCCESS
            : self::FAILURE;
    }
}
