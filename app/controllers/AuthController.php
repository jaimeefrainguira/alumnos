<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

final class AuthController extends Controller
{
    public function loginForm(): void
    {
        $this->view('auth/login');
    }

    public function login(): void
    {
        $username = trim($_POST['usuario'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $_SESSION['flash_error'] = 'Credenciales incompletas.';
            $this->redirect('/login');
        }

        $user = (new Usuario())->findByUsername($username);
        if ($user === null || !password_verify($password, $user['password'])) {
            $_SESSION['flash_error'] = 'Usuario o contraseña inválidos.';
            $this->redirect('/login');
        }

        session_regenerate_id(true);
        $_SESSION['auth'] = [
            'id' => (int) $user['id'],
            'usuario' => $user['usuario'],
            'rol' => $user['rol'],
        ];

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/login');
    }

    public function updateCredentials(): void
    {
        if (!isset($_SESSION['auth'])) {
            $this->redirect('/login');
        }

        if (!$this->validateCsrfToken($_POST['csrf'] ?? null)) {
            $_SESSION['flash_error'] = 'CSRF inválido.';
            $this->redirect('/dashboard');
        }

        $userId = (int) $_SESSION['auth']['id'];
        $newUsername = trim($_POST['nuevo_usuario'] ?? '');
        $newPassword = (string) ($_POST['nueva_password'] ?? '');

        $usuarioModel = new Usuario();

        if ($newUsername !== '') {
            $usuarioModel->updateUsername($userId, $newUsername);
            $_SESSION['auth']['usuario'] = $newUsername;
        }

        if ($newPassword !== '') {
            $usuarioModel->updatePassword($userId, $newPassword);
        }

        $_SESSION['flash_ok'] = 'Credenciales actualizadas correctamente.';
        $this->redirect('/dashboard');
    }
}
