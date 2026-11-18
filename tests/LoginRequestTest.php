<?php

namespace Omatech\Editora\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Accions\AdminLogin;
use PHPUnit\Framework\Attributes\DataProvider;

final class LoginRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Route::any('/fixture-login', static fn () => (new AdminLogin())->render());
    }

    #[DataProvider('nonPostMethods')]
    public function test_only_post_can_attempt_to_sign_in(string $method): void
    {
        $this->call($method, '/fixture-login')->assertStatus(405)->assertHeader('Allow', 'POST');
        $this->assertFalse(Session::has('user_id'));
    }

    public static function nonPostMethods(): array
    {
        return [['GET'], ['HEAD'], ['PUT'], ['PATCH'], ['DELETE']];
    }

    #[DataProvider('malformedRequests')]
    public function test_malformed_credentials_fail_without_authentication_or_password_flash(array $input): void
    {
        $this->post('/fixture-login', $input)->assertStatus(422);
        $this->assertFalse(Session::has('user_id'));
        $this->assertFalse(Session::has('_old_input'));
    }

    public static function malformedRequests(): array
    {
        return [
            [[]],
            [['p_username' => 'fixture-user']],
            [['p_username' => ['fixture-user'], 'p_password' => 'testing-only']],
            [['p_username' => 'fixture-user', 'p_password' => ['testing-only']]],
            [['p_username' => 'fixture-user', 'p_password' => 'testing-only', 'u_lang' => ['ca']]],
        ];
    }
}
