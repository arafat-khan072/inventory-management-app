<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
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
                            'products'      => json_decode($item->product_list),
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
            'invoice_no'                  => ['required'],
            'supplier'                    => ['required', Rule::exists('suppliers', 'id')],
            'productsRow.*.product_name'  => ['required'],
            'productsRow.*.product_stock' => ['required'],
            'productsRow.*.product_qty'   => ['required'],
            'productsRow.*.product_price' => ['required'],
            'productsRow.*.product_total' => ['required'],
            'purchase_date'               => ['required'],

        ]);

        $total = 0;
        foreach(Request::get('productsRow') as $pr){
            $product = DB::table('products')->where('id',$pr['product_id'])->first();
            if($product->stock_qty > $pr['product_qty']){
                DB::table('products')->where('id',$pr['product_id'])->update([
                    'stock_qty' => $product->stock_qty - $pr['product_qty']
                ]);
                $total = $total + $pr['product_total'];
            }
        }
        
        $item = $this->model->create([
            'invoice_no'           => Request::get('invoice_no'),
            'supplier_id'          => Request::get('supplier'),
            'product_list'         => json_encode(Request::get('productsRow')),
            'total_price'          => $total,
            'purchase_date'        => Request::get('purchase_date'),
            'note'                 => Request::get('note'),
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
                'productsRow'   => json_decode($item->product_list) ?? '',
                'total_price'   => $item->total_price,
                'purchase_date' => $item->purchase_date,
                'note'          => $item->note,
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
            'invoice_no'                  => ['nullable'],
            'supplier'                    => ['nullable', Rule::exists('suppliers', 'id')],
            'productsRow.*.product_name'  => ['nullable'],
            'productsRow.*.product_stock' => ['nullable'],
            'productsRow.*.product_qty'   => ['nullable'],
            'productsRow.*.product_price' => ['nullable'],
            'productsRow.*.product_total' => ['nullable'],
            'purchase_date'               => ['nullable'],
        ]);

        $total = 0;
        foreach(Request::get('productsRow') as $pr){
            $product = DB::table('products')->where('id',$pr['product_id'])->first();
            if($product->stock_qty > $pr['product_qty']){
                DB::table('products')->where('id',$pr['product_id'])->update([
                    'stock_qty' => $product->stock_qty - $pr['product_qty']
                ]);
                $total = $total + $pr['product_total'];
            }
        }
        
        $item->update([
            'invoice_no'           => Request::get('invoice_no'),
            'supplier_id'          => Request::get('supplier'),
            'product_list'         => json_encode(Request::get('productsRow')),
            'total_price'          => $total,
            'purchase_date'        => Request::get('purchase_date'),
            'note'                 => Request::get('note'),
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

    public function productList() {
        $data = Product::select('id','code','name','stock_qty','price')->where('status',1)->get();
        return response()->json([
                            'data' => $data,
                        ], 200);
    }

}
