<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

        <title>Inserta una película</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <h3 class="text-center alert-info">INSERTAR PELÍCULA</h3>
                    <?php
                    require 'auxiliar.php';

                    if (!comprobarLogueado()) {
                        return;
                    }
                    $defecto = [
                        'titulo' => '',
                        'anyo' => '',
                        'sinopsis' => '',
                        'duracion' => '',
                        'genero_id' => '',
                    ];

                    $pelicula = filter_input(INPUT_POST, 'pelicula', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
                    $pelicula = array_map('trim', $pelicula);
                    $pelicula = array_merge($defecto, $pelicula);
                    //array_walk($pelicula, function (&$v) { $v = trim($v); });

                    /* En vez de recoger cada variable usamos el array peliculas
                    $titulo = $anyo = $sinopsis = $duracion = $genero_id = '';
                    extract($pelicula, EXTR_IF_EXISTS);*/
                    $error = [];
                    $pdo = conectar();
                    if(!empty($_POST)):
                        try {
                            comprobarTitulo($pelicula['titulo'], $error);
                            comprobarAnyo($pelicula['anyo'], $error);
                            comprobarDuracion($pelicula['duracion'], $error);
                            comprobarGenero($pdo, $pelicula['genero_id'], $error);
                            comprobarErrores($error);
                            $valores = array_filter($pelicula, 'comp');
                            insertar($pdo, $valores);
                            $_SESSION['mensaje'] = 'La película se ha insertado correctamente.'
                            ?>
                            <?php
                            header('Location: index.php');
                            return;
                        } catch (Exception $e) {
                            mostrarErrores($error);
                        }
                    endif;
                    $generos = generos($pdo);
                    formulario($pelicula, null, $generos);
                ?>
            </div>
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
