<?php
declare(strict_types=1);

function current_user(): ?array
{
    return null;
}

function is_logged_in(): bool
{
    return false;
}

function login_with_credentials(string $email, string $password): bool
{
    return false;
}

function logout_user(): void
{
    $_SESSION = [];
}

function require_login(bool $api = false): void
{
    if ($api) {
        json_response(['ok' => false, 'message' => 'El login esta deshabilitado temporalmente.'], 403);
    }

    redirect_to(base_url('pages/product.php'));
}
