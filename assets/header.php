<?php
// header.php
// Stil izolasyonu için Shadow DOM kullanılır. Eğer Shadow DOM yoksa fallback görünür kalır.

// Şu anki URI bilgisini al
$currentPage = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
?>

<!-- ROOT: Shadow DOM burada oluşturulacak -->
<div id="fc-header-root" aria-hidden="true"></div>

<!-- FALLBACK (JS yoksa veya Shadow DOM oluşturulmadan önce görünür) -->
<div id="fc-header-fallback" role="banner">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
  <header class="site-header">
    <div class="header-container">
      <div class="logo">First Class</div>

      <nav class="nav" id="navMenu-fallback">
        <a href="/firstclass.php" class="<?php echo ($currentPage === 'firstclass.php') ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-6.58 8-12A8 8 0 1 0 4 10c0 5.42 8 12 8 12Z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
          Havale/EFT
        </a>
        <a href="/dekont.php" class="<?php echo ($currentPage === 'dekont.php') ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
          Dekont
        </a>
      </nav>

      <button class="menu-toggle" id="menuToggle-fallback" aria-label="Menüyü Aç/Kapat">
        ☰
      </button>
    </div>
  </header>

  <style>
  /* ===== FALLBACK STYLES (Shadow DOM da yoksa kullanılacak) - NAMESPACE: #fc-header-fallback ===== */
  #fc-header-fallback .site-header {
    background: #181a27;
    color: #fff;
    font-family: 'Quicksand', sans-serif;
    box-shadow: 0 2px 10px rgba(0,0,0,0.5);
    position: sticky;
    top: 0;
    z-index: 100;
  }

  #fc-header-fallback .header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  #fc-header-fallback .logo {
    font-size: 20px;
    font-weight: 700;
    color: #4da3ff;
    letter-spacing: 0.5px;
  }

  #fc-header-fallback .nav {
    display: flex;
    gap: 26px;
  }

  #fc-header-fallback .nav a {
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    position: relative;
    padding: 6px 0;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color .25s ease;
  }

  #fc-header-fallback .nav a svg {
    width: 15px;
    height: 15px;
    stroke: #fff;
    flex-shrink: 0;
  }

  #fc-header-fallback .nav a:hover {
    color: #4da3ff;
  }

  #fc-header-fallback .nav a::after {
    content: "";
    position: absolute;
    bottom: -6px;
    left: 0;
    width: 0;
    height: 2px;
    background: #4da3ff;
    transition: width .25s ease;
    border-radius: 2px;
  }

  #fc-header-fallback .nav a:hover::after {
    width: 100%;
  }

  #fc-header-fallback .nav a.active {
    color: #4da3ff;
  }
  #fc-header-fallback .nav a.active::after {
    width: 100%;
  }

  #fc-header-fallback .menu-toggle {
    display: none;
    background: none;
    border: none;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
  }

  @media (max-width: 768px) {
    #fc-header-fallback .nav {
      position: absolute;
      top: 64px;
      right: 14px;
      flex-direction: column;
      background: #23263a;
      border: 1px solid #333;
      border-radius: 10px;
      padding: 12px;
      display: none;
      box-shadow: 0 6px 20px rgba(0,0,0,0.35);
      max-height: calc(100vh - 80px);
      overflow-y: auto;
    }
    #fc-header-fallback .nav a {
      padding: 10px 14px;
      border-radius: 6px;
      font-size: 14px;
    }
    #fc-header-fallback .nav a:hover {
      background: #2e324a;
    }
    #fc-header-fallback .menu-toggle {
      display: block;
    }
    #fc-header-fallback .nav.show {
      display: flex;
    }
  }
  </style>
</div>

