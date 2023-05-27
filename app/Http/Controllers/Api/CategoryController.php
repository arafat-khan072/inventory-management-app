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

        $data =  Category::orderBy('created_at', 'DESC')
                    ->filter(Request::only('search'))
                    ->get()
                    ->transform(function ($item, $index) {
                        return [
                            'id'     => $item->id,
                            'sl'     => $index + 1,
                            'name'   => $item->name,
                            'status' => $item->status == 1 ? 'Active' : 'Inctive'
                        ];
                    });

        return response()->json([
            'filters'          => Request::all('search'),
            'data'             => $data,
            'modelName'        => $this->modelName,
            'modelNamePlural'  => $this->modelNamePlural,
        ], 200);
    }

    public function create()
    {
        return response()->json([
                'data'   => [
                    'modelName'        => $this->modelName,
                    'modelNamePlural'  => $this->modelNamePlural,
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
            'name'   => ['required'],
            'status' => ['required']
        ]);

        $item = $this->model->create([
            'name'   => Request::get('name'),
            'status' => Request::get('status')
        ]);

        return response()->json([
                    'data'      => $item,
                    'message'   => 'Category created successfully'
                ], 200);
    }

    public function edit(Category $item)
    {
        return response()->json([
            'item' => [
                'id'     => $item->id,
                'name'   => $item->name,
                'status' => $item->status,
            ],
            'modelName'        => $this->modelName,
            'modelNamePlural'  => $this->modelNamePlural,
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
        ], 200);
    }

    public function update(Category $item)
    {
        Request::validate([
            'name'   => ['required'],
            'status' => ['nullable']
        ]);

        $item->update([
            'name'   => Request::get('name'),
            'status' => Request::get('status')
        ]);


        return response()->json([
                            'data'      => $item,
                            'message'   => 'Category updated successfully'
                        ], 200);
    }

    public function destroy(Category $item)
    {
        // Category::find($id)->delete();
        $item->delete();

        return response()->json([
                            'message'   => 'Category deleted successfully'
                        ], 200);

    }

}
