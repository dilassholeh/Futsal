<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_data = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi"));
$total_pages = ceil($total_data / $limit);

$query = mysqli_query($conn, "
    SELECT t.*,
        (SELECT td.tanggal FROM transaksi_detail td WHERE td.id_transaksi = t.id LIMIT 1) AS tanggal,
        (SELECT td.jam_mulai FROM transaksi_detail td WHERE td.id_transaksi = t.id LIMIT 1) AS jam_mulai,
        (SELECT td.jam_selesai FROM transaksi_detail td WHERE td.id_transaksi = t.id LIMIT 1) AS jam_selesai
    FROM transaksi t
    ORDER BY t.created_at DESC
    LIMIT $limit OFFSET $offset
");

$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(subtotal) AS total FROM transaksi"))['total'] ?? 0;
$hari_ini = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM transaksi WHERE DATE(created_at) = CURDATE()"));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi | Zona Futsal</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/transaksi.css?v=<?php echo filemtime('../assets/css/transaksi.css'); ?>">
    <style>
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
    </style>
</head>

<body>
    <main class="main">
           <div class="header">
      <div class="header-left">
        <h1>Dashboard</h1>
      </div>

      <div class="header-right">
        <div class="notif">
          <i class='bx bxs-bell'></i>
        </div>

        <div class="profile-card">
          <div class="profile-info">
            <img
              src="../assets/image/<?= $_SESSION['admin_foto'] ?? 'profil.png'; ?>"
              alt="Profile"
              class="profile-img">
            <div class="profile-text">
              <span class="profile-name"><?= $_SESSION['admin_nama'] ?? 'Admin'; ?></span>
              <small class="profile-role">Administrator</small>
            </div>
          </div>
          <div class="profile-actions">
            <a href="../logout.php" class="btn-logout">
              <i class='bx bx-log-out'></i> Keluar
            </a>
          </div>
        </div>
      </div>
    </div>

        <div class="boot">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Cari transaksi...">
                <i class='bx bx-search'></i>
            </div>
            <div class="stats">
                <div class="card">
                    <i class='bx bx-receipt' style="font-size:40px; color:#007bff;"></i>
                    <div>
                        <h3>Total Transaksi</h3>
                        <p><?= $total_data; ?></p>
                    </div>
                </div>

                <div class="card">
                    <i class='bx bx-money' style="font-size:40px; color:#28a745;"></i>
                    <div>
                        <h3>Pendapatan</h3>
                        <p>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></p>
                    </div>
                </div>

                <div class="card">
                    <i class='bx bx-calendar-check' style="font-size:40px; color:#ffc107;"></i>
                    <div>
                        <h3>Transaksi Hari Ini</h3>
                        <p><?= $hari_ini; ?></p>
                    </div>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Transaksi</th>
                            <th>ID User</th>
                            <th>Total (Rp)</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = ($page - 1) * $limit + 1;
                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                $status = strtolower($row['status_pembayaran'] ?? 'pending');

                                $tanggal_booking = !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-';
                                $jam_booking = (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) ? $row['jam_mulai'] . ' - ' . $row['jam_selesai'] : '-';
                        ?>
                                <tr>
                                    <td><?= $no; ?></td>
                                    <td><?= $row['id']; ?></td>
                                    <td><?= $row['user_id']; ?></td>
                                    <td>Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                                    <td><?= $tanggal_booking; ?></td>
                                    <td><?= $jam_booking; ?></td>
                                    <td><span class='status <?= $status; ?>'><?= ucfirst($status); ?></span></td>
                                    <td>
                                        <?php if (!empty($row['bukti_pembayaran'])): ?>
                                            <a href='../../uploads/booking/<?= $row['bukti_pembayaran']; ?>' target='_blank'>Lihat</a>
                                        <?php else: echo '-';
                                        endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href='../includes/transaksi/transaksi_detail.php?id=<?= $row['id']; ?>' class='btn-detail'>Detail</a>

                                            <?php if ($row['status_pembayaran'] === 'dp'): ?>
                                                <a href='../includes/transaksi/set_lunas.php?id=<?= $row['id']; ?>'
                                                    class='btn-success'
                                                    onclick='return confirm("Set transaksi ini menjadi lunas?")'>Set Lunas</a>
                                            <?php endif; ?>

                                            <a href='../includes/transaksi/batalkan_booking.php?id=<?= $row['id']; ?>'
                                                class='btn-warning'
                                                onclick='return confirm("Batalkan transaksi ini dan kosongkan jam yang dibooking?")'>Batalkan</a>

                                            <!-- <a href='transaksi_hapus.php?id=<?= $row['id']; ?>'
                                                class='btn-delete'
                                                onclick='return confirm("Yakin ingin menghapus transaksi ini?")'>Hapus</a> -->
                                        </div>

                                    </td>
                                </tr>
                        <?php
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='9' style='text-align:center;'>Belum ada data transaksi</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <a href="?page=<?= max($page - 1, 1) ?>" class="prev" <?= $page <= 1 ? 'style="pointer-events:none; opacity:0.5;"' : '' ?>>&#10094;</a>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <a href="?page=<?= min($page + 1, $total_pages) ?>" class="next" <?= $page >= $total_pages ? 'style="pointer-events:none; opacity:0.5;"' : '' ?>>&#10095;</a>
            </div>
        </div>
    </main>

    <script>
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', function() {
            const keyword = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    </script>
</body>

</html>