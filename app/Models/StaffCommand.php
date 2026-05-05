<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffCommand extends Model
{
	protected $fillable = [ 'command', 'position' ];
}
