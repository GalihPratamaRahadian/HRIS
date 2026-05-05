<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CountEmployeeLeaveQuotaBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:count_employee_leave_quota_balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghitung saldo cuti karyawan';

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
