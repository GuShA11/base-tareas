<?php

declare (strict_types=1);

namespace Com\Daw2\Helpers;

class Producto {

    private $codigo_producto;
    private $nombre_producto;
    private $descripcionHtml;
    private $provedor;
    private $categoria;
    private $coste;
    private $margen;
    private $stock;
    private $iva;
    private $productosRelacionados;
    
    private static $IVAS=[0,4,10,21];

    public function __construct(string $codigo_producto, string $nombre_producto, string $descripcionHtml, Proveedor $provedor, Categoria $categoria, float $coste, float $margen, int $stock,int $iva) {
        if (!preg_match("/^.{1-10}$/", $codigo_producto)) {
            throw new InvalidArgumentException("La longitud del codigo tiene que estar comprendida entre 0 y 10");
        }
        self::notEmptynotEmpty($nombre_producto,"Nombre no puede estar vacío");
        self::notEmpty($descripcionHtml,"HTML no puede estar vacío");
        if($coste <=0){
            throw new InvalidArgumentException("Coste debe ser mayor que cero");
        }
        if($margen <=0){
            throw new InvalidArgumentException("Margen debe ser mayor que cero");
        }
        if($stock <0){
            throw new InvalidArgumentException("El stock no debe ser menor que cero");
        }
        if(!in_array($iva, self::$IVAS)){
            throw new InvalidArgumentException("El IVA '$iva'no está permitido. Valores permitidos: ".implode(",", self::$IVAS));
        }
        $this->codigo_producto = $codigo_producto;
        $this->nombre_producto = $nombre_producto;
        $this->descripcionHtml = $descripcionHtml;
        $this->provedor = $provedor;
        $this->categoria = $categoria;
        $this->coste = $coste;
        $this->margen = $margen;
        $this->stock = $stock;
        $this->iva=$iva;
        $this->productosRelacionados=[];
    }

    public static function notEmpty(float $param,string $mensaje) {
        if (empty($param)) {
            throw new InvalidArgumentException($mensaje);
        }
    }
    
    public function agregarStock(int $stock) {
        if($stock<0){
            throw new InvalidArgumentException("El stock a agregar no puede ser menor que cero");
        }
        $this->stock+=$stock;
    }
    
    
    public function descontarStock(int $stock){
        if($this->stock< $stock){
            throw new SinStockException();
        }
        $this->stock-=$stock;
    }
    
    public function agregarProductosRealcionado(Producto $p){
        if(!in_array($p, $this->productosRelacionados)){
            $this->productosRelacionados[] = $p;
        }
    }
    
    public function getPrecioVenta(bool $conIva =true):float {
        if($conIva){
            return $this->coste*$this->margen*((100+$this->iva)/100);
        }
    }
    
    public function __get($property){
        if(property_exists($this, $property)){
            return $this->$property;
        }
        if($property=='precioVenta'){
            return $this->getPrecioVenta();
        }
    }
    

}
