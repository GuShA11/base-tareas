<?php

declare (strict_types=1);

namespace Com\Daw2\Helpers;

class Proveedor{
    private $cif;
    private $codigo;
    private $nombre;
    private $direccion;
    private $website;
    private $pais;
    private $email;
    private $telefono;

    
    public const REGEX ="/^[KPQS]([0][1-9])|([1-9][0-9])[0-9]{5}[A-Z]$|^[ABEH]([0][1-9])|([1-9][0-9])[0-9]{5}[0-9]$|^[CDFGLMN]([0][1-9])|([1-9][0-9])[0-9]{5}[0-9A-Z]$/";
    
    public function __construct(string $cif,string $codigo,string $nombre,string $direccion,string $website, string $pais,string $email, string $telefono) {
        self::checkCif($cif);
        self::notEmpty($codigo,"codigo");
        self::notEmpty($nombre,"nombre");
        self::notEmpty($direccion,"direccion");
        self::notEmpty($pais,"pais");
        if(!filter_var($website,FILTER_VALIDATE_URL)){
            throw new InvalidArgumentException("El formato de la web es incorrecto");
        }
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException('¡El formato del email es incorrecto!');
        }
        if(!preg_match("/^[0-9]{9}$/", $telefono)){
            throw new InvalidArgumentException('¡El número de teléfono debe estar compuesto por 9 numeros!');
        }
        $this->cif=$cif;
        $this->codigo=$codigo;
        $this->nombre=$nombre;
        $this->direccion= $direccion;
        $this->website=$website;
        $this->pais=$pais;
        $this->email=$email;
        $this->telefono=$telefono;
    }
    
    public static function checkCif(string $cif) {
        if(!preg_match(Proveedor::REGEX, $cif)){
            throw new InvalidArgumentException('¡El formato del cif es incorrecto!');
        }
    }
    
    public static function notEmpty(string $param,string $field) {
        if(empty($param)){
            throw new InvalidArgumentException('No se permite valor vacío para el parámetro: '.$field);
        }
    }
    
    public function __get($property){
        if(property_exists($this, $property)){
            return $this->$property;
        }
    }
}
