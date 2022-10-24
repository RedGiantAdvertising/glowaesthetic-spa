<?php
  include_once('functions.php');
  $check = checkLogin(false);
  if($check) {
    header("Location: dashboard.php");
  }
?>
<!doctype html>
<html lang="fr"  class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Idov Mamane">
    <title>Login</title>
    <!-- Bootstrap core CSS -->
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- override Bootstrap core CSS -->
    <link href="assets/dist/css/bootstrap-override.css" rel="stylesheet">
    <!-- bootstrap-icons core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Font Awesome icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <!-- custom core CSS -->
    <link href="assets/dist/css/custom.css" rel="stylesheet">
  </head>
  <body class="h-100">
    <div class="d-flex h-100 justify-content-center align-items-center">
        <div class="bg-white login-box">
          <h2>Connexion</h2>
          <form>
            <div class="alert" role="alert" id="status" style="display: none;"></div>
            <label for="exampleInputEmail1" class="form-label mb-3">Connectez-vous à votre espace</label>
            <div class="mb-3">
              <div class="input-group">
                <div class="input-group-text"><i class="bi bi-person-circle"></i></div>
                <input type="text" class="form-control" id="username" placeholder="Nom d'utilisateur">
              </div>
            </div>
            <div class="mb-3">
              <div class="input-group">
                <div class="input-group-text"><i class="bi bi-lock"></i></div>
                <input type="password" class="form-control" id="password" placeholder="Mot de passe">
              </div>
            </div>
            <div class="d-flex h-100 justify-content-between align-items-center">
              <button type="button" class="btn btn-primary px-4" id="login">Connexion</button>
            </div>
          </form>
        </div>
    </div>
  </body>
  <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" integrity="sha512-3j3VU6WC5rPQB4Ld1jnLV7Kd5xr+cq9avvhwqzbH/taCRNURoeEpoPBK9pDyeukwSxwRPJ8fDgvYXd6SkaZ2TA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $("#login").click(function() {
        let username = $("#username").val()
        let password = $("#password").val()
        if(!username) {
          $("#status").removeClass("alert-success")
          $("#status").addClass("alert-danger")
          $("#status").html("<b><i class='fa fa-exclamation-triangle'></i> Please enter a username</b>")
          $("#status").show()
          $("#username").addClass("is-invalid")
          setTimeout(() => {
            $("#username").removeClass("is-invalid")
          }, 3000)
          return
        }
        if(!password) {
          $("#status").removeClass("alert-success")
          $("#status").addClass("alert-danger")
          $("#status").html("<b><i class='fa fa-exclamation-triangle'></i> Please enter a password</b>")
          $("#status").show()
          $("#password").addClass("is-invalid")
          setTimeout(() => {
            $("#password").removeClass("is-invalid")
          }, 3000)
          return
        }
        $(this).prop('disabled', true)
        $("#username").prop('disabled', true)
        $("#password").prop('disabled', true)
        $("#status").removeClass("alert-success")
        $("#status").removeClass("alert-danger")
        $("#status").addClass("alert-primary")
        $("#status").html("<b>Connexion en cours... <i class='fa fa-circle-notch fa-spin'></i></b>")
        $("#status").show()
        $.ajax({
          url: "api.php",
          method: "POST",
          data: { username, password, login: 1 },
          success: function(res) {
            $.cookie('token', res.token)
            window.location = "dashboard.php"
          },
          error: function(xhr) {
            $("#status").removeClass("alert-success")
            $("#status").removeClass("alert-primary")
            $("#status").addClass("alert-danger")
            if(xhr.responseJSON) {
              let res = xhr.responseJSON
              if(res.message) {
                $("#status").html(`<b><i class='fa fa-exclamation-circle'></i> ${res.message}</b>`)
              }
              else {
                $("#status").html(`<b><i class='fa fa-exclamation-circle'></i> Some error has occurred</b>`)
              }
            }
            else {
              $("#status").html(`<b><i class='fa fa-exclamation-circle'></i> Some error has occurred</b>`)
            }
            $("#login").prop('disabled', false)
            $("#username").prop('disabled', false)
            $("#password").prop('disabled', false)
          }
        })
      })
    })
  </script>
</html>
