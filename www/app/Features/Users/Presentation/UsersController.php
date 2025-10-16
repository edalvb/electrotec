<?php
namespace App\Features\Users\Presentation;

use App\Features\Users\Infrastructure\UserRepository;
use App\Shared\Auth\AuthMiddleware;
use App\Shared\Http\JsonResponse;
use App\Shared\Validation\Validator;

final class UsersController
{
    private UserRepository $userRepository;
    private AuthMiddleware $authMiddleware;
    private Validator $validator;

    public function __construct(
        UserRepository $userRepository,
        AuthMiddleware $authMiddleware,
        Validator $validator
    ) {
        $this->userRepository = $userRepository;
        $this->authMiddleware = $authMiddleware;
        $this->validator = $validator;
    }

    /**
     * GET /api/users
     * Lista todos los usuarios (solo admin)
     */
    public function list(): void
    {
        // Solo admin puede listar usuarios
        $this->authMiddleware->requireAdmin();

        $users = $this->userRepository->findAllClientes();
        
        $data = array_map(fn($user) => [
            'id' => $user->id,
            'username' => $user->username,
            'tipo' => $user->tipo,
            'created_at' => $user->createdAt,
            'updated_at' => $user->updatedAt
        ], $users);

        JsonResponse::ok($data);
    }

    /**
     * POST /api/users
     * Crea un nuevo usuario (por defecto administrador). Solo disponible para admins.
     */
    public function create(): void
    {
        // Solo admin puede crear usuarios
        $this->authMiddleware->requireAdmin();

        $input = json_decode(file_get_contents('php://input'), true);

        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';
        $passwordConfirm = $input['password_confirm'] ?? '';
        $tipo = trim($input['tipo'] ?? 'admin');

        // Validar campos obligatorios
        if (!$this->validator->required($username, 'Usuario') ||
            !$this->validator->required($password, 'Contraseña')) {
            JsonResponse::error('Datos incompletos', 400, [
                'errors' => $this->validator->getErrors()
            ]);
            return;
        }

        // Verificar que el username no exista
        if ($this->userRepository->usernameExists($username)) {
            JsonResponse::error('El nombre de usuario ya está registrado', 409);
            return;
        }

        // Validar contraseña
        if (!$this->validator->validatePassword($password)) {
            JsonResponse::error('Contraseña inválida', 400, [
                'errors' => $this->validator->getErrors()
            ]);
            return;
        }

        // Validar que las contraseñas coincidan
        if ($password !== $passwordConfirm) {
            JsonResponse::error('Las contraseñas no coinciden', 400);
            return;
        }

        // Validar tipo de usuario
        if (!in_array($tipo, ['admin', 'client'])) {
            JsonResponse::error('Tipo de usuario inválido', 400);
            return;
        }

        // Crear usuario
        $user = $this->userRepository->create([
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'tipo' => $tipo
        ]);

        JsonResponse::success('Usuario creado exitosamente', [
            'user' => $user->toArray()
        ], 201);
    }

    /**
     * PUT /api/users/:id
     * Actualiza un usuario existente (solo admin)
     */
    public function update(): void
    {
        // Solo admin puede actualizar usuarios
        $currentUser = $this->authMiddleware->requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            JsonResponse::error('ID inválido', 400);
            return;
        }

        $user = $this->userRepository->findById($id);

