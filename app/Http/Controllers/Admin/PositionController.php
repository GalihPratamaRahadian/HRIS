<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use Validations;
use DB;

class PositionController extends Controller
{
	/**
	*   Position
	*
	*/
	public function index(Request $request)
	{
		if($request->ajax()) {
			return Position::dataTable($request);
		}

		return view('admin.position.index', [
			'title'         => 'Jabatan',
			'breadcrumbs'   => [
				[
					'title' => 'Jabatan',
					'link'  => route('admin.position')
				],
			]
		]);
	}

	public function create()
	{
		return view('admin.position.create', [
			'title'         => 'Tambah Jabatan',
			'breadcrumbs'   => [
				[
					'title' => 'Jabatan',
					'link'  => route('admin.position')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.position.create')
				],
			]
		]);
	}

	public function store(Request $request)
	{
		Validations::validatePosition($request);
		DB::beginTransaction();

		try {
			Position::createPosition($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function detail(Position $position)
	{
		return view('admin.position.detail', [
			'title'			=> 'Detail Jabatan',
			'position'		=> $position,
			'breadcrumbs'   => [
				[
					'title' => 'Jabatan',
					'link'  => route('admin.position')
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.position.detail', $position->id)
				],
			]
		]);
	}

	public function edit(Position $position)
	{
		return view('admin.position.edit', [
			'title'			=> 'Edit Jabatan',
			'position'		=> $position,
			'breadcrumbs'   => [
				[
					'title' => 'Jabatan',
					'link'  => route('admin.position')
				],
				[
					'title' => 'Edit',
					'link'  => route('admin.position.edit', $position->id)
				],
			]
		]);
	}

	public function update(Request $request, Position $position)
	{
		Validations::validatePosition($request);
		DB::beginTransaction();

		try {
			$position->updatePosition($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function destroy(Position $position)
	{
		DB::beginTransaction();

		try {
			$position->deletePosition();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
