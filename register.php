<?php require 'config.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $n=trim($_POST['nama']??'');$m=trim($_POST['email']??'');$p=$_POST['password']??'';
  if($n===''||!filter_var($m,FILTER_VALIDATE_EMAIL))$err='Isi nama dan email yang valid.';
  elseif(strlen($p)<6)$err='Kata sandi minimal 6 karakter.';
  else{
    $c=$db->prepare('SELECT id FROM users WHERE email=?');$c->execute([$m]);
    if($c->fetch())$err='Email sudah terdaftar.';
    else{
      $db->prepare('INSERT INTO users(nama,email,password) VALUES(?,?,?)')->execute([$n,$m,password_hash($p,PASSWORD_DEFAULT)]);
      header('Location: login.php');exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Daftar · Cuaca & Musik</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body class="auth">
  <div class="aurora"></div>
  <form class="glass box" method="post">
    <h1>Buat akun</h1>
    <p class="mute">Simpan kota dan lagu favoritmu.</p>
    <?php if($err):?><p class="err"><?=e($err)?></p><?php endif;?>
    <label>Nama<input name="nama" required autofocus></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Kata sandi<input type="password" name="password" minlength="6" required></label>
    <button class="btn">Daftar</button>
    <p class="mute">Sudah punya akun? <a href="login.php">Masuk</a></p>
  </form>
</body>

</html>