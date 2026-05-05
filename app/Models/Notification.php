<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	protected $fillable = [ 'id_user', 'type', 'title', 'description', 'is_read' ];

	const TYPE_INFORMATION	= 'info';
	const TYPE_SUCCESS		= 'success';
	const TYPE_DANGER		= 'danger';
	const TYPE_WARNING		= 'warning';


	public static function createNotification(array $request)
	{
		return self::create($request);
	}


	/**
	 * 	Helper methods
	 * */
	public function isInformationType()
	{
		return $this->type == self::TYPE_INFORMATION;
	}

	public function isDangerType()
	{
		return $this->type == self::TYPE_DANGER;
	}

	public function isWarningType()
	{
		return $this->type == self::TYPE_WARNING;
	}

	public function isSuccessType()
	{
		return $this->type == self::TYPE_SUCCESS;
	}

	public function createdAtText($format = 'd M Y H:i')
	{
		return date($format, strtotime($this->created_at));
	}

	public function isReadHtml()
	{
		if($this->is_read == 'no') return '<span class="text-danger"> Belum Dibaca </span>';
		if($this->is_read == 'yes') return '<span class="text-success"> Sudah Dibaca </span>';
	}

	public function setRead()
	{
		$this->update([
			'is_read'	=> 'yes'
		]);

		return $this;
	}



	public static function dt()
	{
		$data = self::select([ 'notifications.*' ])
					->where('id_user', auth()->user()->id);

		return \DataTables::eloquent($data)
			->editColumn('created_at', function($data){
				return $data->createdAtText('Y-m-d H:i');
			})
			->editColumn('is_read', function($data){
				return $data->isReadHtml();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('notification.detail', $data->id).'" title="Detail Notifikasi">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'is_read', 'action' ])
			->make(true);
	}
}
