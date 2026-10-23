<?php

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

final class ApiStatusRoutesTest extends TestCase
{
    public function test_service_root_returns_api_information(): void
    {
        $response = app('router')->getRoutes()->match(Request::create('/'))->run();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame([
            'service' => 'TaskFlow Laravel API',
            'status' => 'ok',
            'api' => '/api',
            'health' => '/api/health',
        ], json_decode($response->getContent(), true));
    }

    public function test_api_health_route_returns_ok(): void
    {
        $response = app('router')->getRoutes()->match(Request::create('/api/health'))->run();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['data' => ['status' => 'ok']], json_decode($response->getContent(), true));
    }
}
