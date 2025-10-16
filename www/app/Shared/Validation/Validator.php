<?php
namespace App\Shared\Validation;

final class Validator
{
    private array $errors = [];

    /**
     * Valida que un RUC sea válido (11 dígitos numéricos)
     */
    public function validateRuc(string $ruc, string $fieldName = 'RUC'): bool
    {
        // Eliminar espacios
        $ruc = trim($ruc);

        // Verificar que tenga exactamente 11 caracteres
        if (strlen($ruc) !== 11) {
            $this->errors[$fieldName] = "El $fieldName debe tener exactamente 11 dígitos.";
            return false;
        }

        // Verificar que solo contenga números
        if (!ctype_digit($ruc)) {
            $this->errors[$fieldName] = "El $fieldName solo debe contener números.";
            return false;
        }

        return true;
    }

    /**
     * Valida que una contraseña cumpla los requisitos de seguridad
     * - Mínimo 8 caracteres
     * - Al menos una letra mayúscula
     * - Al menos una letra minúscula
     * - Al menos un número
     * - Al menos un carácter especial
     */
    public function validatePassword(string $password, string $fieldName = 'Contraseña'): bool
    {
        // Mínimo 8 caracteres
        if (strlen($password) < 8) {
            $this->errors[$fieldName] = "La $fieldName debe tener al menos 8 caracteres.";
            return false;
        }

        // Al menos una letra mayúscula
        if (!preg_match('/[A-Z]/', $password)) {
            $this->errors[$fieldName] = "La $fieldName debe contener al menos una letra mayúscula.";
            return false;
        }

        // Al menos una letra minúscula
        if (!preg_match('/[a-z]/', $password)) {
            $this->errors[$fieldName] = "La $fieldName debe contener al menos una letra minúscula.";
            return false;
        }

        // Al menos un número
        if (!preg_match('/[0-9]/', $password)) {
            $this->errors[$fieldName] = "La $fieldName debe contener al menos un número.";
            return false;
        }

        // Al menos un carácter especial
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $this->errors[$fieldName] = "La $fieldName debe contener al menos un carácter especial (!@#$%^&*(),.?\":{}|<>).";
            return false;
        }

        return true;
    }

    /**
     * Valida que un email sea válido
     */
    public function validateEmail(string $email, string $fieldName = 'Email'): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$fieldName] = "El $fieldName no es válido.";
            return false;
        }

        return true;
    }

    /**
     * Valida que un campo no esté vacío
     */
    public function required(mixed $value, string $fieldName): bool
    {
        if (empty($value) && $value !== '0') {
            $this->errors[$fieldName] = "El campo $fieldName es obligatorio.";
            return false;
        }

        return true;
    }

    /**
     * Obtiene todos los errores de validación
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Verifica si hay errores
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * Limpia los errores
     */
    public function clearErrors(): void
    {
        $this->errors = [];
    }
}
