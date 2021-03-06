<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <title>Listado de películas</title>
        <style>
            #buscar {
                margin-bottom: 20px;
            }
            #tabla {
                margin: auto;
            }
            .row {
                margin-top:10px;
            }
        </style>
    </head>
    <body>
        <?php
        require 'auxiliar.php';
        ?>
        <div class="container">
            <div class="row">
                <div class="pull-right">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <?= $_SESSION['usuario']['nombre'] ?>
                        <a class="btn btn-info" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="btn btn-info" href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="row">
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?= $_SESSION['mensaje'] ?>
                    </div>
                </div>
                <?php unset($_SESSION['mensaje']) ?>
            <?php endif; ?>
            <?php
            $campo = trim(filter_input(INPUT_GET, 'campo'));
            $texto = trim(filter_input(INPUT_GET, 'texto'));
            ?>
            <div class="row">
                <hr />
                <div class="panel panel-default">
                    <div class="panel-heading">Buscar</div>
                    <div class="panel-body">
                        <form action="index.php" method="get" class="form-inline">
                            <div class="form-group">
                                <select class="form-control" name="campo">
                                    <option value="titulo"
                                        <?= $campo == 'titulo' ? 'selected' : '' ?>
                                        >Título
                                    </option>
                                    <option value="anyo"
                                        <?= $campo == 'anyo' ? 'selected' : '' ?>
                                        >Año
                                    </option>
                                    <option value="sinopsis"
                                        <?= $campo == 'sinopsis' ? 'selected' : '' ?>
                                        >Sinopsis
                                    </option>
                                    <option value="duracion"
                                        <?= $campo == 'duracion' ? 'selected' : '' ?>
                                        >Duración
                                    </option>
                                    <option value="genero_id"
                                        <?= $campo == 'genero_id' ? 'selected' : '' ?>
                                        >Género Id
                                    </option>
                                </select>
                                <input class="form-control" type="text" name="texto" autofocus
                                value="<?= h($texto) ?>" />
                                <button type="submit" value="Buscar" class="btn btn-default">
                                    <span class="glyphicon glyphicon-search"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                if ($texto === '') {
                    $texto = 'true';
                    $operador = '';
                    $campo = '';
                }elseif (is_numeric($texto)) {
                    $texto = (int)$texto;
                    $operador = '=';
                } else {
                    $texto = "lower('%" . $texto . "%')";
                    $operador = "LIKE";
                    $campo = "lower($campo)";
                }
                $pdo = conectar();
                $sql = "SELECT peliculas.id,
                              titulo,
                              anyo,
                              left(sinopsis, 40) AS sinopsis,
                              duracion,
                              genero_id,
                              genero
                         FROM peliculas
                         JOIN generos ON genero_id = generos.id
                        WHERE $campo $operador $texto";
                $sent = $pdo->prepare($sql);
                    // WHERE lower(titulo) LIKE lower('%' || :titulo || '%'"); Operador de concatenación en SQL ||.
                $sent->execute();
                ?>
                <div class="col-md-offset-1 col-md-10">
                    <table id="tabla" class="table table-striped">
                        <thead>
                            <th>Título</th>
                            <th>Año</th>
                            <th>Sinopsis</th>
                            <th>Duración</th>
                            <th>Género</th>
                            <th colspan="2">Operaciones</th>
                        </thead>
                        <tbody>
                            <?php foreach ($sent as $fila): ?>
                                <tr>
                                    <td><?= h($fila['titulo']) ?></td>
                                    <td><?= h($fila['anyo']) ?></td>
                                    <td><?= h($fila['sinopsis']) ?></td>
                                    <td><?= h($fila['duracion']) ?></td>
                                    <td><?= h($fila['genero']) ?></td>
                                    <td>
                                        <a class="btn btn-info btn-xs" href="modificar.php?id=<?= $fila['id'] ?>">
                                            Modificar
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-danger btn-xs" href="borrar.php?id=<?= $fila['id'] ?>">
                                            Borrar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <hr />
                <div class="col-md-offset-4 col-md-4">
                    <a class="btn btn-primary" href="insertar.php">Insertar una nueva película</a>
                </div>
            </div>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
