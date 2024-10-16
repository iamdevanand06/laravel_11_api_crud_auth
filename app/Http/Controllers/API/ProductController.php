<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\commonTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class ProductController extends Controller
{
    use commonTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $products = Product::paginate(10);

        return $this->sendResponse(ProductResource::collection($products)->response()->getData(), 'Products retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|unique:products|max:20|min:4|regex:/^[a-zA-Z ]+$/u',
            'detail' => 'required|max:150|regex:/^[a-zA-Z0-9() ]+$/u',
            'capasity_type' => 'required|max:3|alpha',
            'capasity' => 'required|max:4|numeric',
            'unit' => 'required|max:5|numeric',
            'price_per_unit' => 'required|max:5|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $product = Product::create($input);

        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|unique:products|max:20|min:4|regex:/^[a-zA-Z ]+$/u',
            'detail' => 'required|max:150|regex:/^[a-zA-Z0-9() ]+$/u',
            'capasity_type' => 'required|max:3|alpha',
            'capasity' => 'required|max:4|numeric',
            'unit' => 'required|max:5|numeric',
            'price_per_unit' => 'required|max:5|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->save();

        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
