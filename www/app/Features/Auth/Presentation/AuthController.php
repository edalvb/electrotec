<?php
namespace App\Features\Auth\Presentation;

use App\Features\Users\Infrastructure\UserRepository;
use App\Shared\Auth\JwtService;
use App\Shared\Http\JsonResponse;
use App\Shared\Validation\Validator;

final class AuthController
{
    private UserRepository $userRepository;
    private JwtService $jwtService;
    private Validator $validator;

    public function __construct(
        UserRepository $userRepository,
        JwtService $jwtService,
        Validator $validator
    ) {
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
        $this->validator = $validator;
    }

    /**
     * POST /api/auth/login
     * Autentica un usuario con username y contraseña
     */
    public function login(): void
    {
        // Obtener datos del body
        $input = json_decode(file_get_contents('php://input'), true);

        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        // Validar campos obligatorios
        if (!$this->validator->required($username, 'Usuario') || 
            !$this->validator->required($password, 'Contraseña')) {
            JsonResponse::error('Datos incompletos', 400, [
                'errors' => $this->validator->getErrors()
            ]);
            return;
        }

        // Buscar usuario por username
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            JsonResponse::error('Usuario o contraseña incorrectos', 401);
            return;
        }

        // Verificar contraseña
        if (!password_verify($password, $user->passwordHash)) {
            JsonResponse::error('Usuario o contraseña incorrectos', 401);
            return;
        }

        // Generar token JWT
        $token = $this->jwtService->generateToken([
            'id' => $user->id,
            'username' => $user->username,
            'tipo' => $user->tipo
        ]);

        // Responder con token y datos del usuario
        JsonResponse::success('Login exitoso', [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'tipo' => $user->tipo
            ]
        ]);
    }

    /**
     * GET /api/auth/me
     * Obtiene información del usuario autenticado
     */
    public function me(): void
    {
        $user = $this->jwtService->getCurrentUser();

        if (!$user) {
            JsonResponse::error('No autorizado', 401);
            return;
        }

        // Obtener datos completos del usuario
        $fullUser = $this->userRepository->findById($user->id);

        if (!$fullUser) {
            JsonResponse::error('Usuario no encontrado', 404);
            return;
        }

        JsonResponse::success('Usuario autenticado', [
            'user' => $fullUser->toArray()
        ]);
    }
}
