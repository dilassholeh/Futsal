<?php 
$koneksi_path = file_exists('includes/koneksi.php') 
    ? 'includes/koneksi.php' 
    : (file_exists('../includes/koneksi.php') 
        ? '../includes/koneksi.php' 
        : '../../includes/koneksi.php');

include_once $koneksi_path;

$query = "SELECT * FROM pengaturan WHERE id = 1";
$result = $conn->query($query);
$pengaturan = $result->fetch_assoc();

if (!$pengaturan) {
    $pengaturan = [
        'nama_website' => 'ZonaFutsal',
        'tentang_kami' => 'ZonaFutsal adalah platform modern untuk memesan lapangan dan mengikuti event futsal secara online.',
        'email' => 'info@zonafutsal.id',
        'telepon' => '+62 812 3456 7890',
        'alamat' => 'Jl. Raya Sport Center No. 88, Bandung'
    ];
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        footer {
            color: #e2e8f0;
            padding: 3rem 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2rem;
            background: #f8f9fa;
        }

        .footer-section {
            flex: 1;
            min-width: 200px;
        }

        .footer-section h4 {
            color: #555;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .footer-section p,
        .footer-section a {
            color: #242425;
            font-size: 0.9rem;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.3s;
            line-height: 1.6;
        }

        .footer-section a:hover {
            color: #22c55e;
        }

        .footer-bottom {
            color: #94a3b8;
            text-align: center;
            padding: 1rem;
            font-size: 0.85rem;
            background: #f1f3f5;
            border-top: 1px solid #e5e7eb;
        }

        .garis {
            width: 90%;
            height: 2px;
            background: linear-gradient(90deg, #16a34a, #111111);
            border-radius: 4px;
            margin: 30px auto;
        }

        @media (max-width: 768px) {
            footer {
                flex-direction: column;
                text-align: center;
                padding: 2rem 5%;
            }

            .footer-section {
                min-width: 100%;
            }

            .garis {
                width: 80%;
                margin: 20px auto;
            }
        }

        @media (max-width: 600px) {
            .garis {
                width: 70%;
            }
        }
    </style>
</head>
<body>
    <div class="garis"></div>
    
    <footer>
        <div class="footer-section">
            <h4>Tentang Kami</h4>
            <p><?php echo htmlspecialchars($pengaturan['tentang_kami']); ?></p>
        </div>
        
        <div class="footer-section">
            <h4>Link Cepat</h4>
            <a href="index.php" <?php echo ($current_page == 'index.php') ? 'style="color: #22c55e;"' : ''; ?>>Beranda</a>
            <a href="pages/jadwal.php" <?php echo ($current_page == 'jadwal.php') ? 'style="color: #22c55e;"' : ''; ?>>Jadwal</a>
            <a href="pages/event.php" <?php echo ($current_page == 'event.php') ? 'style="color: #22c55e;"' : ''; ?>>Event</a>
            <a href="pages/kontak.php" <?php echo ($current_page == 'kontak.php') ? 'style="color: #22c55e;"' : ''; ?>>Kontak</a>
        </div>
        
        <div class="footer-section">
            <h4>Hubungi Kami</h4>
            <?php if (!empty($pengaturan['email'])): ?>
                <p>Email: <?php echo htmlspecialchars($pengaturan['email']); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($pengaturan['telepon'])): ?>
                <p>Telp: <?php echo htmlspecialchars($pengaturan['telepon']); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($pengaturan['alamat'])): ?>
                <p>Alamat: <?php echo htmlspecialchars($pengaturan['alamat']); ?></p>
            <?php endif; ?>
        </div>
    </footer>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($pengaturan['nama_website']); ?>. All Rights Reserved.</p>
    </div>
</body>
</html>
