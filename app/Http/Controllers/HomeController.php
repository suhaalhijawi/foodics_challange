<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{

    public function index(): View {
        
        $available_products = Product::GetAvalilableProducts();

        return view('dashboard', [
            'available_products' => $available_products,
        ]);
    }
}
