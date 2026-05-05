<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
	protected $fillable = [ 'title', 'id_department', 'id_position', 'id_employee_group', 'content', 'file', 'is_published', 'send_status', 'send_schedule' ];


	/**
	 * 	Relationship
	 * */
	public function department()
	{
		return $this->belongsTo('App\Models\Department', 'id_department')->withTrashed();
	}

	public function position()
	{
		return $this->belongsTo('App\Models\Position', 'id_position')->withTrashed();
	}

	public function employeeGroup()
	{
		return $this->belongsTo('App\Models\EmployeeGroup', 'id_employee_group');
	}

	public function departmentName()
	{
		return $this->department->department_name ?? 'Semua Departemen';
	}

	public function positionName()
	{
		return $this->position->position_name ?? 'Semua Departemen';
	}

	public function employeeGroupName()
	{
		return $this->employeeGroup->group_name ?? 'Semua Grup Karyawan';
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createAnnouncement($request)
	{
		$announcement = self::create([
			'title'			=> $request->title,
			'id_department'	=> is_numeric($request->id_department) ? $request->id_department : null,
			'id_position'	=> is_numeric($request->id_position) ? $request->id_position : null,
			'id_employee_group'	=> is_numeric($request->id_employee_group) ? $request->id_employee_group : null,
			'content'		=> $request->content,
			'is_published'	=> $request->is_published,
		]);
		$announcement->saveFile($request);

		return $announcement;
	}

	public function updateAnnouncement($request)
	{
		$this->update([
			'title'			=> $request->title,
			'id_department'	=> is_numeric($request->id_department) ? $request->id_department : null,
			'id_position'	=> is_numeric($request->id_position) ? $request->id_position : null,
			'id_employee_group'	=> is_numeric($request->id_employee_group) ? $request->id_employee_group : null,
			'content'		=> $request->content,
			'is_published'	=> $request->is_published,
		]);
		$this->saveFile($request);

		return $this;
	}

	public function deleteAnnouncement()
	{
		$this->removeFile();
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function saveFile($request)
	{
		if(!empty($request->file_announcement))
		{
			$this->removeFile();
			$file = $request->file('file_announcement');
			$filename = date('YmdHis').'.'.$file->getClientOriginalExtension();
			$path = storage_path('app/public/announcement');
			$file->move($path, $filename);

			$this->update([
				'file'	=> $filename
			]);
		}

		return $this;
	}

	public function removeFile()
	{
		if($this->isHasFile()) {
			\File::delete($this->filePath());
			$this->update([
				'file'	=> null,
			]);
		}

		return $this;
	}

	public function isHasFile()
	{
		if(!$this->file) return false;

		return \File::exists($this->filePath());
	}

	public function filePath()
	{
		return storage_path('app/public/announcement/'.$this->file);
	}

	public function fileLink()
	{
		return url('storage/announcement/'.$this->file);
	}

	public function fileExtension()
	{
		return pathinfo($this->filePath())['extension'];
	}

	public function fileIsImage()
	{
		return in_array($this->fileExtension(), [ 'jpeg', 'jpg', 'png', 'gif' ]);
	}

	public function fileIsVideo()
	{
		return in_array($this->fileExtension(), [ 'mp4', 'mkv' ]);
	}

	public function fileIsDocument()
	{
		return in_array($this->fileExtension(), [ 'pdf', 'xlsx', 'xls', 'doc', 'docx' ]);
	}

	public function sendScheduleFormatted($format = 'Y-m-d')
	{
		if(empty($this->send_schedule)) return '-';
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->send_schedule)->format($format);
	}

	public function createdAtText($format = 'Y-m-d')
	{
		return $this->created_at->format($format);
	}

	public function isPublished()
	{
		return $this->is_published == 'yes';
	}

	public function isWaitingToSend()
	{
		return $this->send_status == 'Menunggu';
	}

	public function isPublishedHtml()
	{
		return $this->isPublished() ? '<span class="text-success"> Terpublikasi </span>' : '<span class="text-primary"> Draft </span>';
	}

	public function sendStatusHtml()
	{
		$status = $this->send_status;

		if($status == 'Menunggu') {
			return '<span class="text-primary"> Menunggu </span>';
		} elseif ($status == 'Terkirim') {
			return '<span class="text-success"> Terkirim </span>';
		} elseif ($status == 'Pengiriman') {
			return '<span class="text-success"> Pengiriman </span>';
		} else {
			return '-';
		}
	}

	public function getEmployees()
	{
		$employees = Employee::select([ 'employees.*' ])
							 ->where('status', Employee::STATUS_ACTIVE);

		if(!empty($this->id_department)) {
			$employees = $employees->where('id_department', $this->id_department);
		}

		if(!empty($this->id_position)) {
			$employees = $employees->where('id_position', $this->id_position);
		}

		if(!empty($this->id_employee_group)) {
			$employees = $employees->where('id_employee_group', $this->id_employee_group);
		}

		return $employees->get();
	}

	public function sendBroadcast()
	{
		$message = '*[Pengumuman]*';
		$message .= "\n\n*".$this->title.'*';

		if(!empty($this->content)) {
			$message .= "\n\n".$this->content;
		}

		$message .= "\n\nLihat detail : ".route('announcement.detail', $this->id);

		foreach($this->getEmployees() as $employee) {
			// \App\MyClass\Whatsapp::sendChat([
				// 	'text'	=> $text,
				// 	'to'	=> $employee->phone_number,
			// ]);

			// if($this->isHasFile()) {
				// 	\App\MyClass\Whatsapp::sendMedia([
					// 		'path'	=> $this->filePath(),
					// 		'to'	=> $employee->phone_number,
					// 	]);
					// }
			
			$EndPointWa = WhatsappNew::END_POINT_WA;
			if($EndPointWa == 'WA Baru'){
				// wa Baru
				$res = Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message, $filePath=$this->filePath(), $caption="Pengumuman");
			}else{
				$res = \App\MyClass\Whatsapp::sendChat([
					'to'    => $employee->phone_number,
					'text'  => $message,
				]);
	
				if(!empty($photoPath)) {
					\App\MyClass\Whatsapp::sendMedia([
						'to'    => $employee->phone_number,
						'path'  => $photoPath,
					]);
				}
			}
		}

		return $this;
	}

	public function fetchData()
	{
		return (object) [
			'id'		=> $this->id,
			'title'		=> $this->title,
			'date'		=> date('Y-m-d H:i', strtotime($this->created_at)),
			'date_formatted' => \App\MyClass\Date::fullDateWithDayName($this->created_at),
			'is_has_file' => $this->isHasFile(),
			'file_link'	=> $this->isHasFile() ? $this->fileLink() : null,
		];
	}

	public function checkAccessToData()
	{
		$user = auth()->user();
		$isAllow = false;
		if($user->isEmployee()) {
			if($employeeActive = employee()) {
				foreach($this->getEmployees() as $employee) {
					if($employeeActive->id == $employee->id) {
						$isAllow = true;
						break;
					}
				}
			}
		} elseif ($user->isAdmin()) {
			$isAllow = true;
		}

		return $isAllow;
	}


	public static function fetchAnnouncements($announcements)
	{
		$results = [];

		foreach($announcements as $announcement) {
			$results[] = $announcement->fetchData();
		}

		return $results;
	}

	public static function dataTable($request)
	{
		return self::dt($request);
	}

	public static function dt($request)
	{
		$data = self::select([ 'announcements.*' ])
					->with([ 'department', 'employeeGroup' ]);

		if(auth()->user()->isEmployee()) {
			$employee = employee();
			$data->where(function($query) use($employee) {
				$query->where('id_department', $employee->id_department)
					  ->orWhere('id_department', null);
			})->where(function($query) use($employee){
				$query->where('id_position', $employee->id_position)
					  ->orWhere('id_position', null);
			})->where(function($query) use($employee){
				$query->where('id_employee_group', $employee->id_employee_group)
					  ->orWhere('id_employee_group', null);
			})->where('is_published', 'yes');
		}

		return \DataTables::eloquent($data)
			->editColumn('department.department_name', function($data){
				$html = $data->departmentName();
				if($data->position) {
					$html .= '<br> <span class="text-primary">['.$data->positionName().']';
				}

				return $html;
			})
			->editColumn('employee_group.group_name', function($data){
				return $data->employeeGroupName();
			})
			->editColumn('created_at', function($data){
				return $data->createdAtText('d M Y H:i');
			})
			->editColumn('is_published', function($data){
				return $data->isPublishedHtml();
			})
			->editColumn('send_status', function($data){
				$status = $data->sendStatusHtml();
				if($data->isWaitingToSend()) {
					$status .= '<br>['.$data->sendScheduleFormatted('d M Y H:i').']';
				}
				return $status;
			})
			->addColumn('action', function($data){
				if(user()->isAdmin()) {
					$button = '
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="'.route('announcement.detail', $data->id).'" title="Detail Pengumuman">
								<i class="mdi mdi-magnify"></i> Detail 
							</a>';

					if(UserPermission::check('announcement', 'u')) {
						$button .= '
							<a class="dropdown-item" href="'.route('announcement.edit', $data->id).'" title="Edit Pengumuman">
								<i class="mdi mdi-pencil"></i> Edit 
							</a>';
					}

					if(UserPermission::check('announcement', 'd')) {
						$button .= '
							<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('announcement.destroy', $data->id).'" title="Hapus Pengumuman">
								<i class="mdi mdi-trash-can"></i> Hapus
							</a>';
					}

					$button .= '
						</div>
					</div>';

				} else {
					$button = '
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="'.route('announcement.detail', $data->id).'" title="Detail Pengumuman">
								<i class="mdi mdi-magnify"></i> Detail 
							</a>
						</div>
					</div>';
				}

				return $button;
			})
			->rawColumns([ 'department.department_name', 'is_published', 'send_status', 'action' ])
			->make(true);
	}

	public static function sendWaitingAnnouncements()
	{
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$announcements = self::where('send_status', 'Menunggu')
							 ->where('send_schedule', '!=', null)
							 ->where('send_schedule', '<=', now()->format('Y-m-d H:i:s'))
							 ->orderBy('send_schedule', 'asc')
							 ->take(1)
							 ->get();

		foreach($announcements as $announcement) {
			$announcement->update([
				'send_status' 	=> 'Pengiriman',
				'send_schedule'	=> null,
			]);

			$announcement->sendBroadcast();

			$announcement->update([
				'send_status' 	=> 'Terkirim'
			]);
		}
	}
}
