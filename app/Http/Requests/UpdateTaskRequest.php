<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateTaskRequest extends FormRequest { public function authorize():bool{return $this->user()?->role==='admin'||$this->route('task')?->assignee_id===$this->user()?->id;} public function rules():array{return ['status'=>'required|in:TODO,IN_PROGRESS,DONE'];} }
