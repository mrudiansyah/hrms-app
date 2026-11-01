<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;

class MobileAuthTest extends TestCase
{
    public function test_login_requires_email_and_password(): void
    {
        $response = app()->handle(Request::create(
            '/api/mobile/login',
            'POST',
            [],
            [],
            [],
            ['HTTP_ACCEPT' => 'application/json'],
        ));

        $this->assertSame(422, $response->getStatusCode());
        $errors = json_decode($response->getContent(), true, flags: JSON_THROW_ON_ERROR)['errors'];
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    public function test_profile_requires_a_mobile_token(): void
    {
        $response = app()->handle(Request::create(
            '/api/mobile/profile',
            'GET',
            [],
            [],
            [],
            ['HTTP_ACCEPT' => 'application/json'],
        ));

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame(
            ['message' => 'Unauthenticated.'],
            json_decode($response->getContent(), true, flags: JSON_THROW_ON_ERROR),
        );
    }

    public function test_attendance_routes_require_a_mobile_token(): void
    {
        foreach (['GET', 'POST'] as $method) {
            $response = app()->handle(Request::create(
                '/api/mobile/attendance',
                $method,
                [],
                [],
                [],
                ['HTTP_ACCEPT' => 'application/json'],
            ));

            $this->assertSame(401, $response->getStatusCode());
        }
    }

    public function test_slip_routes_require_a_mobile_token(): void
    {
        foreach (['salary', 'overtime'] as $type) {
            $response = app()->handle(Request::create(
                "/api/mobile/slips/{$type}",
                'GET',
                [],
                [],
                [],
                ['HTTP_ACCEPT' => 'application/json'],
            ));

            $this->assertSame(401, $response->getStatusCode());
        }
    }

    public function test_slip_pdf_routes_require_a_mobile_token(): void
    {
        foreach (['salary', 'overtime'] as $type) {
            foreach (['current', 'previous'] as $source) {
                $response = app()->handle(Request::create(
                    "/api/mobile/slips/{$type}/{$source}/42/pdf",
                    'GET',
                    [],
                    [],
                    [],
                    ['HTTP_ACCEPT' => 'application/json'],
                ));

                $this->assertSame(401, $response->getStatusCode());
            }
        }
    }
}
