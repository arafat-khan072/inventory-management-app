<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class PurchaseController extends Controller
{
    protected $modelName       = 'Purchase';
    protected $modelNamePlural = 'Purchases';
    protected $model;

    public function __construct(Purchase $model)
    {
        $this->model = $model;
    }

    public function index()
    {

        $data =  Purchase::orderBy('created_at', 'DESC')
                    ->filter(Request::only('search'))
                    ->paginate(10)
                    ->through(function ($item, $index) {
                        return [
                            'id'            => $item->id,
                            'sl'            => $index + 1,
                            'invoice_no'    => $item->invoice_no,
                            'supplier'      => $item->supplier->name,
                            'product'       => $item->product->name,
                            'product_qty'   => $item->product_qty,
                            'product_price' => $item->single_product_price,
                            'total_price'   => $item->total_price,
                            'purchase_date' => Carbon::createFromFormat('Y-m-d H:i:s', $item->purchase_date)->format('d/m/Y')
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
                        'suppliers'  => Supplier::select('id as value', 'name as label')->get(),
                        'products'   => Product::select('id as value', 'name as label')->get(),
                    ]
            ]
        ], 200);
    }

    public function store()
    {
        Request::validate([
            'supplier'      => ['required', Rule::exists('suppliers', 'id')],
            'product'       => ['required', Rule::exists('products', 'id')],
            'product_qty'   => ['required'],
            'product_price' => ['required'],
            'purchase_date' => ['required'],

        ]);

        $invoice_no = rand(100000,999999);
        $total = Request::get('product_qty') * Request::get('product_price');
        $item = $this->model->create([
            'invoice_no'           => $invoice_no,
            'supplier_id'          => Request::get('supplier'),
            'product_id'           => Request::get('product'),
            'product_qty'          => Request::get('product_qty'),
            'single_product_price' => Request::get('product_price'),
            'total_price'          => $total,
            'purchase_date'        => Request::get('purchase_date'),
        ]);

        return response()->json([
                    'data'      => $item,
                    'message'   => 'Purchased item successfully'
                ], 200);
    }

    public function edit(Purchase $item)
    {
        return response()->json([
            'item' => [
                'id'            => $item->id,
                'invoice_no'    => $item->invoice_no,
                'supplier'      => $item->supplier->id ?? '',
                'product'       => $item->product->id ?? '',
                'product_qty'   => $item->product_qty,
                'product_price' => $item->single_product_price,
                'total_price'   => $item->total_price,
                'purchase_date' => $item->purchase_date,
            ],
            'modelName'        => $this->modelName,
            'modelNamePlural'  => $this->modelNamePlural,
            'extraData'        => [
                'suppliers'  => Supplier::select('id as value', 'name as label')->get(),
                'products'   => Product::select('id as value', 'name as label')->get(),
            ]
        ], 200);
    }

    public function update(Purchase $item)
    {
        Request::validate([
            'supplier'      => ['nullable', Rule::exists('suppliers', 'id')],
            'product'       => ['nullable', Rule::exists('products', 'id')],
            'product_qty'   => ['nullable'],
            'product_price' => ['nullable'],
            'total_price'   => ['nullable'],
            'purchase_date' => ['nullable'],
        ]);

        $total = Request::get('product_qty') * Request::get('product_price');
        $item->update([
            'supplier_id'          => Request::get('supplier'),
            'product_id'           => Request::get('product'),
            'product_qty'          => Request::get('product_qty'),
            'single_product_price' => Request::get('product_price'),
            'total_price'          => $total,
            'purchase_date'        => Request::get('purchase_date'),
        ]);

        return response()->json([
                            'data'      => $item,
                            'message'   => 'Purchase item updated successfully'
                        ], 200);
    }

    public function destroy(Purchase $item)
    {
        $item->delete();

        return response()->json([
                            'message'   => 'Purchase item deleted successfully'
                        ], 200);
    }

}
