<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('api');
    }
    public function index()
    {

        $products = auth()->user()->products;

        return response()->json([
            'success' => true,
            'data' => $products,

        ]);
    } // end of index fun...

    public function show($id)
    {
        $products = auth()->user()->products()->find($id);

        if (!$products) {
            return response()->json([
                'success' => false,
                'data' => 'no data found for this id =' . $id,

            ]);
        }
        return response()->json([

            'success' => true,
            'data' => $products->toArray(),
        ], 200);
    } // end of show fun...

    public function store(Request $request)
    {

        /// check validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'price' => 'required|integer',
            'image'=>'required|image',
        ]);
        /// if validation failed
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

            $upload_file =$request->image->store('public/product_img/');

        $product = new Product();

        $product->name = $request->name;
        $product->price = $request->price;
        $product->image = $request->image .'-'. $request->image->getClientOriginalExtension();
        if (auth()->user()->products()->save($product)) {


            return response()->json([

                'success' => true,
                'data' => $product->toArray(),
            ], 200);
        } else {

            return response()->json([

                'success' => false,
                'data' => 'Oops, product store Failed',
            ], 422);
        }
    } // end of store fun...



    public function update(Request $request, $id)
    {
        $product = auth()->user()->products()->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'data' => 'Product with id' . $id . 'not found',
            ], 500);
        }

        $updated = $product->fill($request->all())->save();

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product could not be updated',
            ], 500);
        }
    } // end of update fun ...


    public function destroy($id)
    {
        $product = auth()->user()->products()->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product with id' . $id . 'not found'
            ], 400);
        }

        if ($product->delete()) {
            return response()->json([
                'success' => true,
                'message' => "Product deleted successfully"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product could not be deleted'
            ], 500);
        }
    }
}