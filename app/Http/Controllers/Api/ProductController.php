<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    protected $modelName       = 'Product';
    protected $modelNamePlural = 'Products';
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function index()
    {

        $data =  Product::orderBy('created_at', 'DESC')
                    ->filter(Request::only('search'))
                    ->paginate(2)
                    ->through(function ($item, $index) {
                        return [
                            'id'        => $item->id,
                            'sl'        => $index + 1,
                            'code'      => $item->code,
                            'category'  => $item->category->name,
                            'name'      => $item->name,
                            'stock_qty' => $item->stock_qty,
                            'price'     => $item->price,
                            'status'    => $item->status == 1 ? 'Active' : 'Inctive'
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
                    'extraData'        => [
                        'categories'  => Category::select('id as value', 'name as label')->get()
                    ]
                ]
        ], 200);
    }

    public function store()
    {
        Request::validate([
            'code'          => ['required'],
            'category'      => ['required', Rule::exists('categories', 'id')],
            'name'          => ['required'],
            'stock_qty'     => ['required', 'numeric'],
            'price'         => ['required', 'numeric'],
            'status'        => ['required']
        ]);

        $item = $this->model->create([
            'code'         => Request::get('code'),
            'category_id'  => Request::get('category'),
            'name'         => Request::get('name'),
            'stock_qty'    => Request::get('stock_qty'),
            'price'        => Request::get('price'),
            'status'       => Request::get('status')
        ]);


        return response()->json([
                    'data'      => $item,
                    'message'   => 'Product created successfully'
                ], 200);
    }

    public function edit(Product $item)
    {
        return response()->json([
            'item' => [
                'id'        => $item->id,
                'code'      => $item->code,
                'category'  => $item->category->id ?? '',
                'name'      => $item->name,
                'stock_qty' => $item->stock_qty,
                'price'     => $item->price,
                'status'    => $item->status,
            ],
            'modelName'        => $this->modelName,
            'modelNamePlural'  => $this->modelNamePlural,
            'extraData'        => [
                'categories'  => Category::select('id as value', 'name as label')->get()
            ]
        ], 200);
    }

    public function update(Product $item)
    {
        Request::validate([
            'code'          => ['nullable'],
            'category'      => ['required', Rule::exists('categories', 'id')],
            'name'          => ['nullable'],
            'stock_qty'     => ['nullable', 'numeric'],
            'price'         => ['nullable', 'numeric'],
            'status'        => ['required']
        ]);

        $item->update([
            'code'         => Request::get('code'),
            'category_id'  => Request::get('category'),
            'name'         => Request::get('name'),
            'stock_qty'    => Request::get('stock_qty'),
            'price'        => Request::get('price'),
            'status'       => Request::get('status')
        ]);


        return response()->json([
                            'data'      => $item,
                            'message'   => 'Product updated successfully'
                        ], 200);
    }

    public function destroy(Product $item)
    {
        $item->delete();

        return response()->json([
                            'message'   => 'Product deleted successfully'
                        ], 200);

    }

}