        if (!$user) {
            JsonResponse::error('Usuario no encontrado', 404);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $data = [];

        if ($currentUser->id === $user->id && isset($input['tipo']) && trim($input['tipo']) !== 'admin') {
            JsonResponse::error('No puedes cambiar tu propio rol de administrador', 400);
            return;
        }

        // Actualizar username si se proporciona
        if (isset($input['username'])) {
            $username = trim($input['username']);
            if ($this->validator->required($username, 'Usuario')) {
                // Verificar que el username no esté en uso por otro usuario
                if ($this->userRepository->usernameExists($username, $id)) {
                    JsonResponse::error('El nombre de usuario ya está en uso', 409);
                    return;
                }
                $data['username'] = $username;
            }
        }

        // Actualizar tipo si se proporciona
        if (isset($input['tipo'])) {
            $tipo = trim($input['tipo']);
            if (!in_array($tipo, ['admin', 'client'])) {
                JsonResponse::error('Tipo de usuario inválido', 400);
                return;
            }
            $data['tipo'] = $tipo;
        }

        // Si se proporciona nueva contraseña
        if (isset($input['password']) && !empty($input['password'])) {
            $password = $input['password'];
            $passwordConfirm = $input['password_confirm'] ?? '';

            if (!$this->validator->validatePassword($password)) {
                JsonResponse::error('Contraseña inválida', 400, [
                    'errors' => $this->validator->getErrors()
                ]);
                return;
            }

            if ($password !== $passwordConfirm) {
                JsonResponse::error('Las contraseñas no coinciden', 400);
                return;
            }

            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (empty($data)) {
            JsonResponse::error('No se proporcionaron datos para actualizar', 400);
            return;
        }

        $updatedUser = $this->userRepository->update($id, $data);

        JsonResponse::success('Usuario actualizado exitosamente', [
            'user' => $updatedUser->toArray()
        ]);
    }

    /**
     * DELETE /api/users/:id
     * Elimina un usuario (solo admin)
     */
    public function delete(): void
    {
        // Solo admin puede eliminar usuarios
        $currentUser = $this->authMiddleware->requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            JsonResponse::error('ID inválido', 400);
            return;
        }

        $user = $this->userRepository->findById($id);

        if (!$user) {
            JsonResponse::error('Usuario no encontrado', 404);
            return;
        }

        if ($currentUser->id === $user->id) {
            JsonResponse::error('No puedes eliminar tu propio usuario', 400);
            return;
        }

        $this->userRepository->delete($id);

        JsonResponse::success('Usuario eliminado exitosamente');
    }

    /**
     * GET /api/users/me
     * Obtiene perfil del usuario autenticado
     */
    public function me(): void
    {
        $currentUser = $this->authMiddleware->requireAuth();
        
        $user = $this->userRepository->findById($currentUser->id);

        if (!$user) {
            JsonResponse::error('Usuario no encontrado', 404);
            return;
        }

        JsonResponse::ok($user->toArray());
    }

    /**
     * PUT /api/users/me
     * Actualiza perfil del usuario autenticado
     */
    public function updateMe(): void
    {
        $currentUser = $this->authMiddleware->requireAuth();
        
        $input = json_decode(file_get_contents('php://input'), true);

        $data = [];

        // Actualizar username si se proporciona
        if (isset($input['username'])) {
            $username = trim($input['username']);
            if ($this->validator->required($username, 'Usuario')) {
                // Verificar que el username no esté en uso por otro usuario
                if ($this->userRepository->usernameExists($username, $currentUser->id)) {
                    JsonResponse::error('El nombre de usuario ya está en uso', 409);
                    return;
                }
                $data['username'] = $username;
            }
        }

        // Cambiar contraseña
        if (isset($input['password']) && !empty($input['password'])) {
            $password = $input['password'];
            $passwordConfirm = $input['password_confirm'] ?? '';

            if (!$this->validator->validatePassword($password)) {
                JsonResponse::error('Contraseña inválida', 400, [
                    'errors' => $this->validator->getErrors()
                ]);
                return;
            }

            if ($password !== $passwordConfirm) {
                JsonResponse::error('Las contraseñas no coinciden', 400);
                return;
            }

            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (empty($data)) {
            JsonResponse::error('No se proporcionaron datos para actualizar', 400);
            return;
        }

        $updatedUser = $this->userRepository->update($currentUser->id, $data);

        JsonResponse::success('Perfil actualizado exitosamente', [
            'user' => $updatedUser->toArray()
        ]);
    }
}
