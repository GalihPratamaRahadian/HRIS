<?php

namespace App\Console;

use App\Models\EmployeeLeaveQuota;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\SendReminderForAttend',
		'App\Console\Commands\SendReminderTommorowIsOffDay',
		'App\Console\Commands\ChangeEmployeeShiftSchedule',
		'App\Console\Commands\AutoFillAttendanceClockOut',
		'App\Console\Commands\AutoFillNotAttend',
		'App\Console\Commands\SetEmployeeOffDay',
		'App\Console\Commands\SetClockOut',
		'App\Console\Commands\SendErrorLogToDeveloper',
		'App\Console\Commands\SendSecondNotificationForAttendance',
		'App\Console\Commands\SendHaventFilledAttendanceNotification',
		'App\Console\Commands\CheckCourseDeadline',
		'App\Console\Commands\DeleteOldPhotos',
		'App\Console\Commands\SendReminderForClockOut',
		'App\Console\Commands\CountEmployeeLeaveQuotaBalance',
		'App\Console\Commands\Helper\RemoveDoubleAttendances',
		'App\Console\Commands\SendAnnouncements',
		'App\Console\Commands\SendPayrolls',
		'App\Console\Commands\SaveTodayShiftResume',
		'App\Console\Commands\AttendFromFaceTerminalLog',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		// Ubah jadwal shift
		$schedule->command('attendance:change_employee_shift_schedule')->everyMinute();

		// Kirim pengingat untuk isi kehadiran
		$schedule->command('attendance:send_reminder_for_attend')->dailyAt('06:00');

		// Kirim pengingat bahwa besok libur
		$schedule->command('attendance:send_reminder_tomorrow_is_off_day')->dailyAt('12:00');
		$schedule->command('attendance:send_reminder_tomorrow_is_off_day')->dailyAt('20:00');

		// ClockOut Otomatis
		$schedule->command('attendance:auto_fill_clock_out')->dailyAt('23:50');

		// Isi ketidakhadiran
		// $schedule->command('attendance:set_clock_out')->dailyAt('23:30');
		$schedule->command('attendance:auto_fill_not_attend')->dailyAt('23:50');

		// Isi Libur
		$schedule->command('attendance:set_employee_off_day')->everyFiveMinutes();

		// Kirim Error
		$schedule->command('app:send_errors')->everyMinute();

		// Kirim notifikasi kedua untuk karyawan yg belum isi kehadiran
		$schedule->command('attendance:send_second_notification')->everyMinute();

		// Kirim notifikasi berulang jika karyawan belum isi kehadiran
		$schedule->command('attendance:send_havent_filled_attendance_notification')->everyFifteenMinutes();

		// Cek deadline course
		$schedule->command('course:check_deadline')->daily();

		// Delete Old Photo
		$schedule->command('app:delete_old_photos')->daily();

		// Count Employee Leave Quota Balance
		$schedule->command('app:count_employee_leave_quota_balance')->daily();

		// Send Reminder Clock Out
		$schedule->command('attendance:send_reminder_for_clock_out')->everyMinute();

		// Untuk ngehapus absensi yg double
		$schedule->command('helper:remove_double_attendances')->hourly();

		// Untuk Kirim Pengumuman
		$schedule->command('announcement:send')->everyMinute();

		// Untuk Kirim Payroll
		$schedule->command('payroll:send')->everyMinute();

		// Simpan rekap shift
		$schedule->command('app:save_today_shift_resume')->dailyAt('00:10');

		// Sinkronasi log dengan absensi
		$schedule->command('app:attend_from_face_terminal_log')->everyFiveMinutes();

        // Reset Jatah Cuti menjadi 12 hari setiap tahun
        $schedule->command('app:reset_quota')->daily();
	}

	/**
	 * Register the commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		$this->load(__DIR__.'/Commands');

		require base_path('routes/console.php');
	}
}
