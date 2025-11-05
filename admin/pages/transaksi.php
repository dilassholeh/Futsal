<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$limit = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_data = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi"));
$total_pages = ceil($total_data / $limit);

$query = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

// 📊 Statistik Ringkas
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
</head>

<body>

    <main class="main">
        <div class="header">
            <div class="header-left">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari transaksi...">
                    <i class='bx bx-search'></i>
                </div>
            </div>
            <div class="header-right">
                <div class="notif"><i class='bx bxs-bell'></i></div>

                <div class="profile">
                    <img src="../assets/image/<?= $_SESSION['admin_foto'] ?? 'profil.png'; ?>"
                        alt="Profile"
                        style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                    <span><?= $_SESSION['admin_nama'] ?? 'Admin'; ?></span>
                </div>
            </div>
        </div>

        <div class="boot">
            <h2>Data Transaksi</h2>

            <div class="stats">
                <div class="card">
                    <h3>Total Transaksi</h3>
                    <p><?= $total_data; ?></p>
                </div>
                <div class="card">
                    <h3>Pendapatan</h3>
                    <p>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></p>
                </div>
                <div class="card">
                    <h3>Transaksi Hari Ini</h3>
                    <p><?= $hari_ini; ?></p>
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
                            <th>Status</th>
                            <th>Bukti Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = ($page - 1) * $limit + 1;
                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                $status = strtolower($row['status'] ?? 'pending');
                                echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['id']}</td>
                    <td>{$row['user_id']}</td>
                    <td>Rp " . number_format($row['subtotal'], 0, ',', '.') . "</td>
                    <td>" . date('d-m-Y H:i', strtotime($row['created_at'])) . "</td>
                    <td><span class='status {$status}'>" . ucfirst($status) . "</span></td>
                    <td>";
                                if (!empty($row['bukti_pembayaran'])) {
                                    echo "<a href='../uploads/{$row['bukti_pembayaran']}' target='_blank'>Lihat</a>";
                                } else {
                                    echo "-";
                                }
                                echo "</td>
                    <td>
                      <a href='transaksi_detail.php?id={$row['id']}' class='btn-detail'>Detail</a>
                      <a href='transaksi_hapus.php?id={$row['id']}' class='btn-delete' onclick='return confirm(\"Yakin ingin menghapus transaksi ini?\")'>Hapus</a>
                    </td>
                  </tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center;'>Belum ada data transaksi</td></tr>";
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