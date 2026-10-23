<?php
namespace App\Services;
use App\Models\Task;
use Illuminate\Support\Facades\Http;
class OverdueTaskService {
    public function sync(): void { try { Http::withHeader('X-Internal-Token',config('services.django.token'))->timeout(2)->post(rtrim(config('services.django.url'),' /').'/api/tasks/sync-overdue/'); } catch (\Throwable $e) { report($e); } }
    public function applyRequestedStatus(Task $task, string $status, bool $isAdmin): void {
        if ($task->due_date && $task->due_date->lt(today()) && $task->status !== 'DONE') $task->status='OVERDUE';
        if ($task->status === 'OVERDUE' && $status === 'IN_PROGRESS') abort(422,'Overdue tasks cannot move back to IN_PROGRESS.');
        if ($task->status === 'OVERDUE' && $status !== 'DONE') abort(422,'Overdue tasks can only be closed.');
        if ($task->status === 'OVERDUE' && $status === 'DONE' && !$isAdmin) abort(403,'Only admins can close overdue tasks.');
        $task->status=$status;
    }
}
