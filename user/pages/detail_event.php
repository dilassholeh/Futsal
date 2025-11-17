<?php
session_start();
include '../../includes/koneksi.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    header("Location: event.php");
    exit;
}

$query = "SELECT e.*, k.nama as kategori_nama 
          FROM event e 
          LEFT JOIN kategori k ON e.kategori_id = k.id 
          WHERE e.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header("Location: event.php");
    exit;
}

$tanggal_mulai = date('d M Y', strtotime($event['tanggal_mulai']));
$tanggal_berakhir = date('d M Y', strtotime($event['tanggal_berakhir']));

$current_date = date('Y-m-d');
$status = '';
$status_class = '';
if (strtotime($current_date) < strtotime($event['tanggal_mulai'])) {
    $status = 'Akan Datang';
    $status_class = 'upcoming';
} else if (strtotime($current_date) <= strtotime($event['tanggal_berakhir'])) {
    $status = 'Sedang Berlangsung';
    $status_class = 'ongoing';
} else {
    $status = 'Telah Berakhir';
    $status_class = 'ended';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['nama_event'] ?? '') ?> - Detail Event</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/event.css?v=<?php echo filemtime('../assets/css/event.css'); ?>">
    <style>
        .detail-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            margin-top: 100px;
        }

        .event-detail {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .event-header {
            position: relative;
            height: 400px;
            background: #f5f5f5;
        }

        .event-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .event-status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
        }

        .event-status-badge.upcoming {
            background-color: #2196F3;
        }

        .event-status-badge.ongoing {
            background-color: #4CAF50;
        }

        .event-status-badge.ended {
            background-color: #757575;
        }

        .event-content {
            padding: 30px;
        }

        .event-title {
            font-size: 2em;
            margin: 0 0 20px 0;
            color: #333;
        }

        .event-meta-detail {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .meta-label {
            font-size: 0.9em;
            color: #666;
            font-weight: 500;
        }

        .meta-value {
            font-size: 1.1em;
            color: #333;
        }

        .event-description {
            line-height: 1.8;
            color: #444;
            font-size: 1.1em;
            margin-top: 20px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            margin-bottom: 20px;
            background-color: #f1f1f1;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #e0e0e0;
        }

        @media (max-width: 768px) {
            .event-header {
                height: 300px;
            }

            .event-title {
                font-size: 1.5em;
            }

            .event-meta-detail {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="detail-container">
        <a href="../event.php" class="back-button">← Kembali ke Daftar Event</a>

        <div class="event-detail">
            <div class="event-header">
                <img src="<?= !empty($event['foto']) ? "../../uploads/event/{$event['foto']}" : "../assets/image/default-event.jpg" ?>"
                    alt="<?= htmlspecialchars($event['nama_event'] ?? '') ?>">

                <span class="event-status-badge <?= $status_class ?>"><?= $status ?></span>
            </div>

            <div class="event-content">
                <h1 class="event-title"><?= htmlspecialchars($event['nama_event'] ?? '') ?></h1>

                <div class="event-meta-detail">
                    <div class="meta-item">
                        <span class="meta-label">Kategori</span>
                        <span class="meta-value">🏷️ <?= htmlspecialchars($event['kategori_nama'] ?? '') ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Tanggal Mulai</span>
                        <span class="meta-value">📅 <?= $tanggal_mulai ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Tanggal Berakhir</span>
                        <span class="meta-value">📅 <?= $tanggal_berakhir ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Status</span>
                        <span class="meta-value"><?= $status ?></span>
                    </div>
                </div>
                <div class="event-description">
                    <?= nl2br(htmlspecialchars($event['deskripsi'] ?? 'Tidak ada deskripsi.')) ?>
                </div>

            </div>
        </div>
    </div>


</body>

</html>