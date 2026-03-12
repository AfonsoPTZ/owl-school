<?php

namespace App\Controllers;

use App\DTOs\AuthDTO;
use App\Services\AuthService;

class AuthController extends BaseController
{
    private AuthService $service;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->service = new AuthService($conn);
    }

    public function login(): void
    {
        try {
            $dto = new AuthDTO($_POST);
            $result = $this->service->login($dto);

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'login');
        }
    }

    public function logout(): void
    {
        try {
            $result = $this->service->logout();

            if (($result['redirect'] ?? false) === true) {
                header('Location: /owl-school/public/index.html');
                exit;
            }

            $this->json($result, $result['status'] ?? 200);
        } catch (\Throwable $e) {
            $this->handleException($e, 'logout');
        }
    }
}