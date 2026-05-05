<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NecessityReason extends Model
{
	use SoftDeletes;
	
	protected $fillable = [ 'reason', 'max_duration', 'is_using_max_duration', 'is_counted_present', 'is_required_file' ];

	/**
	 * 	CRUD methods
	 * */
	public static function createNecessityReason(array $request)
	{
		return self::create($request);
	}

	public function updateNecessityReason(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteNecessityReason()
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

	public function isCountedPresent()
	{
		return $this->is_counted_present == 'yes';
	}

	public function isRequiredFile()
	{
		return $this->is_required_file == 'yes';
	}

	public function isUsingMaxDurationText()
	{
		return $this->isUsingMaxDuration() ? 'Ya' : 'Tidak';
	}

	public function isCountedPresentText()
	{
		return $this->isCountedPresent() ? 'Ya' : 'Tidak';
	}

	public function isRequiredFileText()
	{
		return $this->isRequiredFile() ? 'Ya' : 'Tidak';
	}



	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'necessity_reasons.*' ]);

		return \DataTables::eloquent($data)
			->editColumn('is_using_max_duration', function($data){
				return $data->isUsingMaxDurationText();
			})
			->editColumn('max_duration', function($data){
				return $data->isUsingMaxDuration() ? $data->max_duration.' hari' : '-';
			})
			->editColumn('is_counted_present', function($data){
				return $data->isCountedPresentText();
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

				if(UserPermission::check('necessity_reason', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.necessity_reason.edit', $data->id).'" title="Edit Alasan Cuti">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('necessity_reason', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.necessity_reason.destroy', $data->id).'" title="Hapus Alasan Cuti">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('necessity_reason', 'u') && !UserPermission::check('necessity_reason', 'd')) {
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
}
