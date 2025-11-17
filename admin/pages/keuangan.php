<?php
include '../../includes/koneksi.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_transaksi'])) {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $tipe = $_POST['tipe'];
    $jumlah = $_POST['jumlah'];

    $pemasukan = ($tipe == 'pemasukan') ? $jumlah : 0;
    $pengeluaran = ($tipe == 'pengeluaran') ? $jumlah : 0;

    $sql = "INSERT INTO transaksi_keuangan (tanggal, keterangan, pemasukan, pengeluaran) 
            VALUES ('$tanggal', '$keterangan', $pemasukan, $pengeluaran)";
    mysqli_query($conn, $sql);
}

$query_transaksi = "SELECT * FROM transaksi_keuangan ORDER BY tanggal DESC, id DESC LIMIT $limit OFFSET $offset";
$result_transaksi = mysqli_query($conn, $query_transaksi);

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi_keuangan");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

$query_total = "SELECT SUM(pemasukan) as total_pemasukan, SUM(pengeluaran) as total_pengeluaran FROM transaksi_keuangan";
$result_total = mysqli_query($conn, $query_total);
$total_data = mysqli_fetch_assoc($result_total);
$total_pemasukan = $total_data['total_pemasukan'] ?? 0;
$total_pengeluaran = $total_data['total_pengeluaran'] ?? 0;
$saldo_akhir = $total_pemasukan - $total_pengeluaran;

$query_bulanan = "SELECT DATE_FORMAT(tanggal,'%Y-%m') as bulan,
                        SUM(pemasukan) as total_pemasukan,
                        SUM(pengeluaran) as total_pengeluaran
                  FROM transaksi_keuangan
                  GROUP BY DATE_FORMAT(tanggal,'%Y-%m')
                  ORDER BY bulan ASC";
$result_bulanan = mysqli_query($conn, $query_bulanan);

$bulan_labels = [];
$pemasukan_data = [];
$pengeluaran_data = [];
$saldo_cumulative = [];
$saldo = 0;
$bulan_indo = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];

