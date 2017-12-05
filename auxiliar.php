<?php

const PELICULA_DEFECTO = [
    'titulo' => '',
    'anyo' => '',
    'sinopsis' => '',
    'duracion' => '',
    'genero_id' => '',
];

function obtenerParametros(string $parametro, array $defecto):array
{
    $ret = filter_input(INPUT_POST, $parametro, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
    $ret = array_map('trim', $ret);
    $ret = array_merge($defecto, $ret);
    return $ret;
}

/**
 * Crea una conexión a la base de datos y la devuelve
 * @return PDO          La instancia de la clase PDO que representa la conexión
 * @throws PDOException Si se produce algún error que impide la conexión
 */
function conectar(): PDO
{
    try {
        return new PDO('pgsql:host=localhost;dbname=fa', 'fa', 'fa');
    } catch (PDOException $e) {
        ?>
        <h1>Error catastrófico de base de datos: no se puede continuar</h1>
        <?php
        throw $e;
    }
}

/**
 * Busca una película a partir de su ID
 * @param  PDO       $pdo   La conexión a la base de datos
 * @param  int       $id    El ID de la película
 * @param  array     $error El array de errores
 * @return array            Devuelve la fila que contiene los datos de la película
 * @throws Exception        Si la película no existe
 */
function buscarPelicula(PDO $pdo, int $id, array &$error): array
{
    $sent = $pdo->prepare("SELECT *
                          FROM peliculas
                          WHERE id = :id");
    $sent->execute([':id' => $id]);
    $fila = $sent->fetch();
    if (empty($fila)) {
        $error[] = 'La película no existe';
        throw new Exception;
    }
    return $fila;
}

/**
 * Borra una película a partir de su ID
 * @param  PDO   $pdo   La conexión a la base de datos
 * @param  int   $id    El ID de la película
 * @param  array $error El array de errores
 */
function borrarPelicula(PDO $pdo, int $id, array &$error): void
{
    $sent = $pdo->prepare("DELETE FROM peliculas
                                  WHERE id = :id");
    $sent->execute([':id'=> $id]);
    if ($sent->rowCount() !== 1) {
        $error[] = 'Ha ocurrido un error al eliminar la película';
    }
}

/**
 * Comprueba si un parámetro es correcto.
 *
 * Un parámetro se considera correcto si ha superado los filtros de validación
 * de filter_input(). Si el parámetro no existe, entendemos que su valor también
 * es false, con lo cual sólo tenemos que comprobar si el valor no es false.
 * @param mixed      $param El parámetro a comprobar
 * @param array      $error El array de errores
 * @throws Exception        Si el parámetro no es correcto
 */
function comprobarParametro($param, array &$error): void
{
    if ($param === false) {
        $error[] = 'Parámetro incorrecto';
        throw new Exception;
    }
}

/**
 * Muestra un enlace a la página principal index.php con el texto 'Volver'
 */
function volver():void
{
    ?>
    <a class="btn btn-info" href="index.php">Volver</a>
    <?php
}

/**
 * Escapa una cadena correctamente
 * @param  string $cadena La cadena a escapar
 * @return string         La cadena escapada
 */
function h(?string $cadena): string
{
    return htmlspecialchars($cadena, ENT_QUOTES | ENT_SUBSTITUTE);
}

/**
 * Muestra en pantalla los mensajes de error capturados
 * @param array $error Los mensajes capturados
 */
function mostrarErrores(array $error): void
{
    foreach ($error as $v):
    ?>
    <div class="alert alert-danger">
        <p>Error: <?= h($v) ?></p>
    </div>
    <?php
    endforeach;
}

function comprobarTitulo(string $titulo, array &$error): void
{
    if ($titulo === '') {
        $error[] = "El título es obligatorio";
        return;
    }
    if (mb_strlen($titulo) > 255) {
        $error[] = "El título es demasiado largo";
    }
}

function comprobarAnyo(string $anyo, array &$error): void
{
    if ($anyo === '') {
        return;
    }
    $filtro =filter_var($anyo, FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 0,
            'max_range' => 9999,
        ],
    ]);
    if ($filtro === false) {
        $error[] = "No es un año válido";
    }
}

function comprobarDuracion(string $duracion, &$error): void
{
    if ($duracion === '') {
        return;
    }
    $filtro = filter_var($duracion, FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 0,
            'max_range' => 32767,
        ],
    ]);
    if ($filtro === false) {
        $error[] = "No es una duración válida";
    }
}

