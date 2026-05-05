<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileAppNotification extends Model
{
	protected $fillable = [ 'id_user', 'title', 'message', 'type', 'id_reference', 'notify_at', 'delivered' ];


	/**
	 * 	Relationship methods
	 * */
	public function user()
	{
		return $this->belongsTo('App\User', 'id_user');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createMobileAppNotification(array $request)
	{
		$notification = self::create(array_merge($request, [
			'notify_at'	=> now(),
			'type'		=> 'Notifikasi Admin',
		]));

		return $notification;
	}



	/**
	 * 	Helper methods
	 * */
	public function userName()
	{
		return $this->user->name ?? '-';
	}

	public function isDelivered() {
		return $this->delivered == 'Yes';
	}

	public function isDeliveredHtml()
	{
		return $this->isDelivered() ? '<span class="text-success"> Yes </span>' : '<span class="text-danger"> No </span>';
	}



	/**
	 * 	Static methods
	 * */
	public static function getNotifications($userId, $limit = 5)
	{
		$now = now();
		$notifications = self::where('id_user', $userId)
							 ->where('delivered', 'No')
							 ->whereDate('notify_at', '<=', $now)
							 ->whereDate('notify_at', '>=', $now->addMonths(-2))
							 ->orderBy('notify_at', 'asc')
							 ->take($limit)
							 ->get();
		$ids = [];
		foreach($notifications as $notification) {
			$ids[] = $notification->id;
		}

		self::whereIn('id', $ids)
			->update([
				'delivered'	=> 'Yes'
			]);

		return $notifications;
	}


	public static function dataTable($request)
	{
		$data = self::select([ 'mobile_app_notifications.*' ])
					->with([ 'user' ])
					->leftJoin('users', 'mobile_app_notifications.id_user', '=', 'users.id');

		// if(!empty($request->id_department)) {
		// 	if($request->id_department != 'all') {
		// 		$data = $data->where('id_department', $request->id_department);
		// 	} 
		// }

		return \DataTables::eloquent($data)
			->editColumn('users.name', function($data){
				return $data->userName();
			})
			->editColumn('delivered', function($data){
				return $data->isDeliveredHtml();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('admin.position.detail', $data->id).'" title="Detail Jabatan">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>';

				if(UserPermission::check('position', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.position.edit', $data->id).'" title="Edit Jabatan">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('position', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.position.destroy', $data->id).'" title="Hapus Jabatan">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('position', 'u') && !UserPermission::check('position', 'd')) {
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
			->rawColumns([ 'action', 'delivered' ])
			->make(true);
	}
}
