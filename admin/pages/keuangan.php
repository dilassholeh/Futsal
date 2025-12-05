<?php
include '../../includes/koneksi.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['admin_id'];
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$pesan = '';
$tipe_pesan = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_transaksi'])) {
    $tipe = $_POST['tipe'] ?? '';
    $jumlah = (float)($_POST['jumlah'] ?? 0);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? date('Y-m-d'));
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? '');

    $q1 = mysqli_query($conn, "SELECT SUM(jumlah_dibayar) AS t FROM transaksi WHERE status_pembayaran='lunas'");
    $t1 = (float)(mysqli_fetch_assoc($q1)['t'] ?? 0);
    
    $q2 = mysqli_query($conn, "SELECT SUM(pengeluaran) AS k FROM transaksi_keuangan");
    $r2 = mysqli_fetch_assoc($q2);
    $t2k = (float)($r2['k'] ?? 0);
    
    $saldo_sekarang = $t1 - $t2k;

    if ($tipe === 'pemasukan') {
        $idtrx = 'TRX' . uniqid();
        $created_at = date('Y-m-d H:i:s', strtotime($tanggal));
        $subtotal = $jumlah;
        $jumlah_dibayar = $jumlah;
        $status_pembayaran = 'lunas';
        mysqli_query($conn, "
            INSERT INTO transaksi (id, user_id, subtotal, jumlah_dibayar, status_pembayaran, created_at)
            VALUES ('$idtrx', '$user_id', $subtotal, $jumlah_dibayar, '$status_pembayaran', '$created_at')
        ");
        $pesan = 'Pemasukan berhasil ditambahkan sebesar Rp ' . number_format($jumlah, 0, ',', '.');
        $tipe_pesan = 'sukses';
    } else {
       
        if ($jumlah > $saldo_sekarang) {
            $pesan = 'Saldo tidak mencukupi! Saldo saat ini: Rp ' . number_format($saldo_sekarang, 0, ',', '.') . 
                     ', Pengeluaran yang diminta: Rp ' . number_format($jumlah, 0, ',', '.');
            $tipe_pesan = 'gagal';
        } else {
            $pemasukan = 0;
            $pengeluaran = $jumlah;
            mysqli_query($conn, "
                INSERT INTO transaksi_keuangan (tanggal, pemasukan, pengeluaran, keterangan, created_at)
                VALUES ('$tanggal', $pemasukan, $pengeluaran, '$keterangan', NOW())
            ");
            $pesan = 'Pengeluaran berhasil ditambahkan sebesar Rp ' . number_format($jumlah, 0, ',', '.') . 
                     '. Sisa saldo: Rp ' . number_format($saldo_sekarang - $jumlah, 0, ',', '.');
            $tipe_pesan = 'sukses';
        }
    }
}

$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi_keuangan");
$total_records = intval(mysqli_fetch_assoc($total_result)['total'] ?? 0);
$total_pages = $total_records > 0 ? ceil($total_records / $limit) : 1;

$query_pagination = mysqli_query($conn,
    "SELECT * FROM transaksi_keuangan ORDER BY tanggal DESC, id DESC LIMIT $limit OFFSET $offset"
);

$q1 = mysqli_query($conn, "SELECT SUM(jumlah_dibayar) AS t FROM transaksi WHERE status_pembayaran='lunas'");
$t1 = (float)(mysqli_fetch_assoc($q1)['t'] ?? 0);

$q2 = mysqli_query($conn, "SELECT SUM(pengeluaran) AS k FROM transaksi_keuangan");
$r2 = mysqli_fetch_assoc($q2);
$t2k = (float)($r2['k'] ?? 0);

$total_pemasukan = $t1;
$total_pengeluaran = $t2k;
$saldo_akhir = $total_pemasukan - $total_pengeluaran;

$bulan_indo = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];

$query_bulanan = "
    SELECT bulan,
        SUM(pemasukan) AS total_pemasukan,
        SUM(pengeluaran) AS total_pengeluaran
    FROM (
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS bulan, jumlah_dibayar AS pemasukan, 0 AS pengeluaran
        FROM transaksi
        WHERE status_pembayaran='lunas'
        UNION ALL
        SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, 0 AS pemasukan, pengeluaran
        FROM transaksi_keuangan
    ) AS x
    GROUP BY bulan
    ORDER BY bulan ASC
";
$result_bulanan = mysqli_query($conn, $query_bulanan);

