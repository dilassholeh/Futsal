<?php
session_start();
include '../../includes/koneksi.php';


$current_date = date('Y-m-d');
$query = "SELECT e.*, k.nama as kategori_nama 
          FROM event e 
          LEFT JOIN kategori k ON e.kategori_id = k.id 
          WHERE e.tanggal_berakhir >= '$current_date'
          ORDER BY e.tanggal_mulai ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Futsal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/event.css?v=<?php echo filemtime('../assets/css/event.css'); ?>">
    <style>
        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin: 10px 0;
            color: #666;
        }
        .event-meta span {
            display: inline-block;
        }
        .no-events {
            text-align: center;
            padding: 40px 20px;
            background: #f8f8f8;
            border-radius: 8px;
            margin: 20px 0;
        }
        .no-events p {
            color: #666;
            font-size: 1.1em;
        }
        .event-desc {
            margin: 15px 0;
            line-height: 1.6;
        }
        .event-img {
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 8px 8px 0 0;
        }
        .event-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .event-img {
            position: relative;
        }
        .event-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-size: 0.85em;
            font-weight: 500;
        }
        .event-status.upcoming {
            background-color: #2196F3;
        }
        .event-status.ongoing {
            background-color: #4CAF50;
        }
        .no-events {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 30px;
        }
    </style>
</head>

<body>
    <header>
          <nav class="nav">
            <div class="logo-container">
                <a href="company.php" class="logo-text">
                    <img src="../assets/image/logo.png" alt="ZonaFutsal Logo" class="logo-img">
                    ZOFA
                </a>
            </div>
            <div class="sub-container">
                <ul>
                    <li><a href="../index.php">Beranda</a></li>
                    <li><a href="sewa.php">Penyewaan</a></li>
                    <li><a href="event.php">Event</a></li>
                </ul>

                <a href="login.php" class="btn-masuk">Masuk</a>
                <a href="register.php" class="btn-daftar">Daftar</a>
            </div>
        </nav>

    </header>

    <section class="hero">
        <img src="../assets/image/latar.png" alt="ZonaFutsal" class="hero-img">
        <div class="hero-overlay">
            <h1>Event Futsal Terbaru</h1>
        </div>
    </section>

    <div class="event-container">
        <h2 class="title">Daftar Event Futsal</h2>

        <?php 
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tanggal_mulai = date('d M Y', strtotime($row['tanggal_mulai']));
                $tanggal_berakhir = date('d M Y', strtotime($row['tanggal_berakhir']));
                
                $foto_path = !empty($row['foto']) ? "../../uploads/event/{$row['foto']}" : "../assets/image/default-event.jpg";
                
                $status = '';
                $status_class = '';
                if (strtotime($current_date) < strtotime($row['tanggal_mulai'])) {
                    $status = 'Akan Datang';
                    $status_class = 'upcoming';
                } else {
                    $status = 'Sedang Berlangsung';
                    $status_class = 'ongoing';
                }

                echo "<div class='event-card'>
                    <div class='event-img'>
                        <img src='{$foto_path}' alt='{$row['nama_event']}'>
                        <span class='event-status {$status_class}'>{$status}</span>
                    </div>
                    <div class='event-info'>
                        <h3>{$row['nama_event']}</h3>
                        <div class='event-meta'>
                            <span>🏷️ Kategori: {$row['kategori_nama']}</span>
                            <span>📅 {$tanggal_mulai} - {$tanggal_berakhir}</span>
                        </div>
                        <div class='event-desc'>" . nl2br(substr($row['deskripsi'], 0, 150)) . 
                        (strlen($row['deskripsi']) > 150 ? '...' : '') . "</div>
                        <a href='detail_event.php?id={$row['id']}' class='btn'>Lihat Detail</a>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='no-events'>
                    <p>Tidak ada event yang sedang berlangsung saat ini.</p>
                  </div>";
        }
        ?>
    </div>

    <div class="garis"></div>

    <footer>
        <div class="footer-section">
            <h4>Tentang Kami</h4>
            <p>ZonaFutsal adalah platform modern untuk memesan lapangan, melihat jadwal, dan mengikuti event futsal secara online.</p>
        </div>

        <div class="footer-section">
            <h4>Link Cepat</h4>
            <a href="../index.php">Beranda</a>
            <a href="sewa.php">Penyewaan</a>
            <a href="event.php">Event</a>
            <a href="kontak.php">Kontak</a>
        </div>

        <div class="footer-section">
            <h4>Hubungi Kami</h4>
            <p>Email: info@zonafutsal.id</p>
            <p>Telp: +62 812 3456 7890</p>
            <p>Alamat: Jl. Raya Sport Center No. 88, Bandung</p>
        </div>
    </footer>
</body>

</html>