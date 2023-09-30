<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $q = QueryBuilder::for(Product::class)
            ->allowedFilters([
                'part_number',
                'lot_number',
                'quantity',
                'date_manufactured',
                'created_at',
                'serial_number',
                AllowedFilter::scope('created_between'),
                AllowedFilter::scope('released_between')
            ])
            ->allowedSorts(['created_at', 'date_manufactured', 'id']);
        $result = $request->has('page') ? $q->jsonPaginate(120) : $q->get();
        return ProductResource::collection($result);
    }

    /**
     * @param Request $request
     * @return ProductResource
     */
    public function store(Request $request)
    {
        $product = Product::create($request->input());
        return new ProductResource($product);
    }

    /**
     * @param $id
     * @return ProductResource
     */
    public function show($id)
    {
        $product = Product::find($id);
        return new ProductResource($product);
    }

    /**
     * @param Request $request
     * @param $id
     * @return ProductResource
     */
    public function update(Request $request, $id)
    {
        $product = tap(Product::find($id))
            ->update($request->input())
            ->fresh();
        return new ProductResource($product);
    }

    /**
     * @param $id
     * @return ProductResource
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return new ProductResource($product);
    }
}
