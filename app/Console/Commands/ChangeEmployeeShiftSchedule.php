<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChangeEmployeeShiftSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:change_employee_shift_schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengubah jadwal shift karyawan';

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
        \App\Models\EmployeeShiftChangeSchedule::changeEmployeeShiftSchedule();
    }
}
