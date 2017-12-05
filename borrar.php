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

        <title>confirmación de borrado</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <h3 class="text-center alert-info">BORRAR PELÍCULA</h3><hr />
                    <?php
                    require 'auxiliar.php';

                    if (!comprobarLogueado()) {
                        return;
                    }
                    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? false;
                    try {
                        $error = [];
                        comprobarParametro($id, $error);
                        $pdo = conectar();
                        $fila = buscarPelicula($pdo, $id, $error);
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            borrarPelicula($pdo, $id, $error);
                            comprobarErrores($error);
                            $_SESSION['mensaje'] = 'Película eliminada correctamente.';
                            header('Location: index.php');
                            return;
                        }
                        ?>
                            <h3>
                                ¿Seguro que desea borrar la película <b>"<?= $fila['titulo'] ?>"</b>?
                            </h3>
                            <form action="borrar.php?id=<?= $id ?>" method="post">
                                <input class="btn btn-success" type="submit" value="Sí" />
                                <a class="btn btn-default" href="index.php">No</a>
                            </form>
                        <?php
                    } catch (Exception $e) {
                        mostrarErrores($error);
                        volver();
                    }
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
