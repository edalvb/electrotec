<?php
namespace App\Shared\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

final class JwtService
{
    private string $secretKey;
    private string $algorithm = 'HS256';
    private int $expirationTime = 3600; // 1 hora en segundos

    public function __construct()
    {
        // Clave secreta desde variable de entorno o valor por defecto
        $this->secretKey = $_ENV['JWT_SECRET_KEY'] ?? 'electrotec_secret_key_change_in_production';
    }

    /**
     * Genera un token JWT con la información del usuario
     * 
     * @param array $userData Array con id, username, tipo del usuario
     * @return string Token JWT generado
     */
    public function generateToken(array $userData): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->expirationTime;

        $payload = [
            'iat' => $issuedAt,           // Issued at: tiempo de emisión
            'exp' => $expire,              // Expire: tiempo de expiración
            'iss' => 'electrotec',         // Issuer: emisor del token
            'data' => [
                'id' => $userData['id'],
                'username' => $userData['username'] ?? null,
                'tipo' => $userData['tipo']
            ]
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Valida y decodifica un token JWT
     * 
     * @param string $token Token JWT a validar
     * @return object|null Datos del usuario si es válido, null si no lo es
     */
    public function validateToken(string $token): ?object
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return $decoded->data;
        } catch (Exception $e) {
            // Token inválido o expirado
            return null;
        }
    }

    /**
     * Extrae el token del header Authorization
     * 
     * @return string|null Token extraído o null si no existe
     */
    public function extractTokenFromHeader(): ?string
    {
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            
            // El token viene en formato: "Bearer <token>"
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Obtiene el usuario actual desde el token JWT en el header
     * 
     * @return object|null Datos del usuario autenticado o null
     */
    public function getCurrentUser(): ?object
    {
        $token = $this->extractTokenFromHeader();
        
        if (!$token) {
            return null;
        }

        return $this->validateToken($token);
    }
}
