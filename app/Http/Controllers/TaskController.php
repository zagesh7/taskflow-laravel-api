<?php
namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\OverdueTaskService;
use Illuminate\Http\Request;
class TaskController extends Controller {
    public function store(StoreTaskRequest $request,Project $project){$data=$request->validated();$task=$project->tasks()->create([...$data,'status'=>'TODO']);return response()->json(['data'=>$task->load('assignee:id,name')],201);}
    public function update(UpdateTaskRequest $request,Task $task,OverdueTaskService $service){$data=$request->validated();$user=$request->user();$service->applyRequestedStatus($task,$data['status'],$user->role==='admin');$task->save();return response()->json(['data'=>$task->fresh()->load('assignee:id,name')]);}
}
