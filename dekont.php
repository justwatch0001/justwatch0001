<?php include "assets/header.php"; ?>

<?php
// --- Varsayılan / sabit değerler (güncellendi) ---
$defaultIban = "TR00 0000 0000 0000 0000 0000 01";
$defaultAdSoyad = "-";
$defaultTutar = "00,00";
$defaultAciklama = "9415 nolu seri transfer"; // <-- güncellendi
$defaultVergiDairesi = "AYDIN"; // <-- güncellendi
$defaultDoviz = "TL";

// --- Form verileri al ---
$iban_raw = isset($_POST['iban']) ? trim($_POST['iban']) : '';
$adsoyad_raw = isset($_POST['adsoyad']) ? trim($_POST['adsoyad']) : '';
$tutar_raw = isset($_POST['tutar']) ? trim($_POST['tutar']) : '';
$aciklama_raw = isset($_POST['aciklama']) ? trim($_POST['aciklama']) : '';
$vdairesi_raw = isset($_POST['vergi_dairesi']) ? trim($_POST['vergi_dairesi']) : '';
$doviz_raw = isset($_POST['doviz']) ? trim($_POST['doviz']) : '';

$iban_input = $iban_raw !== '' ? $iban_raw : $defaultIban;
$adsoyad_input = $adsoyad_raw !== '' ? $adsoyad_raw : $defaultAdSoyad;
$tutar_input = $tutar_raw !== '' ? $tutar_raw : $defaultTutar;
$aciklama_input = $aciklama_raw !== '' ? $aciklama_raw : $defaultAciklama;
$vdairesi_input = $vdairesi_raw !== '' ? $vdairesi_raw : $defaultVergiDairesi;
$doviz_input = $doviz_raw !== '' ? $doviz_raw : $defaultDoviz;

// --- IBAN doğrulama (mod-97) ---
function iban_is_valid($iban) {
    $iban = strtoupper(preg_replace('/\s+/', '', $iban));
    if (strlen($iban) < 15) return false;
    $rearr = substr($iban, 4) . substr($iban, 0, 4);
    $numeric = '';
    foreach (str_split($rearr) as $ch) {
        if (ctype_alpha($ch)) $numeric .= (ord($ch) - 55);
        else $numeric .= $ch;
    }
    if (function_exists('bcmod')) {
        return bcmod($numeric, '97') == '1';
    } else {
        $remainder = intval(substr($numeric, 0, 9)) % 97;
        $rest = substr($numeric, 9);
        while ($rest !== '') {
            $chunk = substr($rest, 0, 7);
            $rest = substr($rest, 7);
            $remainder = intval($remainder . $chunk) % 97;
        }
        return $remainder == 1;
    }
}

