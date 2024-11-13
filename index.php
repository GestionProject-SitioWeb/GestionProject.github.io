<?php
ob_start();
?>

<!DOCTYPE html>
<html>

<head>
  <title>Gestion de Turnos</title>
  <link rel="shortcut icon" href="frontend/ico/escudo.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="frontend/css/estilos.css">
  <link rel="stylesheet" type="text/css" href="frontend/css/captcha.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="frontend/js/js.js"> </script>
</head>

<body>

  <!--La imagen de siderbar-->
  <div class="contenedor">
    <div class="fondo">
      <img src="frontend/img/fondo.jpg">
    </div>

    <div class="titulo">
      <div class="topnav" id="myTopnav">
        <a href="#"><img src="frontend/img/sep.png" alt="sep" class="sep"></a>
        <a href="#"><img src="frontend/img/tecno.png" alt="tecno" class="tecno"></a>
        <a href="#"><img src="frontend/img/escudo.png" alt="Escudo" class="escudo"></a>
        <div class="center-container">
          <span>Gestion de Turnos</span>
        </div>
      </div>
    </div> 

    <div class="formulario">
      <div class="cuadro">
        <div class="tab">
          <button class="tablinks" onclick="openCity(event, 'Alumno')">Alumno</button>
          <button class="tablinks" onclick="openCity(event, 'Personal')">Personal</button>
          <button class="tablinks" onclick="openCity(event, 'Administrador')">Administrador</button>
        </div>
      </div>

      <!--Panel para el ingreso del alumno  -->
      <div id="Alumno" class="tabcontent">
        <div class="formulario">
          <div class="cuadro">
            <div class="formu login-container">
              <form method="post" action="" onsubmit="return validateCaptcha(1)">
                <label>Acceso al Alumno</label>
                <br>
                <br>
                <?php
                include("conexiones/conexion.php");
                include("conexiones/login_estudiante.php");
                ?>
                <input type="text" name="n_control" id="n_control" maxlength="10" pattern="[0-9]{1,10}" title="No de Control" placeholder="No. de Control" required="" autofocus="">
                <input type="password" name="password1" id="password1" maxlength="10" pattern="[0-9]{1,10}" placeholder="NIP" title="Solo números." required="">
                <div class="input-group-append">
                <button class="btn btn-outline-secondary icon fa fa-eye-slash" type="button"  onclick="mostrarPassword('password1', this)"></button>
                </div>

                <br>
                <!-- CAPTCHA -->
                <div class="captcha" id="captchaCode1"></div>
                <input type="text" id="captchaInput1" placeholder="Escribe el CAPTCHA" required><br>
                <div class="error" id="errorMessage1"></div>
                <button type="submit" name="btningresar" class="btn btn-primary w-100" value="Iniciar sesión">Iniciar sesión</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!--Panel para el ingreso del Maestro -->
      <div id="Personal" class="tabcontent">
        <div class="formulario">
          <div class="cuadro">
            <div class="formu login-container">
              <form method="post" action="" onsubmit="return generateCaptcha(2)">
                <label>Acceso al Personal</label>
                <br>
                <br>
                <?php
                include("conexiones/conexion.php");
                include("conexiones/login_personal.php");
                ?>
                <input type="text" id="usuario" name="usuario" pattern="[A-Za-z0-9]{3,50}" title="Usuario alfanumérico entre 3 y 50 caracteres" placeholder="Usuario" required="">
                <input type="password" name="password2" id="password2" pattern="{6,10}" placeholder="Contraseña" title="Contraseña de al menos 6 caracteres" required="">
                <div class="input-group-append">
                <button class="btn btn-outline-secondary icon fa fa-eye-slash" type="button"  onclick="mostrarPassword('password2', this)"></button>
                </div>

                <br>
                <!-- CAPTCHA -->
                <div class="captcha" id="captchaCode2"></div>
                <input type="text" id="captchaInput2" placeholder="Escribe el CAPTCHA" required><br>
                <div class="error" id="errorMessage2"></div>
                <button type="submit" name="btningresar" class="btn btn-primary w-100" value="Iniciar sesión">Iniciar sesión</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!--Panel para el ingreso del administrador -->
      <div id="Administrador" class="tabcontent">
        <div class="formulario">
          <div class="cuadro">
            <div class="formu login-container">
              <form method="post" action="" onsubmit="return validateCaptcha(3)">
                <label>Acceso al Administrador</label>
                <br>
                <br>
                <?php
                include("conexiones/conexion.php");
                include("conexiones/login_administrador.php");
                ?>
                <input type="text" id="correo" name="correo" title="Usuario alfanumérico entre 3 y 20 caracteres" placeholder="administrador" required="">
                <input type="password" id="password3" name="password3" placeholder="Contraseña" title="La contraseña debe tener al menos 8 caracteres, incluyendo letras y números." required>
                <div class="input-group-append">
                <button class="btn btn-outline-secondary icon fa fa-eye-slash" type="button"  onclick="mostrarPassword('password3', this)"></button>
                </div>
                <br>
                <!-- CAPTCHA -->
                <!-- CAPTCHA para Login 3 -->
                <div class="captcha" id="captchaCode3"></div>
                <input type="text" id="captchaInput3" placeholder="Escribe el CAPTCHA" required><br>

                <div class="error" id="errorMessage3"></div>

                <button type="submit" name="btningresar" class="btn btn-primary w-100" value="Iniciar sesión">Iniciar sesión</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      


</body>

</html>

<script>
  // Generar CAPTCHA para cada formulario
  function generateCaptcha(loginId) {
    let captchaCode = Math.random().toString(36).substring(2, 8); // Genera una cadena aleatoria de 6 caracteres
    document.getElementById('captchaCode' + loginId).textContent = captchaCode;
    return captchaCode;
  }

  // Inicializar los CAPTCHAs para cada formulario
  let captcha1 = generateCaptcha(1);
  let captcha2 = generateCaptcha(2);
  let captcha3 = generateCaptcha(3);

  // Validar CAPTCHA según el formulario (loginId = 1, 2 o 3)
  function validateCaptcha(loginId) {
    let userCaptcha = document.getElementById('captchaInput' + loginId).value;
    let errorMessage = document.getElementById('errorMessage' + loginId);

    // Verificar el CAPTCHA adecuado para cada login
    let generatedCaptcha;
    if (loginId === 1) generatedCaptcha = captcha1;
    else if (loginId === 2) generatedCaptcha = captcha2;
    else generatedCaptcha = captcha3;

    if (userCaptcha === generatedCaptcha) {
      errorMessage.textContent = ""; // CAPTCHA correcto
      return true; // Permitir el envío del formulario
    } else {
      errorMessage.textContent = "CAPTCHA incorrecto. Intenta de nuevo.";
      document.getElementById('captchaInput' + loginId).value = ""; // Limpiar el campo CAPTCHA
      // Generar un nuevo CAPTCHA solo para este login
      if (loginId === 1) captcha1 = generateCaptcha(1);
      else if (loginId === 2) captcha2 = generateCaptcha(2);
      else captcha3 = generateCaptcha(3);
      return false; // Bloquear el envío del formulario
    }
  }
</script>
<script type="text/javascript">
  function mostrarPassword(inputId, icon) {
    var cambio = document.getElementById(inputId);
    if (cambio.type === "password") {
      cambio.type = "text";
      $(icon).removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
      cambio.type = "password";
      $(icon).removeClass('fa-eye').addClass('fa-eye-slash');
    }
  }
</script>
<?php
ob_end_flush();
?>