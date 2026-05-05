<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\FaceTerminalLog;
use App\Models\Attendance;
use App\Models\StoreVisit;

class DeleteOldPhotos extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:delete_old_photos';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Hapus foto lama';

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
		$this->deleteFaceTerminalLogPhotos();
		$this->deleteAttendancePhotos();
		$this->deleteStoreVisitPhotos();
		$this->info('[v] Selesai');
	}


	public function minAgesInDays()
	{
		return env('LIMIT_AGE_PHOTO_STORAGE', 60); // days
	}


	public function deleteFaceTerminalLogPhotos()
	{
		$logs = FaceTerminalLog::where('date', '<=', today()->addDays(-$this->minAgesInDays()))
							   ->where('photo', '!=', null)
							   ->get();

		foreach($logs as $log) {
			$log->removePhoto();
		}
	}


	public function deleteAttendancePhotos()
	{
		$attendances = Attendance::where('date', '<=', today()->addDays(-$this->minAgesInDays()))
								 ->whereHas('attendanceMeta', function($query){
								 	$query->where(function($query2){
								 		$query2->where('clock_in_photo', '!=', null)
								 			   ->orWhere('clock_out_photo', '!=', null);
								 	});
								 })->get();

		foreach($attendances as $attendance) {
			if($attendance->isHasMeta()) {
				$attendance->removePhoto();
			}
		}
	}


	public function deleteStoreVisitPhotos()
	{
		$storeVisits = StoreVisit::where('visited_at', '<=', today()->addDays(-$this->minAgesInDays()))
								 ->where('photo', '!=', null)
								 ->get();

		foreach($storeVisits as $storeVisit) {
			$storeVisit->removePhoto();
		}
	}
}
