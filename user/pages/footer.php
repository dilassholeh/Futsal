<?php 
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<style>
    footer {
    color: #e2e8f0;
    padding: 3rem 5%;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 2rem;
}

.footer-section {
    flex: 1;
    min-width: 200px;
}

.footer-section h4 {
    color: #555;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.footer-section p,
.footer-section a {
    color: #242425;
    font-size: 0.9rem;
    text-decoration: none;
    display: block;
    margin-bottom: 0.5rem;
    transition: color 0.3s;
}

.footer-section a:hover {
    color: #22c55e;
}

.footer-bottom {
    color: #94a3b8;
    text-align: center;
    padding: 1rem;
    font-size: 0.85rem;
}

@media (max-width: 768px) {
    header {
        padding: 15px 25px;
    }

    nav ul {
        gap: 15px;
    }

    .event-card {
        flex-direction: column;
    }

    .event-img {
        width: 100%;
        height: 200px;
    }

    footer {
        flex-direction: column;
        text-align: center;
    }

    .footer-section {
        min-width: 100%;
    }

}

@media (max-width: 600px) {
    .garis {
        width: 80%;
        margin: 20px auto;
    }
}

.garis {
    width: 90%;
    height: 2px;
    background: linear-gradient(90deg, #16a34a, #111111);
    border-radius: 4px;
    margin: 30px 0;
    margin-left: 65px;
}

</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <footer>
        <div class="footer-section">
            <h4>Tentang Kami</h4>
            <p>ZonaFutsal adalah platform modern untuk memesan lapangan dan mengikuti event futsal secara online.</p>
        </div>
        <div class="footer-section">
            <h4>Link Cepat</h4>
            <a href="../index.php">Beranda</a>
            <a href="jadwal.php">Jadwal</a>
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