$bulan_labels = [];
$bulan_labels_readable = [];
$pemasukan_data = [];
$pengeluaran_data = [];
$saldo_cumulative = [];
$saldo = 0;
while ($row = mysqli_fetch_assoc($result_bulanan)) {
    $bulan_labels[] = $row['bulan'];
    list($y, $m) = explode('-', $row['bulan']);
    $bulan_labels_readable[] = ($bulan_indo[$m] ?? $m) . ' ' . $y;
    $pemasukan = (float)($row['total_pemasukan'] ?? 0);
    $pengeluaran = (float)($row['total_pengeluaran'] ?? 0);
    $pemasukan_data[] = $pemasukan;
    $pengeluaran_data[] = $pengeluaran;
    $saldo += ($pemasukan - $pengeluaran);
    $saldo_cumulative[] = $saldo;
}

$query_perhari = "
    SELECT tanggal,
           SUM(pemasukan) AS total_pemasukan,
           SUM(pengeluaran) AS total_pengeluaran
    FROM (
        SELECT DATE(created_at) AS tanggal, jumlah_dibayar AS pemasukan, 0 AS pengeluaran
        FROM transaksi
        WHERE status_pembayaran='lunas'
        UNION ALL
        SELECT tanggal, 0 AS pemasukan, pengeluaran
        FROM transaksi_keuangan
    ) AS x
    GROUP BY tanggal
    ORDER BY tanggal ASC
";
$result_perhari = mysqli_query($conn, $query_perhari);

$hari_labels = [];
$hari_pemasukan = [];
$hari_pengeluaran = [];
while ($r = mysqli_fetch_assoc($result_perhari)) {
    $hari_labels[] = date('d-m-Y', strtotime($r['tanggal']));
    $hari_pemasukan[] = (float)($r['total_pemasukan'] ?? 0);
    $hari_pengeluaran[] = (float)($r['total_pengeluaran'] ?? 0);
}

$query_perminggu = "
    SELECT tahun, minggu,
           SUM(pemasukan) AS total_pemasukan,
           SUM(pengeluaran) AS total_pengeluaran
    FROM (
        SELECT YEAR(created_at) AS tahun, WEEK(created_at,1) AS minggu, jumlah_dibayar AS pemasukan, 0 AS pengeluaran
        FROM transaksi
        WHERE status_pembayaran='lunas'
        UNION ALL
        SELECT YEAR(tanggal) AS tahun, WEEK(tanggal,1) AS minggu, 0 AS pemasukan, pengeluaran
        FROM transaksi_keuangan
    ) AS x
    GROUP BY tahun, minggu
    ORDER BY tahun ASC, minggu ASC
";
$result_perminggu = mysqli_query($conn, $query_perminggu);

$minggu_labels = [];
$minggu_pemasukan = [];
$minggu_pengeluaran = [];
while ($r = mysqli_fetch_assoc($result_perminggu)) {
    $label = $r['tahun'] . ' - Minggu ' . sprintf('%02d', $r['minggu']);
    $minggu_labels[] = $label;
    $minggu_pemasukan[] = (float)($r['total_pemasukan'] ?? 0);
    $minggu_pengeluaran[] = (float)($r['total_pengeluaran'] ?? 0);
}

$query_pertahun = "
    SELECT tahun,
           SUM(pemasukan) AS total_pemasukan,
           SUM(pengeluaran) AS total_pengeluaran
    FROM (
        SELECT YEAR(created_at) AS tahun, jumlah_dibayar AS pemasukan, 0 AS pengeluaran
        FROM transaksi
        WHERE status_pembayaran='lunas'
        UNION ALL
        SELECT YEAR(tanggal) AS tahun, 0 AS pemasukan, pengeluaran
        FROM transaksi_keuangan
    ) AS x
    GROUP BY tahun
    ORDER BY tahun ASC
";
$result_pertahun = mysqli_query($conn, $query_pertahun);

$tahun_labels = [];
$tahun_pemasukan = [];
$tahun_pengeluaran = [];
while ($r = mysqli_fetch_assoc($result_pertahun)) {
    $tahun_labels[] = $r['tahun'];
    $tahun_pemasukan[] = (float)($r['total_pemasukan'] ?? 0);
    $tahun_pengeluaran[] = (float)($r['total_pengeluaran'] ?? 0);
}