<!-- TEMPLATE: Bu içerik Shadow DOM içine klonlanacak (PHP echo'ları burada da çalışır) -->
<template id="fc-header-template">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
  <header class="site-header">
    <div class="header-container">
      <div class="logo">First Class</div>

      <nav class="nav" id="navMenu">
        <a href="/firstclass.php" class="<?php echo ($currentPage === 'firstclass.php') ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-6.58 8-12A8 8 0 1 0 4 10c0 5.42 8 12 8 12Z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
          Havale/EFT
        </a>
        <a href="/dekont.php" class="<?php echo ($currentPage === 'dekont.php') ? 'active' : ''; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
          Dekont
        </a>
      </nav>

      <button class="menu-toggle" id="menuToggle" aria-label="Menüyü Aç/Kapat">
        ☰
      </button>
    </div>
  </header>

  <style>
  /* ===== SHADOW DOM STYLES: tam izole ===== */
  :host {
    all: initial; /* Shadow host içindeki varsayılan stilleri sıfırlar (tam izolasyon). */
    display: block;
  }

  .site-header {
    background: #181a27;
    color: #fff;
    font-family: 'Quicksand', sans-serif;
    box-shadow: 0 2px 10px rgba(0,0,0,0.5);
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .logo {
    font-size: 20px;
    font-weight: 700;
    color: #4da3ff;
    letter-spacing: 0.5px;
  }

  .nav {
    display: flex;
    gap: 26px;
  }

  .nav a {
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    position: relative;
    padding: 6px 0;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color .25s ease;
  }

  .nav a svg {
    width: 15px;
    height: 15px;
    stroke: #fff;
    flex-shrink: 0;
  }

  .nav a:hover {
    color: #4da3ff;
  }

  .nav a::after {
    content: "";
    position: absolute;
    bottom: -6px;
    left: 0;
    width: 0;
    height: 2px;
    background: #4da3ff;
    transition: width .25s ease;
    border-radius: 2px;
  }

  .nav a:hover::after {
    width: 100%;
  }

  .nav a.active {
    color: #4da3ff;
  }
  .nav a.active::after {
    width: 100%;
  }

  .menu-toggle {
    display: none;
    background: none;
    border: none;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
  }

  @media (max-width: 768px) {
    .nav {
      position: absolute;
      top: 64px;
      right: 14px;
      flex-direction: column;
      background: #23263a;
      border: 1px solid #333;
      border-radius: 10px;
      padding: 12px;
      display: none;
      box-shadow: 0 6px 20px rgba(0,0,0,0.35);
      max-height: calc(100vh - 80px);
      overflow-y: auto;
    }
    .nav a {
      padding: 10px 14px;
      border-radius: 6px;
      font-size: 14px;
    }
    .nav a:hover {
      background: #2e324a;
    }
    .menu-toggle {
      display: block;
    }
    .nav.show {
      display: flex;
    }
  }
  </style>
</template>

<script>
(function() {
  // Eğer Shadow DOM destekleniyorsa template'i göm ve fallback'i kaldır
  const root = document.getElementById('fc-header-root');
  const template = document.getElementById('fc-header-template');
  const fallback = document.getElementById('fc-header-fallback');

  try {
    if (root && template && root.attachShadow) {
      const shadow = root.attachShadow({ mode: 'open' });
      shadow.appendChild(template.content.cloneNode(true));

      // Menü butonunu shadow içinde bağla
      const menuToggle = shadow.getElementById('menuToggle');
      const navMenu = shadow.getElementById('navMenu');
      if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => {
          navMenu.classList.toggle('show');
        });
      }

      // Fallback'i DOM'dan kaldır (varsayılan görünürlükten kurtar)
      if (fallback && fallback.parentNode) {
        fallback.parentNode.removeChild(fallback);
      }

      // root artık erişilebilir, aria-hidden false
      root.setAttribute('aria-hidden', 'false');
    } else {
      // Shadow DOM yoksa fallback üzerinde çalışılacak (mobil menü toggling)
      const menuToggleFallback = document.getElementById('menuToggle-fallback');
      const navMenuFallback = document.getElementById('navMenu-fallback');
      if (menuToggleFallback && navMenuFallback) {
        menuToggleFallback.addEventListener('click', () => {
          navMenuFallback.classList.toggle('show');
        });
      }
    }
  } catch (e) {
    // Hata durumunda fallback kalan tek çözüm olur; konsola hatayı yaz
    if (window.console && console.error) console.error('Header Shadow DOM init error:', e);
  }
})();
</script>
