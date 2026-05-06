<?php

final class RegisterController
{
    public function register(): void
    {
        Session::start();

        $nombre = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');

        $errors = $this->validate($nombre, $email, $password, $passwordConfirmation);
        if (!empty($errors)) {
            Session::flash('error', $errors[0]);
            Session::flash('active_tab', 'register');
            $this->redirect('/login.php');
        }

        try {
            $supabase = new SupabaseClient();
        } catch (Throwable $e) {
            Session::flash('error', 'No se pudo inicializar Supabase. Revisa el archivo .env.');
            Session::flash('active_tab', 'register');
            $this->redirect('/login.php');
        }

        $existingPersona = Persona::findByEmail($supabase, $email);
        if ($existingPersona !== null) {
            Session::flash('error', 'El correo ya existe.');
            Session::flash('active_tab', 'register');
            $this->redirect('/login.php');
        }

        $signup = $supabase->signUpWithPassword($email, $password);
        if (!$signup['ok']) {
            Session::flash('error', (string) $signup['error']);
            Session::flash('active_tab', 'register');
            $this->redirect('/login.php');
        }

        $createPersona = Persona::create($supabase, $nombre, $email);
        if (!$createPersona['ok']) {
            Session::flash('error', (string) $createPersona['error']);
            Session::flash('active_tab', 'register');
            $this->redirect('/login.php');
        }

        Session::flash('success', 'Registro exitoso. Ahora puedes iniciar sesión.');
        $this->redirect('/login.php');
    }

    private function validate(string $nombre, string $email, string $password, string $passwordConfirmation): array
    {
        $errors = [];

        if ($nombre === '' || mb_strlen($nombre, 'UTF-8') < 2) {
            $errors[] = 'El nombre es obligatorio.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo no es válido.';
        }

        if ($password === '' || strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }

        if ($password !== $passwordConfirmation) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        return $errors;
    }

    private function redirect(string $path): void
    {
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '\\/');
        $location = $basePath === '' ? $path : ($basePath . $path);
        header('Location: ' . $location);
        exit;
    }
}