function comprobarGenero(PDO $pdo, $genero_id, array &$error): void
{
    if ($genero_id === '') {
        $error[] = 'El género es obligatorio';
        return;
    }
    $filtro = filter_var($genero_id, FILTER_VALIDATE_INT);
    if ($filtro === false) {
        $error[] = 'El género debe ser un número entero';
        return;
    }
    $sent = $pdo->prepare('SELECT COUNT(*)
                             FROM generos
                            WHERE id = :genero_id');
    $sent->execute([':genero_id' => $genero_id]);
    if ($sent->fetchColumn() === 0) {
        $error[] = 'El género no existe';
    }
}

function comprobarErrores(array $error): void
{
    if (!empty($error)) {
        throw new Exception;
    }
}

function insertar(PDO $pdo, array $valores): void
{
    $cols = array_keys($valores);
    $vals = array_fill(0, count($valores), '?');
    $sql = 'INSERT INTO peliculas ( ' . implode(', ', $cols) . ')'
                         . 'VALUES (' . implode(', ', $vals) . ')';
    $send = $pdo->prepare($sql);
    $send->execute(array_values($valores));
}

function comp($valor)
{
    return $valor !== '';
}

function modificar(PDO $pdo, int $id, array $valores): void
{
    $sets = [];
    foreach($valores as $k => $v) {
        $sets[] = $v === '' ? "$k = NULL" : "$k = ?";
    }

    $set = implode(', ', $sets);
    $sql = "UPDATE peliculas
               SET $set
             WHERE id = ?";
    $exec = array_values(array_filter($valores, 'comp'));
    $exec[] = $id;
    $sent = $pdo->prepare($sql);
    $sent->execute($exec);
}

function formulario(array $datos, ?int $id, array $generos): void
{
    if ($id === null) {
        $destino = 'insertar.php';
        $boton = 'Insertar';
    } else {
        $destino = "modificar.php?id=$id";
        $boton = 'Modificar';
    }
    extract($datos);
    ?>
    <form action="<?= $destino ?>" method="post">
        <div class="form-group">
            <label for="titulo">Título*:</label>
            <input id="titulo" class="form-control" type="text" name="pelicula[titulo]"
                   value="<?= h($titulo) ?>">
        </div>
        <div class="form-group">
            <label for="anyo">Año:</label>
            <input id="anyo" class="form-control" type="text" name="pelicula[anyo]"
                   value="<?= h($anyo) ?>">
        </div>
        <div class="form-group">
            <label for="sinopsis">Sinopsis:</label>
            <textarea class="form-control"
            id="sinopsis"
            name="pelicula[sinopsis]"
            rows="8"
            cols="70"
            ><?= h($sinopsis) ?></textarea>
        </div>
        <div class="form-group">
            <label for="duracion">Duración:</label>
            <input id="duracion" class="form-control" type="text" name="pelicula[duracion]"
            value="<?= h($duracion) ?>">
        </div>
        <div class="form-group">
            <label for="genero_id">Género*:</label>
            <select id="genero_id" class="form-control" name="pelicula[genero_id]">
        </div>
        <?php
        foreach ($generos as $v):
        ?>
            <option value="<?= h($v['id']) ?>"
                <?=  $v['id'] == $genero_id ? 'selected' : ''?>>
                <?= h($v['genero']) ?>
             </option>
        <?php
        endforeach;
        ?>
        </select>

        <hr />

        <input type="submit" class="btn btn-success" value="<?= $boton ?>">
        <a class="btn btn-danger" href="index.php">Cancelar</a>
    </form>
    <?php
}

function generos($pdo): array
{
    return $pdo->query('SELECT *
                          FROM generos')->fetchAll();
}

function comprobarUsuario(string $usuario, array &$error): void
{
    if ($usuario === '') {
        $error[] = 'El usuario es obligatorio';
        return;
    }
    if (mb_strlen($usuario) > 255) {
        $error[] = 'El usuario es demasiado largo';
    }
    if (mb_strpos($usuario, ' ') !== false) {
        $error[] = 'El usuario no puede contener espacios';
    }
}

function comprobarPassword(string $password, array &$error): void
{
    if ($password === '') {
        $error[] = 'La contraseña es obligatoria';
    }
}

function buscarUsuario(string $usuario, string $password, array &$error): array
{
    $pdo = conectar();
    $sent = $pdo->prepare('SELECT *
                     FROM usuarios
                    WHERE usuario = :usuario');
    $sent->execute(['usuario' => $usuario]);
    $fila = $sent->fetch();
    if (empty($fila)) {
        $error[] = 'El usuario no existe';
        throw new Exception;
    }
    if (!password_verify($password, $fila['password'])) {
        $error[] = 'La contraseña no coincide';
        throw new Exception;
    }
    return $fila;
}

function comprobarLogueado(): bool
{
    if(!isset($_SESSION['usuario'])) {
        $_SESSION['mensaje'] = 'Usuario no identificado';
        header('Location: index.php');
        return false;
    }
    return true;
}
