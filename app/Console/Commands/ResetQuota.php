<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetQuota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset_quota';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Quota';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \App\Models\EmployeeLeaveQuota::resetEmployeeLeaveQuotas2();
    }
}
