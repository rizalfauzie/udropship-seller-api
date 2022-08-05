<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Http\Request;

$router->group([
    'middleware' => 'auth',
    'prefix' => 'api'
], function () use ($router) {

    $router->get('/', function () use ($router) {
        return response()->json([
            'version' => $router->app->version()
        ]);
    });

    $router->get('me', function (Request $request) {
        /** @var \App\Models\Vendor $user */
        $user = $request->user();

        return response()->json([
            'id' => $user->vendor_id,
            'name' => $user->vendor_name,
            'email' => $user->email,
            'phone' => $user->telephone,
            'location' => $user->city
        ]);
    });

    $router->get('product/types', ['uses' => 'ProductController@types', 'as' => 'types']);
    $router->get('product/categories', ['uses' => 'ProductController@categories', 'as' => 'categories']);
    $router->get('product/attributes/{id}', ['uses' => 'ProductController@attributes', 'as' => 'attributes']);

    $router->get('products', ['uses' => 'ProductController@index', 'as' => 'products']);
    $router->get('product/{sku}', ['uses' => 'ProductController@product', 'as' => 'product']);
    $router->post('product', ['uses' => 'ProductController@crproduct', 'as' => 'product.create']);

});
