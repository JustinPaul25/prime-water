<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    public function list()
    {
        return AdminLog::orderByDesc('created_at')->get();
    }
}
