<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReminderForAttend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send_reminder_for_attend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat untuk isi kehadiran';

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
        return \App\Models\Attendance::sendReminderForAttend();
    }
}
