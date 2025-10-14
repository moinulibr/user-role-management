<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(){
            //return Auth::user();
            $tickets = Ticket::latest()->get();
            return view('dashboard',compact('tickets'));
    }
}
