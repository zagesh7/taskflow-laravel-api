<?php
namespace App\Http\Controllers;
use App\Models\User;
class UserController extends Controller { public function index(){return response()->json(['data'=>User::query()->orderBy('name')->get(['id','name','email','role'])]);} }
