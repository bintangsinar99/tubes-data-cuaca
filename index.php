<?php
session_start();

/* =====================
   LOAD CONFIG (WAJIB)
===================== */
require_once __DIR__ . "/config/config.php";

/* =====================
   API KEY
===================== */
$api_key = getenv("API_KEY");
if (empty($api_key)) {
    die("API Key tidak ditemukan. Silakan set environment variable API_KEY.");
}

/* =====================
   INPUT KOTA
===================== */
$city = isset($_GET['city']) && !empty($_GET['city'])
    ? $_GET['city']
    : 'Jakarta';

/* =====================
   API REQUEST
===================== */
$url = "https://api.openweathermap.org/data/2.5/weather?q="
     . urlencode($city)
     . "&appid=$api_key&units=metric&lang=id";

$data  = http_request_get($url);
$hasil = json_decode($data, true);

/* =====================
   ERROR HANDLING
===================== */
$error = null;
if (!$hasil || !isset($hasil['cod']) || $hasil['cod'] != 200) {
    $error = $hasil['message'] ?? 'Gagal mengambil data cuaca';
}

/* =====================
   RIWAYAT PENCARIAN
===================== */
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

if (!$error && $city) {
    if (!in_array($city, $_SESSION['history'])) {
        $_SESSION['history'][] = $city;
    }
}

if (isset($_GET['clear']) && $_GET['clear'] == 1) {
    $_SESSION['history'] = [];
    header("Location: index.php");
    exit;
}

/* =====================
   BACKGROUND DINAMIS
===================== */
$bgGif = "assets/bg/cloudy.gif";
if (!$error && isset($hasil['weather'][0]['main'])) {
    $weatherMain = strtolower($hasil['weather'][0]['main']);

    if (strpos($weatherMain, 'rain') !== false) {
        $bgGif = "assets/bg/rain.gif";
    } elseif (strpos($weatherMain, 'clear') !== false) {
        $bgGif = "assets/bg/clear.gif";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>REST Client Data Cuaca</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
        body.weather-ui {
            background: linear-gradient(
                rgba(0,0,0,0.45),
                rgba(0,0,0,0.45)
            ),
            url("<?= $bgGif; ?>") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            color: #ffffff;
        }

        .weather-card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            border-radius: 25px;
            padding: 45px 25px;
            max-width: 520px;
            margin: 0 auto;
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
        }

        .weather-temp-main {
            font-size: 96px;
            font-weight: 300;
        }

        .weather-desc {
            text-transform: capitalize;
        }

        .btn-outline-light:hover {
            background-color: #ffc107;
            color: #000;
        }
    </style>
</head>

<body class="weather-ui">

<div class="container mt-5">
    <h2 class="text-center text-warning mb-4">Data Cuaca</h2>

    <!-- FORM INPUT -->
    <form method="GET" class="text-center mb-3">
        <input type="text" name="city"
               value="<?= htmlspecialchars($city); ?>"
               class="form-control w-50 mx-auto mb-2"
               required>
        <button class="btn btn-warning">Cari Cuaca</button>
    </form>

    <!-- RIWAYAT -->
    <?php if (!empty($_SESSION['history'])): ?>
        <div class="text-center mb-3">
            <h5>Riwayat Pencarian</h5>
            <?php foreach ($_SESSION['history'] as $kota): ?>
                <a href="?city=<?= urlencode($kota); ?>"
                   class="btn btn-outline-light btn-sm m-1">
                    <?= htmlspecialchars($kota); ?>
                </a>
            <?php endforeach; ?>
            <div class="mt-2">
                <a href="?clear=1" class="btn btn-danger btn-sm">
                    Hapus Riwayat
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- HASIL -->
    <?php if ($error): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php else: ?>
        <div class="weather-card text-center">
            <h4><?= htmlspecialchars($hasil['name']); ?></h4>
            <img src="https://openweathermap.org/img/wn/<?= $hasil['weather'][0]['icon']; ?>@4x.png">
            <div class="weather-temp-main">
                <?= round($hasil['main']['temp']); ?>°C
            </div>
            <div class="weather-desc">
                <?= htmlspecialchars($hasil['weather'][0]['description']); ?>
            </div>
            <p>Kelembapan: <?= $hasil['main']['humidity']; ?>%</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
