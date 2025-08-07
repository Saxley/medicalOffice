<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Cita Médica</title>
</head>
<body>

    <h1>Formulario de Cita Médica</h1>

    <form action="procesar_cita.php" method="post">
        <label for="nombre">Nombre completo:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="email">Correo electrónico:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="telefono">Teléfono:</label><br>
        <input type="tel" id="telefono" name="telefono" required><br><br>

        <label for="fecha">Fecha de la cita:</label><br>
        <input type="date" id="fecha" name="fecha" required><br><br>

        <label for="hora">Hora de la cita:</label><br>
        <input type="time" id="hora" name="hora" required><br><br>

        <label for="motivo">Motivo de la consulta:</label><br>
        <textarea id="motivo" name="motivo" rows="4" cols="50" required></textarea><br><br>

        <button type="submit">Agendar Cita</button>
    </form>

</body>
</html>