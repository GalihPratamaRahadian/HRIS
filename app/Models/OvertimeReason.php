<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeReason extends Model
{
	protected $fillable = [ 'reason' ];

	/**
	 * 	CRUD methods
	 * */
	public static function createOvertimeReason(array $request)
	{
		$overtimeReason = self::create($request);
		return $overtimeReason;
	}

	public function updateOvertimeReason(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteOvertimeReason()
	{
		return $this->delete();
	}


	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'overtime_reasons.*' ]);

		return \DataTables::eloquent($data)
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('overtime_reason', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.overtime_reason.edit', $data->id).'" title="Edit Alasan Lembur">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('overtime_reason', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.overtime_reason.destroy', $data->id).'" title="Hapus Alasan Lembur">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('overtime_reason', 'u') && !UserPermission::check('overtime_reason', 'd')) {
					$button .= '
						<a class="dropdown-item" href="javascript:void(0);">
							Tidak Ada Aksi
						</a>';
				}

				return $button;
			})
			->rawColumns([ 'action' ])
			->make(true);
	}
}
