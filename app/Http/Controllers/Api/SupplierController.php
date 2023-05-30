<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Supplier;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class SupplierController extends Controller
{
    protected $modelName       = 'Supplier';
    protected $modelNamePlural = 'Suppliers';
    protected $model;

    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    public function index()
    {

        $data =  Supplier::orderBy('created_at', 'DESC')
                    ->filter(Request::only('search'))
                    ->paginate(10)
                    ->through(function ($item, $index) {
                        return [
                            'id'      => $item->id,
                            'sl'      => $index + 1,
                            'name'    => $item->name,
                            'phone'   => $item->phone,
                            'address' => $item->address
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
                    'modelNamePlural'  => $this->modelNamePlural
            ]
        ], 200);
    }

    public function store()
    {
        Request::validate([
            'name'    => ['required'],
            'phone'   => ['required'],
            'address' => ['required']
        ]);

        $item = $this->model->create([
            'name'    => Request::get('name'),
            'phone'   => Request::get('phone'),
            'address' => Request::get('address')
        ]);

        return response()->json([
                    'data'      => $item,
                    'message'   => 'Supplier created successfully'
                ], 200);
    }

    public function edit(Supplier $item)
    {
        return response()->json([
            'item' => [
                'id'      => $item->id,
                'name'    => $item->name,
                'phone'   => $item->phone,
                'address' => $item->address,
            ],
            'modelName'        => $this->modelName,
            'modelNamePlural'  => $this->modelNamePlural
        ], 200);
    }

    public function update(Supplier $item)
    {
        Request::validate([
            'name'    => ['required'],
            'phone'   => ['nullable'],
            'address' => ['nullable']
        ]);

        $item->update([
            'name'    => Request::get('name'),
            'phone'   => Request::get('phone'),
            'address' => Request::get('address')
        ]);


        return response()->json([
                            'data'      => $item,
                            'message'   => 'Supplier updated successfully'
                        ], 200);
    }

    public function destroy(Supplier $item)
    {
        $item->delete();

        return response()->json([
                            'message'   => 'Supplier deleted successfully'
                        ], 200);
    }

}
