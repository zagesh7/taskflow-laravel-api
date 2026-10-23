<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreTaskRequest extends FormRequest { public function authorize():bool{return $this->user()?->role==='admin';} public function rules():array{return ['title'=>'required|string|max:180','description'=>'nullable|string|max:5000','assignee_id'=>'required|exists:users,id','priority'=>'required|in:LOW,MEDIUM,HIGH','due_date'=>'required|date'];} }
