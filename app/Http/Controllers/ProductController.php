<?php

namespace App\Http\Controllers;
use App\Http\Requests\Product as ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth:api')->except('index','show');
    }

    public function index()
    {
        return ProductCollection::collection(Product::paginate());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest  $request)
    {
        $product = new Product;
        $product->name = $request->name;
        $product->detail = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->discount = $request->discount;
        $product->user_id = $request->user_id;

        $product->save();

        return response([
            'data' => new ProductResource($product)
        ],Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
//        print_r($request);die('first');
        $isAuthorized = $this->userAuthorize($product);

        if($isAuthorized){
            $request['detail'] = $request->description;
            unset($request['description']);
            $product->update($request->all());

            return response([
                'data' => new ProductResource($product)
            ],Response::HTTP_CREATED);
        }else{
            return response([
                'data'=>[
                    'err'=>true,
                    'code'=>203,
                    'message' => 'Sorry you are not allowed to update this information'
                ]
            ],203);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response(null,Response::HTTP_NO_CONTENT);
    }

    public function userAuthorize($product)
    {
//        print_r($product);die('second');
        if(Auth::user()->id != $product->user_id){
            //throw your exception text here;
            return 0;
        }else{
            return true;
        }

    }

}
