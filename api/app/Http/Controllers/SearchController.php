<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        if ($request->has('filter')) {
            foreach ($request->input('filter') as $key => $value) {
                $collection = collect(Product::select($key)->distinct()->get());
                $collection = $collection->filter(function ($item) use ($key, $value) {
                    return str_starts_with(strtolower($item[$key]), strtolower($value));
                });
                $values = $collection->map(function ($item) use ($key) {
                   return $item[$key];
                })->all();
                return response()->json(array_values($values), 200);
            }
        }

        return 'silence';
    }
}
