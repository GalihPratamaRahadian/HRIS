<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseComment extends Model
{
	protected $fillable = [ 'id_course', 'id_employee', 'id_user', 'comment', 'id_comment_reply' ];


	/**
	 * 	Relationship methods
	 * */
	public function course()
	{
		return $this->belongsTo('App\Models\Course', 'id_course');
	}

	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}

	public function user()
	{
		return $this->belongsTo('App\User', 'id_user');
	}

	public function replies()
	{
		return $this->hasMany('App\Models\CourseComment', 'id_comment_reply');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createCourseComment(array $request)
	{
		return self::create(array_merge($request, [
			'id_employee'	=> user()->isEmployee() ? employee()->id : null,
			'id_user'		=> user()->id,
		]));
	}

	public function deleteCourseComment()
	{
		CourseComment::where('id_comment_reply', $this->id)->delete();
		return $this->delete();
	}


	/**
	 *  Helper methods
	 * */
	public function commentHtml()
	{
		$comment = str_replace("\n", "<br>", $this->comment);
		return $comment;
	}

	public function avatarLink()
	{
		try {
			return $this->user->profilePhotoUrl();
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function name()
	{
		try {
			return $this->user->name;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function createdAtText($format = 'd M Y H:i')
	{
		return $this->created_at->format($format);
	}
}
