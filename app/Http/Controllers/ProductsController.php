<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductResource;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Imports\ProductsImport;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;

class ProductsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $products = Product::all();
            return $this->sendResponse(ProductResource::collection($products), 'All Products Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
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
    public function store(Request $request)
    {
        try {

            Validator::make($request->all(), [
                'file' => 'required',
            ])->validate();

            $rows = Excel::toCollection(new ProductsImport(), $request->file('file'))->first()->toArray();

            $errors = $importedProducts = [];
            $i = 0;
            foreach ($rows as $index => $row) {

                $row = [
                    'name' => $row[1],
                    'description' => $row[2],
                    'price' => $row[3],
                    'sku' => $row[4],
                ];

                $validator = Validator::make($row, [
                    'name' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
                    'sku' => 'required|unique:products,sku',
                    'price' => 'required|numeric|min:0',
                ]);

                if ($validator->fails()) {
                    // Handle validation errors

                    $errors[] = [
                        'row' => $index + 1,
                        'errors' => $validator->errors()->all(),
                    ];
                    // ...
                }

                $i++;
            }

            // exit(print_r($errors));


            if (empty($errors)) {

                foreach ($rows as $index => $row) {


                    $product = new Product([
                        'name' => $row[1],
                        'description' => $row[2],
                        'price' => $row[3],
                        'sku' => $row[4],
                    ]);
                    $product->save();

                    $importedProducts[] = $product;
                }

                return $this->sendResponse($importedProducts, 'Products Imported successfully', 201);
            } else {
                return $this->sendError('Validation Errors.', ['error' => $errors], 401);
            }
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
