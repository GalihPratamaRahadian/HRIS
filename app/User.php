<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'username', 'password', 'role', 'is_restricted_access',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	const ROLE_DEVELOPER		= 'developer';
	const ROLE_ADMIN			= 'admin';
	const ROLE_HRD				= 'hrd';
	const ROLE_STAFF			= 'staff';
	const ROLE_HSE				= 'hse';
	const ROLE_FRONT_SECURITY	= 'front_security';
	const ROLE_BACK_SECURITY	= 'back_security';
	const ROLE_REGISTRANT		= 'registrant';


	public function userPermissions()
	{
		return $this->hasMany('App\Models\UserPermission', 'id_user');
	}


	public function employee()
	{
		return $this->hasOne('App\Models\Employee', 'id_user');
	}


	public function registrant()
	{
		return $this->hasOne('App\Models\Registrant', 'id_user');
	}


	public function changePassword($newPassword)
	{
		$this->update([
			'password'  => \Hash::make($newPassword)
		]);

		return $this;
	}


	public function profilePhotoUrl()
	{
		if($this->isEmployee()) {
			if($this->employee) {
				if($this->employee->isPhotoExists()) {
					return $this->employee->photoLink('face');
				}
			}
		}
		
		return url('images/default-avatar.jpg');
	}


	public function getName()
	{
		if($this->isStaff()) {
			if(!empty($this->employee)) {
				return $this->employee->employee_name;
			}
		}

		return $this->name;
	}


	/**
	 * 	Helper methods
	 * */
	public function isDeveloper()
	{
		return $this->role == self::ROLE_DEVELOPER;
	}

	public function isAdmin()
	{
		return $this->role == self::ROLE_ADMIN;
	}

	public function isHrd()
	{
		return $this->role == self::ROLE_HRD;
	}

	public function isHse()
	{
		return $this->role == self::ROLE_HSE;
	}

	public function isStaff()
	{
		return $this->role == self::ROLE_STAFF;
	}

	public function isEmployee()
	{
		return $this->isStaff();
	}

	public function isFrontSecurity()
	{
		return $this->role == self::ROLE_FRONT_SECURITY;
	}

	public function isBackSecurity()
	{
		return $this->role == self::ROLE_BACK_SECURITY;
	}

	public function isRegistrant()
	{
		return $this->role == self::ROLE_REGISTRANT;
	}

	public function roleText()
	{
		if($this->isDeveloper()) return 'Developer';
		if($this->isAdmin()) return 'Administrator';
		if($this->isHrd()) return 'HRD';
		if($this->isHse()) return 'HSE';
		if($this->isEmployee() || $this->isStaff()) return 'Karyawan';
		if($this->isFrontSecurity()) return 'Front Security';
		if($this->isBackSecurity()) return 'Back Security';
		if($this->isRegistrant()) return 'Pendaftar';
	}


	public function comparePassword($password)
	{
		return \Hash::check($password, $this->password);
	}

	public function isRestrictedAccess()
	{
		if($this->isDeveloper()) return false;
		return $this->is_restricted_access == 'yes';
	}

	public function isRestrictedAccessText()
	{
		return $this->isRestrictedAccess() ? 'Ya' : 'Tidak';
	}



	/**
	 * 	CRUD methods
	 * */
	public static function createUser(array $request)
	{
		try {
			unset($request['permissions']);
		} catch (\Exception $e) {}

		$request['password'] = \Hash::make($request['password']);
		$user = self::create($request);

		return $user;
	}

	public function updateUser(array $request)
	{
		try {
			unset($request['permissions']);
		} catch (\Exception $e) {}
		
		if(!empty($request['password'])) {
			$request['password'] = \Hash::make($request['password']);
		} else {
			unset($request['password']);
		}

		$this->update($request);

		return $this;
	}

	public function deleteUser()
	{
		return $this->delete();
	}

	public function removePermissions()
	{
		\App\Models\UserPermission::where('id_user', $this->id)->delete();
		return $this;
	}

	public function modifyPermissions($permissions)
	{
		if(!empty($permissions))
		{
			$this->removePermissions();
			foreach($permissions as $menu => $accessAllowed)
			{
				\App\Models\UserPermission::create([
					'id_user'		=> $this->id,
					'menu'			=> $menu,
					'access_allowed'=> $accessAllowed,
				]);
			}

			$this->load('userPermissions');
		}
			
		return $this;
	}

	public function createMobileAppNotification($data)
	{
		$notification = \App\Models\MobileAppNotification::create(array_merge($data, [
			'id_user'	=> $this->id,
			'notify_at'	=> now(),
		]));

		return $notification;
	}


	/**
	 * 	Static methods
	 * */
	public static function dt($request)
	{
		$data = self::select([ 'users.*' ])
					->where('role', '!=', self::ROLE_DEVELOPER)
					->where('username', '!=', 'admin2')
					->whereIn('role', [
						self::ROLE_ADMIN,
						self::ROLE_HRD
					]);

		return \DataTables::eloquent($data)
			->editColumn('role', function($data){
				return $data->roleText();
			})
			->editColumn('is_restricted_access', function($data){
				return $data->isRestrictedAccessText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(\UserPermission::check('user', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('user.edit', $data->id).'" title="Edit User">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(\UserPermission::check('user', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('user.destroy', $data->id).'" title="Hapus User">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!\UserPermission::check('user', 'u') && !\UserPermission::check('user', 'd')) {
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
