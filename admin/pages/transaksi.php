<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

function statusClass($status_db)
{
    $status_db = strtolower(trim($status_db));
    if ($status_db == 'menunggu_konfirmasi') return 'dp';
    return $status_db;
}

function statusLabel($status_db)
{
    $status_db = strtolower(trim($status_db));
    if ($status_db == 'menunggu_konfirmasi') return 'DP';
    return ucfirst($status_db);
}

$limit = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';

$where = [];
if ($search) $where[] = "(t.id LIKE '%$search%' OR u.name LIKE '%$search%')";
if ($status) {
    if ($status == 'dp') $where[] = "t.status_pembayaran='menunggu_konfirmasi'";
    else $where[] = "t.status_pembayaran='$status'";
}
if ($tanggal) $where[] = "DATE(td.tanggal)='$tanggal'";
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$total_transaksi = mysqli_num_rows(mysqli_query($conn, "SELECT t.id FROM transaksi t LEFT JOIN user u ON t.user_id=u.id LEFT JOIN transaksi_detail td ON td.id_transaksi=t.id $where_sql"));
$total_pages = ceil($total_transaksi / $limit);

$query_transaksi = mysqli_query($conn, "
SELECT t.*, u.name AS user_nama, td.tanggal, td.jam_mulai, td.jam_selesai
FROM transaksi t
LEFT JOIN user u ON t.user_id = u.id
LEFT JOIN (
    SELECT id_transaksi, MIN(tanggal) AS tanggal, MIN(jam_mulai) AS jam_mulai, MAX(jam_selesai) AS jam_selesai
    FROM transaksi_detail
    GROUP BY id_transaksi
) td ON td.id_transaksi=t.id
$where_sql
ORDER BY t.created_at DESC
LIMIT $limit OFFSET $offset
");

$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COALESCE(SUM(jumlah_dibayar),0) AS total
FROM transaksi
WHERE status_pembayaran IN ('menunggu_konfirmasi','lunas')
"))['total'];

$transaksi_hari_ini = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM transaksi WHERE DATE(created_at)=CURDATE()"));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Transaksi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/green.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="../assets/css/transaksi.css?v=<?= filemtime('../assets/css/transaksi.css'); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .status.dp {
            background: #f9a825;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .status.lunas {
            background: #4caf50;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .status.dibatalkan {
            background: #e53935;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .btn-icon.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <main class="main">
        <div class="header">
            <h1>Data Transaksi</h1>
            <div class="header-right">
                <div class="profile-card">
                    <img src="../assets/image/<?= htmlspecialchars($_SESSION['admin_foto'] ?? 'profil.png'); ?>" class="profile-img">
                    <div class="profile-info">
                        <span class="profile-name"><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></span>
                    </div>
                    <a href="../logout.php" class="btn-logout"><i class='bx bx-log-out'></i></a>
                </div>
            </div>
        </div>

        <div class="bottom">
            <div class="card-container">
                <div class="card card-pendapatan">
                    <div class="card-text">
                        <h3>Total Pendapatan</h3>
                        <p>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></p>
                    </div>
                </div>
                <div class="card card-transaksi">
                    <div class="card-text">
                        <h3>Total Transaksi</h3>
                        <p><?= $total_transaksi; ?> Transaksi</p>
                    </div>
                </div>
                <div class="card card-hariini">
                    <div class="card-text">
                        <h3>Transaksi Hari Ini</h3>
                        <p><?= $transaksi_hari_ini; ?> Transaksi</p>
                    </div>
                </div>
            </div>

            <div class="filter-search">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Cari ID / User" value="<?= htmlspecialchars($search); ?>">
                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="dp" <?= ($status == 'dp') ? 'selected' : ''; ?>>DP</option>
                        <option value="lunas" <?= ($status == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                        <option value="dibatalkan" <?= ($status == 'dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                    </select>
                    <input type="text" id="tanggalFilter" name="tanggal" placeholder="Pilih tanggal..." value="<?= htmlspecialchars($tanggal); ?>">
                    <button type="submit">Filter</button>
                </form>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Transaksi</th>
                            <th>Nama User</th>
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
                        if (mysqli_num_rows($query_transaksi) > 0):
                            while ($row = mysqli_fetch_assoc($query_transaksi)):
                                $tanggal_booking = !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-';
                                $jam_booking = (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) ? $row['jam_mulai'] . ' - ' . $row['jam_selesai'] : '-';
                        ?>
                                <tr>
                                    <td><?= $no; ?></td>
                                    <td><?= $row['id']; ?></td>
                                    <td><?= $row['user_nama'] ?? '-'; ?></td>
                                    <td>Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                                    <td><?= $tanggal_booking; ?></td>
                                    <td><?= $jam_booking; ?></td>
                                    <td><span class="status <?= statusClass($row['status_pembayaran']); ?>"><?= statusLabel($row['status_pembayaran']); ?></span></td>
                                    <td><?= !empty($row['bukti_pembayaran']) ? "<a href='../../uploads/booking/{$row['bukti_pembayaran']}' target='_blank'>Lihat</a>" : "-"; ?></td>
                                    <td class="aksi">
                                        <a class="btn-icon edit" href="../includes/transaksi/edit.php?id=<?= $row['id']; ?>"><i class="bx bx-edit-alt"></i></a>
                                        <a class="btn-icon lunas" href="../includes/transaksi/transaksi_update.php?action=set_lunas&id=<?= $row['id']; ?>"><i class='bx bx-check-circle'></i></a>
                                        <a class="btn-icon detail" href="../includes/transaksi/transaksi_detail.php?id=<?= $row['id']; ?>"><i class='bx bx-detail'></i></a>
                                    </td>
                                </tr>
                            <?php $no++;
                            endwhile;
                        else: ?>
                            <tr>
                                <td colspan="9" style="text-align:center;">Belum ada data transaksi</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <?php
                $prev = $page - 1;
                $next = $page + 1;
                echo ($page > 1) ? '<a href="?page=' . $prev . '&search=' . urlencode($search) . '&status=' . $status . '&tanggal=' . $tanggal . '">&laquo;</a>' : '<span class="disabled">&laquo;</span>';
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo ($i == $page) ? '<span class="active">' . $i . '</span>' : '<a href="?page=' . $i . '&search=' . urlencode($search) . '&status=' . $status . '&tanggal=' . $tanggal . '">' . $i . '</a>';
                }
                echo ($page < $total_pages) ? '<a href="?page=' . $next . '&search=' . urlencode($search) . '&status=' . $status . '&tanggal=' . $tanggal . '">&raquo;</a>' : '<span class="disabled">&raquo;</span>';
                ?>
            </div>
    </main>

    <script>
        flatpickr("#tanggalFilter", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            theme: "green",
            defaultDate: "<?= htmlspecialchars($tanggal); ?>"
        });
    </script>
</body>

</html>