$result_perhari_for_table = mysqli_query($conn, "
    SELECT tanggal,
           SUM(pemasukan) AS total_pemasukan,
           SUM(pengeluaran) AS total_pengeluaran
    FROM (
        SELECT DATE(created_at) AS tanggal, jumlah_dibayar AS pemasukan, 0 AS pengeluaran
        FROM transaksi
        WHERE status_pembayaran='lunas'
        UNION ALL
        SELECT tanggal, 0 AS pemasukan, pengeluaran
        FROM transaksi_keuangan
    ) AS x
    GROUP BY tanggal
    ORDER BY tanggal DESC
");

$result_perminggu_for_table = mysqli_query($conn, "
    SELECT tahun, minggu,
           SUM(pemasukan) AS total_pemasukan,
           SUM(pengeluaran) AS total_pengeluaran
    FROM (
        SELECT YEAR(created_at) AS tahun, WEEK(created_at,1) AS minggu, jumlah_dibayar AS pemasukan, 0 AS pengeluaran
        FROM transaksi
        WHERE status_pembayaran='lunas'
        UNION ALL
        SELECT YEAR(tanggal) AS tahun, WEEK(tanggal,1) AS minggu, 0 AS pemasukan, pengeluaran
        FROM transaksi_keuangan
    ) AS x
    GROUP BY tahun, minggu
    ORDER BY tahun DESC, minggu DESC
");

$result_perbulan_for_table = mysqli_query($conn, "
    SELECT bulan,
           SUM(pemasukan) AS total_pemasukan,
           SUM(pengeluaran) AS total_pengeluaran
    FROM (
        SELECT DATE_FORMAT(created_at,'%Y-%m') AS bulan, jumlah_dibayar AS pemasukan, 0 AS pengeluaran
        FROM transaksi
        WHERE status_pembayaran='lunas'
        UNION ALL
        SELECT DATE_FORMAT(tanggal,'%Y-%m') AS bulan, 0 AS pemasukan, pengeluaran
        FROM transaksi_keuangan
    ) AS x
    GROUP BY bulan
    ORDER BY bulan DESC
");

$result_pertahun_for_table = mysqli_query($conn, "
    SELECT tahun,
           SUM(pemasukan) AS total_pemasukan,
           SUM(pengeluaran) AS total_pengeluaran
    FROM (
        SELECT YEAR(created_at) AS tahun, jumlah_dibayar AS pemasukan, 0 AS pengeluaran
        FROM transaksi
        WHERE status_pembayaran='lunas'
        UNION ALL
        SELECT YEAR(tanggal) AS tahun, 0 AS pemasukan, pengeluaran
        FROM transaksi_keuangan
    ) AS x
    GROUP BY tahun
    ORDER BY tahun DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keuangan - Laporan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../assets/css/keuangan.css?v=<?php echo filemtime('../assets/css/keuangan.css'); ?>">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabs = document.querySelectorAll(".tab-content");

    tabButtons.forEach(button => {
        button.addEventListener("click", () => {
            const target = button.getAttribute("data-tab");

            tabButtons.forEach(btn => btn.classList.remove("active"));
            tabs.forEach(tab => tab.classList.remove("active"));

            button.classList.add("active");
            document.getElementById(target).classList.add("active");
        });
    });
});
</script>
    <style>
        .alert {
            padding: 16px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .alert-sukses {
            background-color: #d1fae5;
            border-left: 4px solid #10b981;
            color: #065f46;
        }
        
        .alert-gagal {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #7f1d1d;
        }
        
        .alert i {
            font-size: 24px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        .alert-sukses i {
            color: #10b981;
        }
        
        .alert-gagal i {
            color: #ef4444;
        }
        
        .alert-content {
            flex: 1;
        }
        
        .alert-content strong {
            display: block;
            font-weight: 700;
            margin-bottom: 4px;
            font-size: 15px;
        }
        
        .alert-close {
            margin-left: auto;
            cursor: pointer;
            font-size: 20px;
            opacity: 0.6;
            transition: opacity 0.2s;
            flex-shrink: 0;
            line-height: 1;
        }
        
        .alert-close:hover {
            opacity: 1;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .saldo-warning {
            color: #ef4444;
            font-weight: 700;
        }
        
        .saldo-safe {
            color: #16a34a;
            font-weight: 700;
        }

        .tab-content {
       display: none;
   }
   .tab-content.active {
       display: block;
   }
   .tab-btn.active {
       background: #4CAF50;
       color: white;
   }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main class="main-grid">
        <div class="header">
            <h1>Data Keuangan</h1>
            <div style="display:flex;gap:10px;align-items:center">
                <div style="display:flex;flex-direction:column;text-align:right">
                    <span class="<?php echo $saldo_akhir < 0 ? 'saldo-warning' : 'saldo-safe'; ?>">
                        Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
                    </span>
                    <small>Saldo Akhir</small>
                </div>
                <div class="profile-card" style="display:flex;align-items:center;gap:8px">
                    <img src="../assets/image/<?php echo htmlspecialchars($_SESSION['admin_foto'] ?? 'profil.png'); ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
                    <div style="text-align:left">
                        <div style="font-weight:600"><?php echo htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></div>
                    </div>
                    <a href="../logout.php" style="margin-left:8px;color:#ef4444"><i class='bx bx-log-out'></i></a>
                </div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" data-tab="input">Input Transaksi</button>
            <button class="tab-btn" data-tab="harian">Harian</button>
            <button class="tab-btn" data-tab="mingguan">Mingguan</button>
            <button class="tab-btn" data-tab="bulanan">Bulanan</button>
            <button class="tab-btn" data-tab="tahunan">Tahunan</button>
            <button class="tab-btn" data-tab="grafik">Grafik</button>
        </div>

        <div class="tab-content active" id="input">
            <h2><i class="bx bx-wallet"></i> Input Transaksi</h2>
            
            <?php if (!empty($pesan)): ?>
                <div class="alert alert-<?php echo $tipe_pesan; ?>" id="alertBox">
                    <i class='bx <?php echo $tipe_pesan === 'sukses' ? 'bx-check-circle' : 'bx-error-circle'; ?>'></i>
                    <div class="alert-content">
                        <strong><?php echo $tipe_pesan === 'sukses' ? 'Berhasil!' : 'Gagal!'; ?></strong>
                        <div><?php echo $pesan; ?></div>
                    </div>
                    <span class="alert-close" onclick="this.parentElement.style.display='none'">×</span>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" id="formTransaksi">
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <div style="flex:1;min-width:180px">
                            <label>Tanggal</label>
                            <input type="text" id="tanggal" name="tanggal" required value="<?php echo date('Y-m-d'); ?>" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
                        </div>
                        <div style="flex:1;min-width:160px">
                            <label>Tipe</label>
                            <select name="tipe" id="tipe" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
                                <option value="">-- Pilih --</option>
                                <option value="pemasukan">Pemasukan (masuk ke transaksi)</option>
                                <option value="pengeluaran">Pengeluaran (masuk ke transaksi_keuangan)</option>
                            </select>
                        </div>
                        <div style="flex:1;min-width:160px">
                            <label>Jumlah (Rp)</label>
                            <input type="number" name="jumlah" id="jumlah" required min="0" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
                        </div>
                    </div>
                    <div style="margin-top:8px">
                        <label>Keterangan</label>
                        <textarea name="keterangan" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></textarea>
                    </div>
                    <div id="warningBox" style="display:none;margin-top:10px;padding:12px;background:#fef3c7;border-left:4px solid #f59e0b;border-radius:6px;color:#92400e;font-size:14px">
                        <i class='bx bx-error' style="font-size:20px;vertical-align:middle;margin-right:8px"></i>
                        <strong>Peringatan:</strong> <span id="warningText"></span>
                    </div>
                    <div style="margin-top:10px">
                        <button type="submit" name="simpan_transaksi" id="btnSimpan" class="tab-btn" style="background:#4CAF50;color:#fff;border:none">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <div style="font-size:0.9rem;color:#6b7280">Total Pemasukan</div>
                    <div class="amount">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></div>
                </div>
                <div class="summary-card">
                    <div style="font-size:0.9rem;color:#6b7280">Total Pengeluaran</div>
                    <div class="amount">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></div>
                </div>
                <div class="summary-card">
                    <div style="font-size:0.9rem;color:#6b7280">Saldo Akhir</div>
                    <div class="amount <?php echo $saldo_akhir < 0 ? 'saldo-warning' : ''; ?>">
                        Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="harian">
            <h2><i class="bx bx-calendar"></i> Laporan Harian</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th>Laba/Rugi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mysqli_data_seek($result_perhari_for_table, 0);
                        while ($r = mysqli_fetch_assoc($result_perhari_for_table)):
                            $laba = (float)($r['total_pemasukan'] ?? 0) - (float)($r['total_pengeluaran'] ?? 0);
                        ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($r['tanggal'])); ?></td>
                                <td>Rp <?php echo number_format($r['total_pemasukan'] ?? 0, 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($r['total_pengeluaran'] ?? 0, 0, ',', '.'); ?></td>
                                <td style="color:<?php echo $laba >= 0 ? '#16a34a' : '#ef4444'; ?>">Rp <?php echo number_format($laba, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-content" id="mingguan">
            <h2><i class="bx bx-calendar-week"></i> Laporan Mingguan</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Minggu</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th>Laba/Rugi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mysqli_data_seek($result_perminggu_for_table, 0);
                        while ($r = mysqli_fetch_assoc($result_perminggu_for_table)):
                            $laba = (float)($r['total_pemasukan'] ?? 0) - (float)($r['total_pengeluaran'] ?? 0);
                        ?>
                            <tr>
                                <td><?php echo $r['tahun']; ?></td>
                                <td><?php echo $r['minggu']; ?></td>
                                <td>Rp <?php echo number_format($r['total_pemasukan'] ?? 0, 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($r['total_pengeluaran'] ?? 0, 0, ',', '.'); ?></td>
                                <td style="color:<?php echo $laba >= 0 ? '#16a34a' : '#ef4444'; ?>">Rp <?php echo number_format($laba, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-content" id="bulanan">
            <h2><i class="bx bx-bar-chart-alt-2"></i> Laporan Bulanan</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th>Laba/Rugi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mysqli_data_seek($result_perbulan_for_table, 0);
                        while ($r = mysqli_fetch_assoc($result_perbulan_for_table)):
                            list($y, $m) = explode('-', $r['bulan']);
                            $label = ($bulan_indo[$m] ?? $m) . ' ' . $y;
                            $laba = (float)($r['total_pemasukan'] ?? 0) - (float)($r['total_pengeluaran'] ?? 0);
                        ?>
                            <tr>
                                <td><?php echo $label; ?></td>
                                <td>Rp <?php echo number_format($r['total_pemasukan'] ?? 0, 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($r['total_pengeluaran'] ?? 0, 0, ',', '.'); ?></td>
                                <td style="color:<?php echo $laba >= 0 ? '#16a34a' : '#ef4444'; ?>">Rp <?php echo number_format($laba, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-content" id="tahunan">
            <h2><i class="bx bx-calendar-alt"></i> Laporan Tahunan</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th>Laba/Rugi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mysqli_data_seek($result_pertahun_for_table, 0);
                        while ($r = mysqli_fetch_assoc($result_pertahun_for_table)):
                            $laba = (float)($r['total_pemasukan'] ?? 0) - (float)($r['total_pengeluaran'] ?? 0);
                        ?>
                            <tr>
                                <td><?php echo $r['tahun']; ?></td>
                                <td>Rp <?php echo number_format($r['total_pemasukan'] ?? 0, 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($r['total_pengeluaran'] ?? 0, 0, ',', '.'); ?></td>
                                <td style="color:<?php echo $laba >= 0 ? '#16a34a' : '#ef4444'; ?>">Rp <?php echo number_format($laba, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

            <div class="tab-content" id="grafik">
            <h2><i class="bx bx-line-chart"></i> Grafik Keuangan</h2>
            <div class="chart-grid">
                <div>
                    <h4>Harian (Pemasukan vs Pengeluaran)</h4>
                    <canvas id="chartHarian" style="height:260px"></canvas>
                </div>
                <div>
                    <h4>Mingguan (Pemasukan vs Pengeluaran)</h4>
                    <canvas id="chartMingguan" style="height:260px"></canvas>
                </div>
                <div>
                    <h4>Bulanan (Pemasukan vs Pengeluaran)</h4>
                    <canvas id="chartBulanan" style="height:320px"></canvas>
                </div>
                <div>
                    <h4>Tahunan (Pemasukan vs Pengeluaran)</h4>
                    <canvas id="chartTahunan" style="height:320px"></canvas>
                </div>
            </div>
            <div style="margin-top:16px">
                <h4>Saldo Kumulatif Bulanan</h4>
                <canvas id="chartSaldo" style="height:260px"></canvas>
            </div>
        </div>

        <div style="height:24px"></div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(x => x.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(x => x.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(btn.dataset.tab).classList.add('active');
            });
        });

        flatpickr("#tanggal", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            allowInput: true
        });

        const hariLabels = <?php echo json_encode($hari_labels); ?>;
        const hariPemasukan = <?php echo json_encode($hari_pemasukan); ?>;
        const hariPengeluaran = <?php echo json_encode($hari_pengeluaran); ?>;

        const mingguLabels = <?php echo json_encode($minggu_labels); ?>;
        const mingguPemasukan = <?php echo json_encode($minggu_pemasukan); ?>;
        const mingguPengeluaran = <?php echo json_encode($minggu_pengeluaran); ?>;

        const bulanLabels = <?php echo json_encode($bulan_labels_readable); ?>;
        const bulanPemasukan = <?php echo json_encode($pemasukan_data); ?>;
        const bulanPengeluaran = <?php echo json_encode($pengeluaran_data); ?>;

        const tahunLabels = <?php echo json_encode($tahun_labels); ?>;
        const tahunPemasukan = <?php echo json_encode($tahun_pemasukan); ?>;
        const tahunPengeluaran = <?php echo json_encode($tahun_pengeluaran); ?>;

        const saldoCumulative = <?php echo json_encode($saldo_cumulative); ?>;

        function createLineChart(ctx, labels, datasets, options = {}) {
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: Object.assign({
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 800, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 12, weight: 500 }, color: '#333' } },
                        tooltip: {
                            backgroundColor: '#333',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#555', font: { size: 12 } } },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID'), color: '#555' }
                        }
                    }
                }, options)
            });
        }

        function createBarChart(ctx, labels, datasets, options = {}) {
            return new Chart(ctx, {
                type: 'bar',
                data: { labels: labels, datasets: datasets },
                options: Object.assign({
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 800, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 12, weight: 500 }, color: '#333' } },
                        tooltip: {
                            backgroundColor: '#333',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#555' } },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID'), color: '#555' }
                        }
                    }
                }, options)
            });
        }

        const ctxHarian = document.getElementById('chartHarian').getContext('2d');
        createLineChart(ctxHarian, hariLabels, [
            { label: 'Pemasukan', data: hariPemasukan, borderColor: '#16a34a', backgroundColor: 'rgba(16,163,74,0.12)', tension: 0.3, fill: true },
            { label: 'Pengeluaran', data: hariPengeluaran, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.12)', tension: 0.3, fill: true }
        ]);

        const ctxMingguan = document.getElementById('chartMingguan').getContext('2d');
        createLineChart(ctxMingguan, mingguLabels, [
            { label: 'Pemasukan', data: mingguPemasukan, borderColor: '#0ea5a4', backgroundColor: 'rgba(14,165,164,0.12)', tension: 0.3, fill: true },
            { label: 'Pengeluaran', data: mingguPengeluaran, borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.12)', tension: 0.3, fill: true }
        ]);

        const ctxBulanan = document.getElementById('chartBulanan').getContext('2d');
        const gradIncome = ctxBulanan.createLinearGradient(0, 0, 0, 400);
        gradIncome.addColorStop(0, 'rgba(22,163,74,0.7)');
        gradIncome.addColorStop(1, 'rgba(22,163,74,0.2)');
        const gradExpense = ctxBulanan.createLinearGradient(0, 0, 0, 400);
        gradExpense.addColorStop(0, 'rgba(239,68,68,0.7)');
        gradExpense.addColorStop(1, 'rgba(239,68,68,0.2)');
        createBarChart(ctxBulanan, bulanLabels, [
            { label: 'Pemasukan', data: bulanPemasukan, backgroundColor: gradIncome, borderColor: '#16a34a', borderWidth: 1 },
            { label: 'Pengeluaran', data: bulanPengeluaran, backgroundColor: gradExpense, borderColor: '#ef4444', borderWidth: 1 }
        ]);

        const ctxTahunan = document.getElementById('chartTahunan').getContext('2d');
        createBarChart(ctxTahunan, tahunLabels, [
            { label: 'Pemasukan', data: tahunPemasukan, backgroundColor: 'rgba(59,130,246,0.7)', borderColor: '#3b82f6', borderWidth: 1 },
            { label: 'Pengeluaran', data: tahunPengeluaran, backgroundColor: 'rgba(234,88,12,0.7)', borderColor: '#ea580c', borderWidth: 1 }
        ]);

        const ctxSaldo = document.getElementById('chartSaldo').getContext('2d');
        createLineChart(ctxSaldo, <?php echo json_encode($bulan_labels_readable); ?>, [
            { label: 'Saldo Kumulatif', data: saldoCumulative, borderColor: '#0f172a', backgroundColor: 'rgba(15,23,42,0.08)', tension: 0.3, fill: true }
        ], {
            scales: {
                y: { beginAtZero: false, ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID') } }
            }
        });
    </script>
</body>
</html>