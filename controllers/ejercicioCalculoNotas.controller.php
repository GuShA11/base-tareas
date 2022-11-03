<?php

declare(strict_types=1);

if (isset($_POST['enviar'])) {
    $data['errores'] = checkForm($_POST);
    $data['input'] = filter_var_array($_POST);
    if (count($data['errores']) === 0) {
        $jsonArray = json_decode($_POST['json_notas'], true);
        $resultado = calcular($jsonArray);
        $data['resultado'] = $resultado;
    }
}

function checkForm(array $post): array {
    $errores = [];
    if (empty($post['json_notas'])) {
        $errores['json_notas'] = 'Este campo es obligatorio';
    } else {
        $modulos = json_decode($post['json_notas'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $errores['json_notas'] = 'El formato no es correcto';
        } else {
            $erroresJson = "";
            foreach ($modulos as $modulo => $alumnos) {
                if (empty($modulo)) {
                    $erroresJson .= "El nombre del módulo no puede estar vacío<br>";
                } else {
                    foreach ($alumnos as $nombre => $notas) {
                        foreach ($notas as $nota) {
                            if (empty($nombre)) {
                                $erroresJson .= "El módulo '" . htmlentities($modulo) . "' tiene un alumno sin nombre<br>"; //Equivale a filter_var($modulo, FILTER_SANITIZE_SPECIAL_CHARS);
                            } else {
                                if (!is_numeric($nota)) {
                                    $erroresJson .= "El módulo '" . htmlentities($modulo) . "' el/la alumno/a '" . htmlentities($nombre) . "' tiene la nota '" . htmlentities($nota) . "' que no es un numero<br>"; //Equivale a filter_var($modulo, FILTER_SANITIZE_SPECIAL_CHARS);
                                } else {
                                    if ($nota < 0 || $nota > 10) {
                                        $erroresJson .= "Módulo '" . htmlentities($modulo) . "' alumno '" . htmlentities($nombre) . "' tiene una nota de " . $nota . "<br>"; //Equivale a filter_var($modulo, FILTER_SANITIZE_SPECIAL_CHARS);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($erroresJson)) {
                $errores['json_notas'] = $erroresJson;
            }
        }
    }
    return $errores;
}

function calcular($array): array {
    $resultado = [];
    $alumnado = [];
    foreach ($array as $asignatura => $alumnos) {
        $suspensos = 0;
        $aprobados = 0;
        $notaMedia = 0;
        $somaNotaMedias = 0;
        $somaNotas = 0;
        $max = [
            'alumno' => '',
            'nota' => -1
        ];
        $min = [
            'alumno' => '',
            'nota' => 11
        ];
//        var_dump("ASIGNATURA: " . $asignatura);
        foreach ($alumnos as $nombre => $notas) {
            if (!isset($alumnado[$nombre])) {
                $alumnado[$nombre] = ['aprobados' => 0, 'suspensos' => 0];
            }
            foreach ($notas as $nota) {
                $somaNotas += $nota;
            }
            $notaMedia = $somaNotas / count($notas);
            $somaNotaMedias += $notaMedia;
            if ($notaMedia < 5) {
                $suspensos++;
                $alumnado[$nombre]['suspensos']++;
            } else {
                $aprobados++;
                $alumnado[$nombre]['aprobados']++;
            }

            $somaNotas = 0;
            if ($notaMedia > $max['nota']) {
                $max['alumno'] = $nombre;
                $max['nota'] = $notaMedia;
            }
            if ($notaMedia < $min['nota']) {
                $min['alumno'] = $nombre;
                $min['nota'] = $notaMedia;
            }
        }
//        var_dump("Nota Media: " . $somaNotaMedias / count($alumnos));
//        var_dump($min);
//        var_dump($max);
        if (count($alumnos) > 0) {
            $resultado[$asignatura]['media'] = $somaNotaMedias / count($alumnos);
            $resultado[$asignatura]['max'] = $max;
            $resultado[$asignatura]['min'] = $min;
        } else {
            $resultado[$asignatura]['media'] = 0;
        }
        $resultado[$asignatura]['suspensos'] = $suspensos;
        $resultado[$asignatura]['aprobados'] = $aprobados;
    }
//    var_dump($resultado);
//    var_dump($alumnado);
    return array('modulos' => $resultado, 'alumnos' => $alumnado);
}

include 'views/templates/header.php';
include 'views/ejercicioCalculoNotas.view.php';
include 'views/templates/footer.php';
