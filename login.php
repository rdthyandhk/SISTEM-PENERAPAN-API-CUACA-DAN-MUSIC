<?php require 'config.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $s=$db->prepare('SELECT * FROM users WHERE email=?');$s->execute([trim($_POST['email']??'')]);
  $u=$s->fetch();
  if($u&&password_verify($_POST['password']??'',$u['password'])){
    session_regenerate_id(true);$_SESSION['uid']=$u['id'];$_SESSION['nama']=$u['nama'];
    $_SESSION['csrf']=bin2hex(random_bytes(16));
    header('Location: dashboard.php');exit;
  }
  $err='Email atau kata sandi salah.';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Masuk · Cuaca & Musik</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body class="auth">
  <div class="aurora"></div>
  <form class="glass box" method="post">
    <h1>Selamat datang kembali</h1>
    <p class="mute">Masuk untuk melihat cuaca dan musik favoritmu.</p>
    <?php if($err):?><p class="err"><?=e($err)?></p><?php endif;?>
    <label>Email<input type="email" name="email" required autofocus></label>
    <label>Kata sandi<input type="password" name="password" required></label>
    <button class="btn">Masuk</button>
    <p class="mute">Belum punya akun? <a href="register.php">Daftar</a></p>
  </form>
</body>

</html>