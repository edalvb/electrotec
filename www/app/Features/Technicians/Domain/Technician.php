<?php
namespace App\Features\Technicians\Domain;

final class Technician
{
    public function __construct(
        public int $id,
        public string $nombreCompleto,
        public ?string $cargo,
        public ?string $pathFirma
    ) {}

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre_completo' => $this->nombreCompleto,
            'cargo' => $this->cargo,
            'path_firma' => $this->pathFirma,
        ];
    }
}
