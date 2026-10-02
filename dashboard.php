<?php require 'config.php'; need_login(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf" content="<?=e($_SESSION['csrf'])?>">
  <title>Dashboard · Cuaca & Musik</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>
  <div class="aurora"></div>
  <header><a class="brand" href="dashboard.php"><span class="brand-mark"><img src="assets/img/logodtzy.png" alt="Logo DTZY"></span> <span>Cuaca &amp; Musik</span></a><span
      class="account">Halo, <?=e($_SESSION['nama'])?> <a href="logout.php">Keluar</a></span></header>
  <main class="grid">
    <section>
      <form id="f" class="search"><input id="q" placeholder="Cari kota…" aria-label="Kota"><button
          class="btn">Cari</button></form>
      <div id="chips" class="chips"></div>
      <div id="cuaca" class="glass hero">
        <p class="mute">Memuat cuaca…</p>
      </div>
      <section class="glass collection-summary" aria-label="Ringkasan koleksi">
        <h2>Ruang koleksimu</h2>
        <div class="summary-items">
          <div class="summary-card favorite-summary"><span class="summary-icon" aria-hidden="true">♥</span>
            <p><span>Lagu favorit</span><strong id="favorite-count">—</strong><small>tersimpan di koleksimu</small></p>
          </div>
          <div class="summary-card city-summary"><span class="summary-icon city-icon" aria-hidden="true">⌖</span>
            <p><span>Kota tersimpan</span><strong id="city-count">—</strong><small>pernah kamu cari</small></p>
          </div>
        </div>
      </section>
    </section>
    <section class="glass music">
      <div class="section-heading">
        <div>
          <p class="eyebrow">TEMANI HARI INI</p>
          <h2>Musik pilihan</h2>
        </div>
        <div class="tabs"><button id="t1" class="on">Rekomendasi</button><button id="t2">Favorit</button></div>
      </div>
      <form id="music-search" class="music-search" role="search">
        <input id="music-query" type="search" placeholder="Judul lagu atau nama artis…"
          aria-label="Cari lagu atau artis">
        <button class="btn" type="submit">Cari musik</button>
      </form>
      <p id="mood" class="mute"></p>
      <p id="music-status" class="music-status" role="status" aria-live="polite"></p>
      <ul id="list"></ul>
    </section>
  </main>
  <div class="player" id="player"><span id="np"></span><audio id="au" controls></audio></div>
  <script src="assets/app.js"></script>
</body>

</html>