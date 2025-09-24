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
            return view('Students.index');
        } 
        public function region()
        {
            return view('regions.index');
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
        public function preparationStus()
        {
            return view('preparation_stus.index');
        }
        public function preparationDrivers()
        {
            return view('preparation_drivers.index');
        }
        public function distributionStu()
        {
            return view('distribution-stu.index');
        }
}