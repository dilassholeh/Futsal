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

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';

$where = [];
if ($search) $where[] = "(t.id LIKE '%$search%' OR u.name LIKE '%$search%')";
if ($status) $where[] = "LOWER(t.status_pembayaran) = '" . strtolower($status) . "'";
if ($tanggal) $where[] = "DATE((SELECT td.tanggal FROM transaksi_detail td WHERE td.id_transaksi = t.id LIMIT 1)) = '$tanggal'";

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$total_transaksi = mysqli_num_rows(mysqli_query($conn, "
    SELECT t.id FROM transaksi t
    LEFT JOIN user u ON t.user_id = u.id
    $where_sql
"));
$total_pages = ceil($total_transaksi / $limit);

$query_transaksi = mysqli_query($conn, "
    SELECT t.*, u.name AS user_nama,
           (SELECT td.tanggal FROM transaksi_detail td WHERE td.id_transaksi = t.id LIMIT 1) AS tanggal,
           (SELECT td.jam_mulai FROM transaksi_detail td WHERE td.id_transaksi = t.id LIMIT 1) AS jam_mulai,
           (SELECT td.jam_selesai FROM transaksi_detail td WHERE td.id_transaksi = t.id LIMIT 1) AS jam_selesai
    FROM transaksi t
    LEFT JOIN user u ON t.user_id = u.id
    $where_sql
    ORDER BY t.created_at DESC
    LIMIT $limit OFFSET $offset
");

$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(subtotal) AS total FROM transaksi"))['total'] ?? 0;
$transaksi_hari_ini = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM transaksi WHERE DATE(created_at) = CURDATE()"));
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
</head>

<body>
    <main class="main">
        <div class="header">
            <h1>Data Transaksi</h1>
            <div class="header-right">
                <div class="profile-card">
                    <img src="../assets/image/<?php echo htmlspecialchars($_SESSION['admin_foto'] ?? 'profil.png'); ?>" class="profile-img">
                    <div class="profile-info">
                        <span class="profile-name"><?php echo htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></span>
                    </div>
                    <a href="../logout.php" class="btn-logout"><i class='bx bx-log-out'></i></a>
                </div>
            </div>
        </div>

        <div class="bottom">
            <div class="card-container">
                <div class="card card-pendapatan">
                    <div class="card-icon"><i class='bx bx-credit-card'></i></div>
                    <div class="card-text">
                        <h3>Total Pendapatan</h3>
                        <p>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></p>
                    </div>
                </div>

                <div class="card card-transaksi">
                    <div class="card-icon"><i class='bx bx-receipt'></i></div>
                    <div class="card-text">
                        <h3>Total Transaksi</h3>
                        <p><?= $total_transaksi; ?> Transaksi</p>
                    </div>
                </div>

                <div class="card card-hariini">
                    <div class="card-icon"><i class='bx bx-calendar'></i></div>
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
                        <option value="pending" <?= ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="lunas" <?= ($status == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                        <option value="dibatalkan" <?= ($status == 'dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                    </select>

                    <div class="date-input-wrapper">
                        <input type="text" id="tanggalFilter" name="tanggal" placeholder="Pilih tanggal..." value="<?= htmlspecialchars($tanggal); ?>">
                        <i class='bx bx-calendar calendar-icon'></i>
                    </div>

                    <button type="submit">Filter</button>
                </form>

                <div class="export-buttons">
                    <a href="../includes/transaksi/export.php?type=excel&search=<?= urlencode($search); ?>&status=<?= $status; ?>&tanggal=<?= $tanggal; ?>" class="btn_excel">Export Excel</a>
                    <a href="../includes/transaksi/export.php?type=pdf&search=<?= urlencode($search); ?>&status=<?= $status; ?>&tanggal=<?= $tanggal; ?>" class="btn_pdf">Export PDF</a>
                </div>
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
                                $status_bayar = strtolower($row['status_pembayaran'] ?? 'pending');
                                $tanggal_booking = !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-';
                                $jam_booking = (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) ? $row['jam_mulai'] . ' - ' . $row['jam_selesai'] : '-';
                        ?>
                                <tr>
                                    <td><?= $no; ?></td>
                                    <td><?= $row['id']; ?></td>
                                    <td><?= $row['user_nama']; ?></td>
                                    <td>Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                                    <td><?= $tanggal_booking; ?></td>
                                    <td><?= $jam_booking; ?></td>
                                    <td><span class="status <?= $status_bayar; ?>"><?= ucfirst($status_bayar); ?></span></td>
                                    <td>
                                        <?php if (!empty($row['bukti_pembayaran'])): ?>
                                            <a href='../../uploads/booking/<?= $row['bukti_pembayaran']; ?>' target='_blank'>Lihat</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <a href="../includes/transaksi/edit.php?id=<?= $row['id']; ?>">Edit</a> |

                                        <?php if ($status_bayar != 'lunas'): ?>
                                            <a href="../includes/transaksi/transaksi_update.php?action=set_lunas&id=<?= $row['id']; ?>"
                                                onclick="return confirm('Set status transaksi menjadi LUNAS?')">Set Lunas</a> |
                                        <?php endif; ?>

                                        <?php if ($status_bayar != 'dibatalkan'): ?>
                                            <a href="#" data-id="<?= $row['id']; ?>" class="btn-batal">Batalkan</a> |
                                        <?php endif; ?>


                                        <a href="../includes/transaksi/transaksi_detail.php?id=<?= $row['id']; ?>">Detail</a>
                                    </td>
                                </tr>
                            <?php
                                $no++;
                            endwhile;
                        else:
                            ?>
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

                if ($page > 1) {
                    echo '<a href="?page=' . $prev . '&search=' . urlencode($search) . '&status=' . $status . '&tanggal=' . $tanggal . '">&laquo;</a>';
                } else {
                    echo '<span class="disabled">&laquo;</span>';
                }

                if ($total_pages <= 5) {
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $page) echo '<span class="active">' . $i . '</span>';
                        else echo '<a href="?page=' . $i . '&search=' . urlencode($search) . '&status=' . $status . '&tanggal=' . $tanggal . '">' . $i . '</a>';
                    }
                } else {
                    if ($page == 1) echo '<span class="active">1</span>';
                    else echo '<a href="?page=1&search=' . urlencode($search) . '&status=' . $status . '&tanggal=' . $tanggal . '">1</a>';

                    if ($page > 3) echo '<span>...</span>';

                    $start = max(2, $page - 1);
                    $end = min($total_pages - 1, $page + 1);

                    for ($i = $start; $i <= $end; $i++) {
                        if ($i == $page) echo '<span class="active">' . $i . '</span>';
                        else echo '<a href="?page=' . $i . '&search=' . urlencode($search) . '&status=' . $status . '&tanggal=' . $tanggal . '">' . $i . '</a>';
                    }

                    if ($page < $total_pages - 2) echo '<span>...</span>';

                    if ($page == $total_pages) echo '<span class="active">' . $total_pages . '</span>';
                    else echo '<a href="?page=' . $total_pages . '&search=' . urlencode($search) . '&status=' . $status . '&tanggal=' . $tanggal . '">' . $total_pages . '</a>';
                }

                if ($page < $total_pages) {
                    echo '<a href="?page=' . $next . '&search=' . urlencode($search) . '&status=' . $status . '&tanggal=' . $tanggal . '">&raquo;</a>';
                } else {
                    echo '<span class="disabled">&raquo;</span>';
                }
                ?>
            </div>

        </div>
    </main>

    <script>
        flatpickr("#tanggalFilter", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            theme: "green",
            defaultDate: "<?= htmlspecialchars($tanggal); ?>",
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                },
                months: {
                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                },
            },
            onReady: function(selectedDates, dateStr, instance) {
                const clearBtn = document.createElement("button");
                clearBtn.textContent = "Clear";
                clearBtn.type = "button";
                clearBtn.classList.add("flatpickr-clear-btn");
                clearBtn.addEventListener("click", function() {
                    instance.clear();
                });
                instance.calendarContainer.appendChild(clearBtn);
            }
        });
        document.querySelectorAll('.btn-batal').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                let id = this.getAttribute('data-id');
                document.getElementById('batal_id').value = id; 
                document.getElementById('modalBatal').style.display = "flex";
            });
        });

        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('modalBatal').style.display = "none";
        });
    </script>

    <div class="modal" id="modalBatal" style="
display:none; position:fixed; top:0; left:0; width:100%; height:100%;
background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
        <div style="background:#fff; padding:20px; width:400px; border-radius:10px;">
            <h3>Alasan Pembatalan</h3>

            <form id="formBatal" method="POST" action="../includes/transaksi/transaksi_update.php">
                <input type="hidden" name="id" id="batal_id">
                <textarea name="alasan_batal" required style="width:100%; height:100px; margin-top:10px;" placeholder="Masukkan alasan..."></textarea>

                <div style="margin-top:15px; text-align:right;">
                    <button type="button" id="closeModal" style="padding:6px 12px;">Batal</button>
                    <button type="submit" style="padding:6px 12px; background:#e63946; color:#fff; border:none;">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>