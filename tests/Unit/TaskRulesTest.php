<?php

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\OverdueTaskService;
use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class TaskRulesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \Carbon\Carbon::setTestNow('2026-10-24 12:00:00');
    }

    protected function tearDown(): void
    {
        \Carbon\Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_past_due_task_cannot_return_to_in_progress(): void
    {
        $task = $this->task(['due_date' => '2026-10-23', 'status' => 'TODO']);

        try {
            (new OverdueTaskService())->applyRequestedStatus($task, 'IN_PROGRESS', true);
            self::fail('Expected overdue task transition to be rejected.');
        } catch (HttpException $exception) {
            self::assertSame(422, $exception->getStatusCode());
            self::assertSame('OVERDUE', $task->status);
        }
    }

    public function test_admin_can_close_overdue_task(): void
    {
        $task = $this->task(['due_date' => '2026-10-23', 'status' => 'OVERDUE']);

        (new OverdueTaskService())->applyRequestedStatus($task, 'DONE', true);

        self::assertSame('DONE', $task->status);
    }

    public function test_member_cannot_close_overdue_task(): void
    {
        $task = $this->task(['due_date' => '2026-10-23', 'status' => 'OVERDUE']);

        try {
            (new OverdueTaskService())->applyRequestedStatus($task, 'DONE', false);
            self::fail('Expected a member close attempt to be rejected.');
        } catch (HttpException $exception) {
            self::assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_on_time_task_can_move_to_in_progress(): void
    {
        $task = $this->task(['due_date' => '2026-10-25', 'status' => 'TODO']);

        (new OverdueTaskService())->applyRequestedStatus($task, 'IN_PROGRESS', false);

        self::assertSame('IN_PROGRESS', $task->status);
    }

    public function test_update_request_allows_admin_and_assignee_only(): void
    {
        $task = $this->task(['id' => 12, 'assignee_id' => 7]);

        self::assertTrue($this->updateRequest($this->user(1, 'admin'), $task)->authorize());
        self::assertTrue($this->updateRequest($this->user(7, 'user'), $task)->authorize());
        self::assertFalse($this->updateRequest($this->user(8, 'user'), $task)->authorize());
    }

    public function test_create_requests_are_admin_only_and_validate_required_fields(): void
    {
        $admin = $this->user(1, 'admin');
        $member = $this->user(2, 'user');
        $projectRequest = new StoreProjectRequest();
        $taskRequest = new StoreTaskRequest();
        $projectRequest->setUserResolver(fn () => $admin);
        $taskRequest->setUserResolver(fn () => $admin);

        self::assertTrue($projectRequest->authorize());
        self::assertTrue($taskRequest->authorize());
        self::assertSame('required|string|max:160', $projectRequest->rules()['name']);
        self::assertSame('required|string|max:180', $taskRequest->rules()['title']);
        self::assertSame('required|exists:users,id', $taskRequest->rules()['assignee_id']);
        self::assertSame('required|in:LOW,MEDIUM,HIGH', $taskRequest->rules()['priority']);
        self::assertSame('required|date', $taskRequest->rules()['due_date']);

        $projectRequest->setUserResolver(fn () => $member);
        $taskRequest->setUserResolver(fn () => $member);
        self::assertFalse($projectRequest->authorize());
        self::assertFalse($taskRequest->authorize());
    }

    private function task(array $attributes = []): Task
    {
        $task = new Task();
        $task->forceFill($attributes + ['id' => 1, 'assignee_id' => 2, 'due_date' => '2026-10-25', 'status' => 'TODO']);

        return $task;
    }

    private function user(int $id, string $role): User
    {
        $user = new User();
        $user->forceFill(['id' => $id, 'role' => $role]);

        return $user;
    }

    private function updateRequest(User $user, Task $task): UpdateTaskRequest
    {
        $route = Mockery::mock(Route::class);
        $route->shouldReceive('parameter')->with('task', null)->andReturn($task);

        $request = new UpdateTaskRequest();
        $request->setUserResolver(fn () => $user);
        $request->setRouteResolver(fn () => $route);

        return $request;
    }
}
