<?php

declare (strict_types=1);

namespace Com\Daw2\Helpers;

class Categoria {

    private $nombre;
    private $padre;

    public function __construct(string $nombre, ?Categoria $padre = null) {
        if (trim($nombre) == "") {
            throw new InvalidArgumentException('El nombre no puede estar vacío');
        }
        $this->nombre = $nombre;
        $this->padre = $padre;
    }

    public function getFullName(): string {
        if (!is_null($this->padre)) {
            return $this->padre->getFullName() . ' > ' . $this->nombre;
        } else {
            return $this->nombre;
        }
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

}
