<form method="POST" action="12procesa.php">
  Nombre: <input type="text" name="nombre"><br><br>

  Edad: <input type="number" name="edad"><br><br>

  Email: <input type="text" name="email"><br><br>

  Fecha de nacimiento: <input type="date" name="fecha_nac"><br><br>

  Rol:
  <select name="rol">
    <option value="">-- Selecciona --</option>
    <option value="admin">Admin</option>
    <option value="user">User</option>
    <option value="guest">Guest</option>
  </select><br><br>

  Acepto términos: <input type="checkbox" name="terminos" value="1"><br><br>

  <button type="submit">Enviar</button>
</form>
