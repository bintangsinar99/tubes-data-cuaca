<?php
session_start();
include("config/config.php");

/* =====================
   API KEY
===================== */
$api_key = getenv("API_KEY");
if (empty($api_key)) {
    die("API Key tidak ditemukan");
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

$error = null;
if (!isset($hasil['cod']) || $hasil['cod'] != 200) {
    $error = $hasil['message'] ?? 'Terjadi kesalahan';
}

/* =====================
   BACKGROUND DINAMIS
===================== */
$bgGif = "assets/bg/cloudy.gif";
if (!$error) {
    $weatherMain = strtolower($hasil['weather'][0]['main']);
    if (str_contains($weatherMain, 'rain')) {
        $bgGif = "assets/bg/rain.gif";
    } elseif (str_contains($weatherMain, 'clear')) {
        $bgGif = "assets/bg/clear.gif";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
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
            color: #fff;
        }

        .weather-card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 25px;
            padding: 45px 25px;
            max-width: 520px;
            margin: 0 auto;
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
            border: 1px solid rgba(255,255,255,0.25);
        }

        .weather-temp-main {
            font-size: 96px;
            font-weight: 300;
            line-height: 1;
        }

        .weather-range {
            font-size: 18px;
            opacity: 0.9;
        }

        .weather-desc {
            font-size: 18px;
            text-transform: capitalize;
            margin-bottom: 15px;
        }
    </style>
</head>

<body class="weather-ui">

<div class="container mt-5">

    <h2 class="text-center text-warning mb-4">Data Cuaca</h2>

    <!-- FORM INPUT KOTA -->
    <form method="GET" class="mb-4 text-center">
        <input type="text" name="city"
               value="<?= htmlspecialchars($city); ?>"
               placeholder="Masukkan nama kota"
               class="form-control w-50 mx-auto mb-2"
               required>
        <button type="submit" class="btn btn-warning">
            Cari Cuaca
        </button>
    </form>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php else: ?>
        <div class="weather-card text-center mt-4">

            <h4><?= htmlspecialchars($hasil['name']); ?></h4>

            <img src="https://openweathermap.org/img/wn/<?= $hasil['weather'][0]['icon']; ?>@4x.png"
                 alt="Weather Icon">

            <div class="weather-temp-main">
                <?= round($hasil['main']['temp']); ?>°C
            </div>

            <div class="weather-range">
                <?= round($hasil['main']['temp_max']); ?>° /
                <?= round($hasil['main']['temp_min']); ?>°
            </div>

            <div class="weather-desc">
                <?= htmlspecialchars($hasil['weather'][0]['description']); ?>
            </div>

            <p>Kelembapan: <?= $hasil['main']['humidity']; ?> %</p>

        </div>
    <?php endif; ?>

</div>

</body>
</html>
