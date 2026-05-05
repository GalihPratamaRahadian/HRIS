<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveReason extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'reason', 'max_duration', 'is_using_max_duration', 'is_cut_leave_quota', 'is_required_file' ];


	/**
	 * 	CRUD methods
	 * */
	public static function createLeaveReason(array $request)
	{
		$leaveReason = self::create($request);
		return $leaveReason;
	}

	public function updateLeaveReason(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteLeaveReason()
	{
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function reasonWithDurationText()
	{
		$text = $this->reason;
		if($this->isUsingMaxDuration()) $text .= ' (Maks. '.$this->max_duration.' hari)';
		return $text;
	}

	public function isUsingMaxDuration()
	{
		return $this->is_using_max_duration == 'yes';
	}

	public function isCutLeaveQuota()
	{
		return $this->is_cut_leave_quota == 'yes';
	}

	public function isRequiredFile()
	{
		return $this->is_required_file == 'yes';
	}

	public function isUsingMaxDurationText()
	{
		return $this->isUsingMaxDuration() ? 'Ya' : 'Tidak';
	}

	public function isCutLeaveQuotaText()
	{
		return $this->isCutLeaveQuota() ? 'Ya' : 'Tidak';
	}

	public function isRequiredFileText()
	{
		return $this->isRequiredFile() ? 'Ya' : 'Tidak';
	}

	public function fetchData()
	{
		return (object) [
			'id' => $this->id,
			'reason' => $this->reason,
			'max_duration_in_day' => $this->max_duration,
			'is_using_max_duration' => $this->isUsingMaxDuration(),
			'is_cut_leave_quota' => $this->isCutLeaveQuota(),
			'is_required_file' => $this->isRequiredFile(),
		];
	}


	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'leave_reasons.*' ]);

		return \DataTables::eloquent($data)
			->editColumn('is_using_max_duration', function($data){
				return $data->isUsingMaxDurationText();
			})
			->editColumn('max_duration', function($data){
				return $data->isUsingMaxDuration() ? $data->max_duration.' hari' : '-';
			})
			->editColumn('is_cut_leave_quota', function($data){
				return $data->isCutLeaveQuotaText();
			})
			->editColumn('is_required_file', function($data){
				return $data->isRequiredFileText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('leave_reason', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.leave_reason.edit', $data->id).'" title="Edit Alasan Cuti">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('leave_reason', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.leave_reason.destroy', $data->id).'" title="Hapus Alasan Cuti">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('leave_reason', 'u') && !UserPermission::check('leave_reason', 'd')) {
					$button .= '
						<a class="dropdown-item" href="javascript:void(0);">
							Tidak Ada Aksi
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'action' ])
			->make(true);
	}

	public static function fetchLeaveReasons($leaveReasons)
	{
		$results = [];
		foreach($leaveReasons as $lr) {
			$results[] = $lr->fetchData();
		}

		return $results;
	}
}
