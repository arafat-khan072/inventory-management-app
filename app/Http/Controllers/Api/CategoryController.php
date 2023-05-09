<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;



class CategoryController extends Controller
{
    
	protected $modelName       = 'Category';
	protected $modelNamePlural = 'Categories';
	protected $model;

	public function __construct(Category $model)
	{
		$this->model = $model;
	}

	public function index()
	{
		return response()->json([
			'filters'       => Request::all('search'),
			'category'      => Category::latest()
				->filter(Request::only('search'))
				->paginate()
				->transform(function ($item, $index) {
					return [
						'id'     => $item->id,
						'sl'     => $index + 1,
						'name'   => $item->name,
						'status' => $item->status == 1 ? 'Active' : 'Inctive'
					];
				}),
		],200);
	}

	public function create()
	{
        return response()->json([
                'data'   => [
                    'extraData'       => [
                        'statuses'   => [
                            [
                                'value' => '1',
                                'label' => 'Active'
                            ],
                            [
                                'value' => '0',
                                'label' => 'Inactive'
                            ]
                        ]
			        ]
            ]
        ], 200);
	}

	public function store()
	{
		Request::validate([
			'title'             => ['required',],
			'content'           => ['nullable'],
			'short_content'           => ['nullable'],
			'image'             => ['nullable', 'image'],
			'type'              => ['required'],
			'status'            => ['required'],
			'other_time'        => ['nullable'],
			'archived_time'     => ['nullable']
		]);

		$item = $this->model->create([
			'title'             => Request::get('title'),
			'content'           => Request::get('content'),
			'short_content'           => Request::get('short_content'),
			'type'              => Request::get('type'),
			'status'            => Request::get('status'),
			'other_time'        => Request::get('other_time'),
			'archived_time'     => Request::get('archived_time'),
		]);

		if (Request::file('image')) {
			$item->update(['image' => Request::file('image')->store(strtolower($this->modelNamePlural))]);
		}

		return Redirect::route('news')->with('success', 'News created.');
	}

	public function edit(Category $item)
	{
		return response()->json([
			'item' => [
				'id'             => $item->id,
				'title'          => $item->title,
				'slug'          => $item->slug,
				'image'          => $item->image,
				'content'        => $item->content,
				// 'date'           =>  $item->date->format('d M Y'),
				'short_content'           =>  $item->short_content,
				'type'           => $item->type,
				'status'         => $item->status,
				'other_time'     => $item->other_time,
				'archived_time'  => $item->archived_time,
			],
			'extraData'       => [
				'statuses'   => [
					[
						'value' => '1',
						'label' => 'Active'
					],
					[
						'value' => '0',
						'label' => 'Inactive'
					]
				]
			]
		],200);
	}

	public function update(Category $item)
	{
		Request::validate([
			'title'             => ['nullable'],
			'content'           => ['nullable'],
			'short_content'           => ['nullable'],
			'image'             => ['nullable'],
			'type'              => ['nullable'],
			'status'            => ['nullable'],
			'other_time'        => ['nullable'],
			'archived_time'     => ['nullable']
		]);

		$item->update([
			'title'             => Request::get('title'),
			'content'           => Request::get('content'),
			'short_content'           => Request::get('short_content'),
			'type'              => Request::get('type'),
			'status'            => Request::get('status'),
			'other_time'        => Request::get('other_time'),
			'archived_time'     => Request::get('archived_time'),
		]);

		if (Request::file('image')) {
			$oldFile = $item->image;
			$item->update([
				'image' => Request::file('image')->store(strtolower($this->modelNamePlural))
			]);
			if (Storage::exists($oldFile)) {
				Storage::delete($oldFile);
			}
		}

		return Redirect::back()->with('success', 'News updated.');
	}

	public function destroy(Category $item)
	{
		$oldFile = $item->image;
		if (Storage::exists($oldFile)) {
			Storage::delete($oldFile);
		}
		$item->delete();
		return Redirect::route('news')->with('success', 'News deleted.');
	}

}
