<?php

namespace App\Http\Controllers;


abstract class Controller
{
    
}


// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Illuminate\Foundation\Bus\DispatchesJobs;
// use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Routing\Controller as BaseController;

// class Controller extends BaseController
// {
//     use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

//     protected $branch_id;
//     protected $isAdmin;

//     public function __construct()
//     {
//         $this->branch_id = session('branch_id', auth()->check() ? auth()->user()->branch_id : 0);
//         $this->isAdmin = session('isAdmin', auth()->check() ? auth()->user()->isAdmin == 1 : false);
//     }
// }
