<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Dashboard extends Controller
{
        public function index()
        {
            return view('dashboard.index');
        }
        public function buses()
        {
            return view('buses.index');
        }
        public function drivers()
        {
            return view('drivers.index');
        }

        public function students()
        {
            return view('students.index');
        } 
        public function locations()
        {
            return view('locations.index');
        }
        public function presentations()
        {
            return view('presentations.index');
        }
        public function retreats()
        {
            return view('retreats.index');
        }
        public function wages()
        {
            return view('wages.index');
        }
}