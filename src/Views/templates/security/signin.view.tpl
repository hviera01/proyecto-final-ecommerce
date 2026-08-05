<div class="panel-angosto">
<h2>Crear Cuenta</h2>

{{if errorMessage}}
<p class="mensaje-error">{{errorMessage}}</p>
{{endif errorMessage}}

{{if successMessage}}
<p class="mensaje-ok">{{successMessage}}</p>
{{endif successMessage}}

<form method="post" action="index.php?page=Sec_Register">
  <p>
    <label>Nombre de usuario</label><br>
    <input type="text" name="username" required>
  </p>
  <p>
    <label>Correo electronico</label><br>
    <input type="email" name="useremail" required>
  </p>
  <p>
    <label>Contrasena (minimo 8 caracteres)</label><br>
    <input type="password" name="userpswd" required minlength="8">
  </p>
  <p>
    <label>Confirmar contrasena</label><br>
    <input type="password" name="userpswd2" required minlength="8">
  </p>
  <p>
    <button type="submit">Registrarme</button>
  </p>
</form>

<p>Ya tiene cuenta? <a href="index.php?page=Sec_Login">Iniciar sesion</a></p>
</div>