// --- IBAN gösterim formatı ---
$iban_normal = strtoupper(preg_replace('/\s+/', '', $iban_input));
$iban_display = trim(chunk_split($iban_normal, 4, ' '));
$iban_display_safe = htmlspecialchars($iban_display, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

// --- Tutar normalize ---
$tutar_tmp = str_replace(["\xc2\xa0", ' '], ['', ''], $tutar_input);
$tutar_tmp = str_replace('.', '', $tutar_tmp);
$tutar_tmp = str_replace(',', '.', $tutar_tmp);
$amount = is_numeric($tutar_tmp) ? floatval($tutar_tmp) : 0.0;
$tutar_display = number_format($amount, 2, ',', '.');

// Güvenli sürümler
$adsoyad_display = htmlspecialchars($adsoyad_input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$aciklama_display = htmlspecialchars($aciklama_input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$vdairesi_display = htmlspecialchars($vdairesi_input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$doviz_display = htmlspecialchars($doviz_input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

// IBAN geçerliliği
$iban_valid = iban_is_valid($iban_input);

// İşlem tarihi (sadece tarih)
$islem_tarihi = date("d.m.Y");

// QR yerine sabit resim
$qr_img_src = "img/qG9vLt.png";
?>

<!-- Quicksand font -->
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;600;700&display=swap" rel="stylesheet">

<!-- Set page title (assets/header.php muhtemelen head içinde değilse garanti için JS ile) -->
<script>document.title = "First Class - Dekont";</script>

<!-- --- FORM --- -->
<div class="form-wrap">
    <form id="dekontForm" method="post" class="form-grid">
        <div class="form-row full">
            <label>Ad Soyad / Ünvan</label>
            <input name="adsoyad" type="text" value="<?php echo htmlspecialchars($adsoyad_input); ?>" required>
        </div>

        <div class="form-row">
            <label>IBAN</label>
            <input id="ibanInput" name="iban" type="text" value="<?php echo htmlspecialchars($iban_input); ?>" required>
        </div>

        <div class="form-row">
            <label>Tutar</label>
            <input name="tutar" type="text" value="<?php echo htmlspecialchars($tutar_input); ?>" required>
        </div>

        <div class="form-row full">
            <label>Açıklama</label>
            <input name="aciklama" type="text" value="<?php echo htmlspecialchars($aciklama_input); ?>">
        </div>

        <div class="form-row">
            <label>Müşteri Vergi Dairesi</label>
            <input name="vergi_dairesi" type="text" value="<?php echo htmlspecialchars($vdairesi_input); ?>" required>
        </div>

        <div class="form-row">
            <label>Döviz Cinsi</label>
            <input name="doviz" type="text" value="<?php echo htmlspecialchars($doviz_input); ?>" required>
        </div>

        <div class="form-row actions">
            <button type="submit" class="btn">Dekont Oluştur</button>
            <button type="button" class="btn outline" id="downloadPdf">İndir</button>
        </div>
    </form>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$iban_valid): ?>
    <div class="alert">Uyarı: Girdiğiniz IBAN geçerli görünmüyor. Lütfen kontrol edin.</div>
<?php endif; ?>

<!-- --- DEKONT (MODERN TASARIM) --- -->
<div class="dekont" id="dekont">
    <header class="dekont-header">
        <img src="img/yapikredi-logo.jpg" alt="Yapı Kredi" class="logo left-logo">
        <div class="dekont-title">
            <div class="title-main">e-Dekont</div>
            <div class="title-sub">TİCARİ İŞLEM DEKONTU</div>
        </div>
        <img src="img/tc-maliye-bakanligi.png" alt="TC Maliye Bakanlığı" class="logo right-logo">
    </header>

    <section class="top-cards">
        <div class="card info-card">
            <div class="row"><div class="label">MÜŞTERİ NO</div><div class="value">65157476</div></div>
            <div class="row"><div class="label">İŞLEM REF</div><div class="value">603099620453</div></div>
            <div class="row"><div class="label">VKN/TCKN/YKN</div><div class="value">***********</div></div>
            <div class="row"><div class="label">SIRA NO/ID</div><div class="value">- / 2396401434017</div></div>
            <div class="row"><div class="label">IBAN NO</div><div class="value mono"><?php echo $iban_display_safe; ?></div></div>
            <div class="row"><div class="label">İŞLEM TARİHİ</div><div class="value"><?php echo $islem_tarihi; ?></div></div>
        </div>

        <div class="card qr-card">
            <img src="<?php echo $qr_img_src; ?>" alt="QR" class="qr-img">
        </div>
    </section>

    <section class="middle-cards">
        <div class="card wide">
            <div class="row"><div class="label">HESAP NUMARASI</div><div class="value">52787867 / IBAN: <?php echo $iban_display_safe; ?></div></div>
            <div class="row"><div class="label">MÜŞTERİ VERGİ DAİRESİ</div><div class="value"><?php echo $vdairesi_display; ?></div></div>
            <div class="row"><div class="label">DÖVİZ CİNSİ</div><div class="value"><?php echo $doviz_display; ?></div></div>
            <div class="row"><div class="label">İŞLEM TUTARI</div><div class="value amount"><?php echo $tutar_display; ?></div></div>
        </div>
    </section>

    <section class="bottom-cards">
        <div class="card large">
            <!-- Alıcı ve Açıklama soldan başlasın -->
            <div class="label">Alıcı / Ünvan</div>
            <div class="value left"><?php echo $adsoyad_display; ?></div>

            <div class="label" style="margin-top:12px;">Açıklama</div>
            <div class="value left"><?php echo $aciklama_display; ?></div>
        </div>

        <div class="card info-right">
            <div class="small">Ticari Unvan</div><div class="small-value">Yapı ve Kredi Bankası A.Ş.</div>
            <div class="small">Büyük Mükellefler VD</div><div class="small-value">9370020892</div>
            <div class="small">Web</div><div class="small-value">www.yapikredi.com.tr</div>
            <div class="small">Ticaret Sicil No</div><div class="small-value">32736</div>
            <div class="small">Adres</div><div class="small-value">Yapı Kredi Plaza D Blok 34330 Levent - İstanbul</div>
            <div class="small">Mersis No</div><div class="small-value">0937002089200741</div>
        </div>
    </section>

    <!-- Uyarı: kartın dışında, ortalanmış ve daha küçük -->
    <div class="alert-red centered">
        Para transferiniz alıcının SPK/MASAK özel belge durumu ve vergi beyannamesinin mevcut olmadığından dolayı engellenmiştir.
    </div>

    <footer class="dekont-footer">e-Dekont izni kapsamında elektronik ortamda üretilmiştir.</footer>
</div>

<!-- --- STYLES --- -->
<style>
:root{
    --bg:#f3f6f9;
    --card:#ffffff;
    --muted:#6b7280;
    --accent:#0b5ed7;
    --border:#e6e9ee;
    --shadow: 0 8px 22px rgba(16,24,40,0.06);
}
*{box-sizing:border-box}
body{font-family:'Quicksand',sans-serif;background:var(--bg);margin:18px;padding:0;color:#0f172a;-webkit-font-smoothing:antialiased;}
.form-wrap{max-width:980px;margin:0 auto 18px;padding:12px;}
.form-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;align-items:end;background:transparent;padding:8px;}
.form-grid .full{grid-column:1 / -1;}
.form-row label{display:block;font-size:13px;color:var(--muted);margin-bottom:6px;}
.form-row input[type="text"], .form-row input[type="email"]{width:100%;padding:10px;border:1px solid var(--border);border-radius:8px;background:#fff;font-size:14px;}
.form-row.actions{display:flex;gap:8px;align-items:center;}
.btn{background:var(--accent);color:#fff;border:none;padding:10px 14px;border-radius:8px;cursor:pointer;font-weight:600;}
.btn.outline{background:transparent;border:1px solid var(--accent);color:var(--accent);}

/* alert */
.alert{max-width:980px;margin:12px auto;padding:12px;border-radius:8px;background:#fff6f6;border:1px solid #f5c6cb;color:#721c24;}

/* dekont */
.dekont{max-width:900px;margin:18px auto;background:var(--card);border-radius:12px;padding:20px;box-shadow:var(--shadow);border:1px solid var(--border);overflow:hidden}
.dekont-header{position:relative;height:90px;display:block;margin-bottom:12px}
.logo{height:80px;object-fit:contain} /* logolar biraz daha büyük */
.left-logo{position:absolute;left:18px;top:50%;transform:translateY(-50%)}
.right-logo{position:absolute;right:18px;top:50%;transform:translateY(-50%)}
.dekont-title{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);text-align:center;}
.title-main{font-size:20px;font-weight:700;letter-spacing:0.2px}
.title-sub{font-size:12px;color:var(--muted);margin-top:4px;}

/* cards layout */
.top-cards{display:grid;grid-template-columns:1fr 140px;gap:12px;margin-bottom:12px}
.middle-cards{display:block;margin-bottom:12px}
.bottom-cards{display:grid;grid-template-columns:1fr 320px;gap:12px;align-items:start}

/* card styles */
.card{background:linear-gradient(180deg,#fff,#fbfdff);border-radius:10px;padding:14px;border:1px solid var(--border);box-shadow:0 6px 18px rgba(12,20,35,0.04)}
.info-card .row, .wide .row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed #f1f3f6}
.info-card .row:last-child, .wide .row:last-child{border-bottom:none}
.label{font-size:12px;color:var(--muted);min-width:160px}
.value{font-size:14px;font-weight:600;word-break:break-word;text-align:right}
.value.left{ text-align:left; } /* soldan hizalamak için */
.mono{font-family:monospace,monospace;font-weight:600;letter-spacing:1px}
.amount{font-size:18px;color:var(--accent);font-weight:700}

/* QR card */
.qr-card{display:flex;align-items:center;justify-content:center;padding:10px}
.qr-img{max-width:120px;height:auto;display:block;border-radius:6px;border:1px solid var(--border)}

/* bottom */
.large{padding:18px}
.info-right{padding:14px;font-size:13px}
.info-right .small{color:var(--muted);margin-top:8px}
.info-right .small-value{font-weight:600}

/* Uyarı - ortalanmış ve daha küçük */
.alert-red{color:#c53030;font-weight:600;margin-top:18px;font-size:13px}
.alert-red.centered{ text-align:center; }

/* footer */
.dekont-footer{text-align:center;color:var(--muted);font-size:13px;margin-top:14px}

/* responsive */
@media (max-width:900px){
    .form-grid{grid-template-columns:repeat(2,1fr)}
    .top-cards{grid-template-columns:1fr 120px}
    .bottom-cards{grid-template-columns:1fr}
    .info-card .row .label{min-width:120px}
}
@media (max-width:600px){
    .form-grid{grid-template-columns:1fr}
    .left-logo, .right-logo{position:static;transform:none;margin:0 auto;display:block;height:56px}
    .dekont-header{height:auto;padding-top:10px;padding-bottom:10px}
    .dekont-title{position:static;transform:none;margin:8px 0;text-align:center}
    .top-cards{grid-template-columns:1fr;align-items:center}
    .qr-card{order:2;justify-content:center}
}

/* PRINT STYLES: A4 uyumlu */
@page { size: A4; margin: 10mm; }
@media print{
    body{background:#fff}
    html,body{height:auto}
    .form-wrap, .alert{display:none}
    .dekont{box-shadow:none;border:none;border-radius:0;padding:10mm;margin:0 auto;max-width:100%;width:100% !important}
    .left-logo, .right-logo{height:56px}
    .qr-img{max-width:110px}
    body * { visibility: hidden; }
    .dekont, .dekont * { visibility: visible; }
    .dekont { position: absolute; left: 0; top: 0; }
    -webkit-print-color-adjust: exact;
}
</style>

<!-- html2pdf.js (indir butonu için) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<!-- --- Scripting: IBAN format & PDF indirme --- -->
<script>
(function(){
    var ibanInput = document.getElementById('ibanInput');
    if(!ibanInput) return;

    function formatIban(s){
        s = s.toUpperCase().replace(/[^A-Z0-9]/g, '');
        return s.replace(/(.{4})/g, '$1 ').trim();
    }

    ibanInput.addEventListener('input', function(e){
        var pos = this.selectionStart;
        var old = this.value;
        this.value = formatIban(this.value);
        var diff = this.value.length - old.length;
        if (diff > 0) this.selectionStart = this.selectionEnd = pos + diff;
    });

    var dl = document.getElementById('downloadPdf');
    if(dl){
        dl.addEventListener('click', function(){
            var element = document.getElementById('dekont');
            html2pdf().set({
                margin:10,
                filename:'dekont.pdf',
                html2canvas:{scale:2, useCORS:true},
                jsPDF:{unit:'mm', format:'a4', orientation:'portrait'}
            }).from(element).save();
        });
    }
})();
</script>
