<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Document;

class HomeController extends Controller
{

    public function home()
    {
        $data['clients']=Client::orderBy('client')->get();        
        return view('lightworx::web.home',$data);
    }
}
