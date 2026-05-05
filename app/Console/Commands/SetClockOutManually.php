<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;

class SetClockOutManually extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:set_clock_out_manually';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Clock Out Manual';

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
        $dateStart = $this->ask('Masukkan Tgl Awal');
        $dateEnd = $this->ask('Masukkan Tgl Akhir');
        $clockOut = $this->ask('Masukkan Jam Keluar');

        $attendances = Attendance::where('date', '>=', $dateStart)
                           ->where('date', '<=', $dateEnd)
                           ->where('type', Attendance::TYPE_HADIR)
                           ->get();
        
        foreach($attendances as $attendance)
        {
            if(empty($attendance->clock_out) || empty($attendance->clock_out_at)) {
                $attendance->update([
                    'clock_out'     => $clockOut,
                    'clock_out_at'  => $attendance->date.' '.$clockOut,
                ]);
            }
        }
    }
}
