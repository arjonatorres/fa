<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Modifica una película</title>
    </head>
    <body>
        <?php
        require 'auxiliar.php';
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? false;
        try {
            $error = [];
            comprobarParametro($id, $error);
            $pdo = conectar();
            $fila = buscarPelicula($pdo, $id, $error);
            comprobarErrores($error);
            extract($fila);
            if(!empty($_POST)):
                $error = [];
                $titulo    = trim(filter_input(INPUT_POST, 'titulo'));
                $anyo      = trim(filter_input(INPUT_POST, 'anyo'));
                $sinopsis  = trim(filter_input(INPUT_POST, 'sinopsis'));
                $duracion  = trim(filter_input(INPUT_POST, 'duracion'));
                $genero_id = trim(filter_input(INPUT_POST, 'genero_id'));
                try {
                    comprobarTitulo($titulo, $error);
                    comprobarAnyo($anyo, $error);
                    comprobarDuracion($duracion, $error);
                    comprobarGenero($pdo, $genero_id, $error);
                    comprobarErrores($error);
                    $valores = compact(
                        'titulo',
                        'anyo',
                        'sinopsis',
                        'duracion',
                        'genero_id'
                    );
                    modificar($pdo, $id, $valores);
                    ?>
                    <h3>La película se ha modificado correctamente.</h3>
                    <?php
                    volver();
                } catch (Exception $e) {
                    mostrarErrores($error);
                }
            endif;
            if (empty($_POST) || (!empty($_POST) && !empty($error))) {
                $generos = generos($pdo);
                formulario(compact(
                    'titulo',
                    'anyo',
                    'sinopsis',
                    'duracion',
                    'genero_id'
                ), $id, $generos);
            }
        } catch (Exception $e) {
            mostrarErrores($error);
        }
        ?>
    </body>
</html>
