<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingMaterial extends Model
{
	protected $fillable = [ 'id_training', 'id_employee', 'title', 'material_type', 'file_material', 'link_youtube' ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id_employee');
    }

    public function training()
    {
        return $this->belongsTo(Training::class, 'id_training');
    }

	/**
	 * 	Helper methods
	 * */
	public function filePath()
	{
		return storage_path('app/public/training_material/'.$this->file_material);
	}

	public function fileLink()
	{
		return url('storage/training_material/'.$this->file_material);
	}

	public function getLink()
	{
		if($this->material_type == 'File Upload') {
			return $this->fileLink();
		} elseif($this->material_type == 'File Video') {
			return $this->fileLink();
		} elseif($this->material_type == 'Link Youtube') {
			return $this->fileLink();
		}
	}

	public function getYoutubeId()
	{
		try {
			$id = null;
			if(\Str::contains($this->link_youtube, 'https://www.youtube.com/embed/')) {
				$id = str_replace('https://www.youtube.com/embed/', '', $this->link_youtube);
			} elseif(\Str::startsWith($this->link_youtube, 'https://youtu.be/')) {
				$id = str_replace('https://youtu.be/', '', $this->link_youtube);
			} else {
				parse_str( parse_url( $this->link_youtube, PHP_URL_QUERY ), $my_array_of_vars );
				$id = $my_array_of_vars['v'];
			}

			$id = explode('?', $id);
			return $id[0];
		} catch (\Exception $e) {
			return false;
		}
	}
}
