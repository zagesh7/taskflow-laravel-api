<?php
namespace App\Http\Controllers;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Services\OverdueTaskService;
use Illuminate\Http\Request;
class ProjectController extends Controller {
    public function index(Request $request,OverdueTaskService $overdue){$overdue->sync();$user=$request->user();$visibleTasks=fn($tasks)=>$user->role==='admin'?$tasks:$tasks->where('assignee_id',$user->id);$q=Project::with(['creator:id,name'])->with(['tasks'=>function($tasks)use($visibleTasks){$visibleTasks($tasks)->with('assignee:id,name');}])->withCount(['tasks as tasks_count'=>$visibleTasks]);if($user->role!=='admin')$q->whereHas('tasks',fn($t)=>$t->where('assignee_id',$user->id));return response()->json(['data'=>$q->latest()->get()]);}
    public function show(Request $request,Project $project,OverdueTaskService $overdue){$overdue->sync();$user=$request->user();if($user->role!=='admin'&&!$project->tasks()->where('assignee_id',$user->id)->exists())abort(404);$project->load(['creator:id,name','tasks'=>function($tasks)use($user){$tasks->with('assignee:id,name');if($user->role!=='admin')$tasks->where('assignee_id',$user->id);}]);return response()->json(['data'=>$project]);}
    public function store(StoreProjectRequest $request){$data=$request->validated();$project=Project::create([...$data,'created_by'=>$request->user()->id]);return response()->json(['data'=>$project],201);}
}
