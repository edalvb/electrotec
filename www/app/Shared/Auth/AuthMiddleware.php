<?php
namespace App\Shared\Auth;

use App\Shared\Http\JsonResponse;

final class AuthMiddleware
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Verifica que el usuario esté autenticado
     * Retorna los datos del usuario o termina la ejecución con error 401
     */
    public function requireAuth(): object
    {
        $user = $this->jwtService->getCurrentUser();

        if (!$user) {
            JsonResponse::error('No autorizado. Token inválido o expirado.', 401);
            exit;
        }

        return $user;
    }

    /**
     * Verifica que el usuario esté autenticado y sea de un tipo específico
     * 
     * @param string $tipo Tipo de usuario requerido ('admin' o 'cliente')
     * @return object Datos del usuario autenticado
     */
    public function requireRole(string $tipo): object
    {
        $user = $this->requireAuth();

        if ($user->tipo !== $tipo) {
            JsonResponse::error('Acceso denegado. Permisos insuficientes.', 403);
            exit;
        }

        return $user;
    }

    /**
     * Verifica que el usuario sea administrador
     */
    public function requireAdmin(): object
    {
        return $this->requireRole('admin');
    }

    /**
     * Verifica que el usuario sea cliente
     */
    public function requireClient(): object
    {
        return $this->requireRole('cliente');
    }
}
