<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Listado de películas</title>
        <style>
            #buscar {
                margin-bottom: 20px;
            }
            #tabla {
                margin: auto;
            }
        </style>
    </head>
    <body>
        <?php
        $titulo = trim(filter_input(INPUT_GET, 'titulo'));
        require 'auxiliar.php';
        ?>
        <div id="buscar">
            <form action="index.php" method="get">
                <fieldset>
                    <legend>Título:</legend>
                    <input type="text" name="titulo" autofocus
                           value="<?= h($titulo) ?>" />
                    <input type="submit" value="Buscar" />
                </fieldset>
            </form>
        </div>
        <?php
        $pdo = conectar();
        $sent = $pdo->prepare("SELECT *
                                  FROM peliculas
                                 WHERE lower(titulo) LIKE lower(:titulo)");
                                 // WHERE lower(titulo) LIKE lower('%' || :titulo) || '%'"); Operador de concatenación en SQL ||.
        $sent->execute([':titulo' => "%$titulo%"]);
        // Podemos quitar esta fila porque es iterable
        // $filas = $query->fetchAll();
        ?>
        <div >
            <table border="1" id="tabla">
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
                            <td><?= h($fila['genero_id']) ?></td>
                            <td>
                                <a href="modificar.php?id=<?= $fila['id'] ?>">
                                    Modificar
                                </a>
                            </td>
                            <td>
                                <a href="borrar.php?id=<?= $fila['id'] ?>">
                                    Borrar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="insertar.php">Insertar una nueva película</a>
    </body>
</html>
