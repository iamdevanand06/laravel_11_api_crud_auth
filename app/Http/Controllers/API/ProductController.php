<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\commonTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;
use Storage;

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
        try{
            $products = Product::paginate(10);
            return $this->sendResponse(ProductResource::collection($products)->response()->getData(), 'Products retrieved successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
            return $this->sendError('Error', $e, 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        try{
            $input = $request->all();

            $validator = Validator::make($input, [
                'name' => 'required|unique:products|max:20|min:4|regex:/^[a-zA-Z ]+$/u',
                'detail' => 'required|max:150|regex:/^[a-zA-Z0-9() ]+$/u',
                'img_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'capasity_type' => 'required|max:3|alpha',
                'capasity' => 'required|max:1000|numeric',
                'unit' => 'required|max:500|numeric',
                'price_per_unit' => 'required|max:100000|numeric',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            if ($image = $request->file('img_path')) {
                $productName = str_replace(' ', '_', $request->name) . '-' . date('Ymd_His') . "." . $image->getClientOriginalExtension();
                Storage::disk('publicLocal')->putFileAs('products', $request->file('img_path'), $productName);
                $input['img_path'] = env('APP_URL').'/assets/images/products/'.$productName;
            }

            $product = Product::create($input);

            return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
            return $this->sendError('Error', $e, 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        try{
            $product = Product::find($id);

            if (is_null($product)) {
                return $this->sendError('Product not found.');
            }

            return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
            return $this->sendError('Error', $e, 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        try{
            $input = $request->all();

            $validator = Validator::make($input, [
                'name' => 'required|unique:products|max:20|min:4|regex:/^[a-zA-Z ]+$/u',
                'detail' => 'required|max:150|regex:/^[a-zA-Z0-9() ]+$/u',
                'img_path' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
                'capasity_type' => 'required|max:3|alpha',
                'capasity' => 'required|max:4|numeric',
                'unit' => 'required|max:5|numeric',
                'price_per_unit' => 'required|max:5|numeric',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            if (($request->file('img_path'))) {
                $productImage = $request->file('img_path');
                $productName = date('YmdHis') . "." . $productImage->getClientOriginalExtension();
                $request->image->move(public_path().'\assets\images', $productName);
                $input['img_path'] = $productName;
            }

            $product->name = $input['name'];
            $product->detail = $input['detail'];
            $product->img_path = $input['img_path'];
            $product->capasity_type = $input['capasity_type'];
            $product->capasity = ['capasity'];
            $product->unit = ['unit'];
            $product->price_per_unit = ['price_per_unit'];
            $product->save();

            return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
            return $this->sendError('Error', $e, 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): JsonResponse
    {
        try{
            $product->delete();
            return $this->sendResponse([], 'Product deleted successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
            return $this->sendError('Error', $e, 404);
        }

    }
}
