<?php
namespace App\Http\Controllers\Custom;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Space\Custom\Product;

class ProductsController extends Controller
{
    public function allProducts()
    {
       return $products = Product::all();
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return $products = Product::all();
    }
}
