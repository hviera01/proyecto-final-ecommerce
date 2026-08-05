<div class="panel-angosto">
<h2>Iniciar Sesion</h2>

{{if errorMessage}}
<p class="mensaje-error">{{errorMessage}}</p>
{{endif errorMessage}}

<form method="post" action="index.php?page=Sec_Login">
  <p>
    <label>Correo electronico</label><br>
    <input type="email" name="useremail" required>
  </p>
  <p>
    <label>Contrasena</label><br>
    <input type="password" name="userpswd" required>
  </p>
  <p>
    <button type="submit">Ingresar</button>
  </p>
</form>

<p>No tiene cuenta? <a href="index.php?page=Sec_Register">Crear cuenta</a></p>
</div>
