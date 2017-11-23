<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Inserta una película</title>
    </head>
    <body>
        <?php
        require 'auxiliar.php';

        $_SESSION['pepe'] = 'hola';

        $titulo    = trim(filter_input(INPUT_POST, 'titulo'));
        $anyo      = trim(filter_input(INPUT_POST, 'anyo'));
        $sinopsis  = trim(filter_input(INPUT_POST, 'sinopsis'));
        $duracion  = trim(filter_input(INPUT_POST, 'duracion'));
        $genero_id = trim(filter_input(INPUT_POST, 'genero_id'));
        $error = [];
        if(!empty($_POST)):
            try {
                comprobarTitulo($titulo, $error);
                comprobarAnyo($anyo, $error);
                comprobarDuracion($duracion, $error);
                $pdo = conectar();
                comprobarGenero($pdo, $genero_id, $error);
                comprobarErrores($error);
                $valores = array_filter(compact(
                    'titulo',
                    'anyo',
                    'sinopsis',
                    'duracion',
                    'genero_id'
                ), 'comp');
                insertar($pdo, $valores);
                ?>
                <h3>La película se ha insertado correctamente.</h3>
                <?php
                volver();
            } catch (Exception $e) {
                mostrarErrores($error);
            }
        endif;
        if (empty($_POST) || (!empty($_POST) && !empty($error))){
                formulario(compact(
                    'titulo',
                    'anyo',
                    'sinopsis',
                    'duracion',
                    'genero_id'
                ), null);
        }
    ?>
    </body>
</html>