while ($row = mysqli_fetch_assoc($result_bulanan)) {
    list($tahun, $bulan) = explode('-', $row['bulan']);
    $bulan_labels[] = $bulan_indo[$bulan] . ' ' . $tahun;
    $pemasukan_data[] = $row['total_pemasukan'];
    $pengeluaran_data[] = $row['total_pengeluaran'];
    $saldo += ($row['total_pemasukan'] - $row['total_pengeluaran']);
    $saldo_cumulative[] = $saldo;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Dashboard Modern</title>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../assets/css/keuangan.css?v=<?php echo filemtime('../assets/css/keuangan.css'); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <main>
        <div class="header">
            <h1>Data Keuangan</h1>
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
            <div class="tabs">
                <button class="tab-btn active" data-tab="input">Input Transaksi</button>
                <button class="tab-btn" data-tab="harian">Transaksi Harian</button>
                <button class="tab-btn" data-tab="bulanan">Laporan Bulanan</button>
                <button class="tab-btn" data-tab="grafik">Grafik Keuangan</button>
            </div>

            <div class="tab-content active" id="input">
                <h2><i class='bx bx-wallet'></i> Input Transaksi Keuangan</h2>
                <div class="form-container">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tanggal">Tanggal <span style="color:red">*</span></label>
                                <div class="input-icon">
                                    <input type="text" id="tanggal" name="tanggal" required value="<?= date('Y-m-d'); ?>" placeholder="Pilih tanggal">
                                    <i class='bx bx-calendar'></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Tipe Transaksi <span style="color:red">*</span></label>
                                <select name="tipe" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="pemasukan">Pemasukan</option>
                                    <option value="pengeluaran">Pengeluaran</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan <span style="color:red">*</span></label>
                            <textarea name="keterangan" required placeholder="Contoh: Sewa Lapangan A, Pembelian Bola"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Jumlah (Rp) <span style="color:red">*</span></label>
                            <input type="number" name="jumlah" required min="0" placeholder="500000">
                        </div>
                        <button type="submit" name="simpan_transaksi" class="btn btn-primary"><i class='bx bx-save'></i> Simpan</button>
                    </form>
                </div>
                <div class="summary-grid">
                    <div class="summary-card income">
                        <h3><i class='bx bx-money'></i> Total Pemasukan</h3>
                        <div class="amount">Rp <?= number_format($total_pemasukan, 0, ',', '.'); ?></div>
                    </div>
                    <div class="summary-card expense">
                        <h3><i class='bx bx-credit-card'></i> Total Pengeluaran</h3>
                        <div class="amount">Rp <?= number_format($total_pengeluaran, 0, ',', '.'); ?></div>
                    </div>
                    <div class="summary-card balance">
                        <h3><i class='bx bx-wallet'></i> Saldo Akhir</h3>
                        <div class="amount">Rp <?= number_format($saldo_akhir, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="harian">
                <h2><i class='bx bx-calendar'></i> Transaksi Harian</h2>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Pemasukan</th>
                                <th>Pengeluaran</th>
                                <th>Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = ($page - 1) * $limit + 1;
                            $saldo_temp = 0;
                            while ($row = mysqli_fetch_assoc($result_transaksi)) {
                                $saldo_temp += $row['pemasukan'] - $row['pengeluaran'];
                                echo "<tr>
    <td>{$no}</td>
    <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>
    <td>{$row['keterangan']}</td>
    <td class='text-success'>" . number_format($row['pemasukan'], 0, ',', '.') . "</td>
    <td class='text-danger'>" . number_format($row['pengeluaran'], 0, ',', '.') . "</td>
    <td><strong>" . number_format($saldo_temp, 0, ',', '.') . "</strong></td>
    </tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i; ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i; ?></a>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="tab-content" id="bulanan">
                <h2><i class='bx bx-bar-chart-alt-2'></i> Laporan Bulanan</h2>
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
                            <?php
                            foreach ($bulan_labels as $idx => $bulan) {
                                $laba = $pemasukan_data[$idx] - $pengeluaran_data[$idx];
                                $status_class = ($laba >= 0) ? 'text-success' : 'text-danger';
                                $status_label = ($laba >= 0) ? 'Laba' : 'Rugi';
                                echo "<tr>
    <td>{$bulan}</td>
    <td class='text-success'>Rp " . number_format($pemasukan_data[$idx], 0, ',', '.') . "</td>
    <td class='text-danger'>Rp " . number_format($pengeluaran_data[$idx], 0, ',', '.') . "</td>
    <td class='{$status_class}'><strong>{$status_label}: Rp " . number_format(abs($laba), 0, ',', '.') . "</strong></td>
    </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-content" id="grafik">
                <h2><i class='bx bx-line-chart'></i> Grafik Keuangan</h2>
                <div class="chart-grid">
                    <div class="chart-item">
                        <canvas id="chartIncomeExpense"></canvas>
                    </div>
                    <div class="chart-item">
                        <canvas id="chartSaldo"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        const buttons = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');
        buttons.forEach(btn => btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(btn.dataset.tab).classList.add('active');
        }));

        new Chart(document.getElementById('chartIncomeExpense'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($bulan_labels); ?>,
                datasets: [{
                        label: 'Pemasukan',
                        data: <?= json_encode($pemasukan_data); ?>,
                        backgroundColor: 'rgba(40,167,69,0.6)',
                        borderColor: 'rgba(40,167,69,1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pengeluaran',
                        data: <?= json_encode($pengeluaran_data); ?>,
                        backgroundColor: 'rgba(220,53,69,0.6)',
                        borderColor: 'rgba(220,53,69,1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Pemasukan & Pengeluaran Bulanan',
                        color: '#28a745',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartSaldo'), {
            type: 'line',
            data: {
                labels: <?= json_encode($bulan_labels); ?>,
                datasets: [{
                    label: 'Saldo Kumulatif',
                    data: <?= json_encode($saldo_cumulative); ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40,167,69,0.3)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Saldo Kumulatif Bulanan',
                        color: '#28a745',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });

        flatpickr("#tanggal", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            allowInput: true,
        });
    </script>
</body>

</html>