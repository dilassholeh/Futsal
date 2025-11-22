<?php
include '../../includes/koneksi.php';
session_start();

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_transaksi'])) {
    $tanggal = $_POST['tanggal'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tipe = $_POST['tipe'];
    $jumlah = (float)$_POST['jumlah'];
    $pemasukan = ($tipe == 'pemasukan') ? $jumlah : 0;
    $pengeluaran = ($tipe == 'pengeluaran') ? $jumlah : 0;
    $sql = "INSERT INTO transaksi_keuangan (tanggal, keterangan, pemasukan, pengeluaran) VALUES ('$tanggal', '$keterangan', $pemasukan, $pengeluaran)";
    mysqli_query($conn, $sql);
}

$query_transaksi = "SELECT * FROM transaksi_keuangan ORDER BY tanggal DESC, id DESC LIMIT $limit OFFSET $offset";
$result_transaksi = mysqli_query($conn, $query_transaksi);

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi_keuangan");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ($total_data > 0) ? ceil($total_data / $limit) : 1;

$query_total = "SELECT SUM(pemasukan) as total_pemasukan, SUM(pengeluaran) as total_pengeluaran FROM transaksi_keuangan";
$result_total = mysqli_query($conn, $query_total);
$total_data = mysqli_fetch_assoc($result_total);
$total_pemasukan = (float)($total_data['total_pemasukan'] ?? 0);
$total_pengeluaran = (float)($total_data['total_pengeluaran'] ?? 0);
$saldo_akhir = $total_pemasukan - $total_pengeluaran;

$query_bulanan = "SELECT DATE_FORMAT(tanggal,'%Y-%m') as bulan, SUM(pemasukan) as total_pemasukan, SUM(pengeluaran) as total_pengeluaran FROM transaksi_keuangan GROUP BY DATE_FORMAT(tanggal,'%Y-%m') ORDER BY bulan ASC";
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
    $pemasukan_data[] = (float)$row['total_pemasukan'];
    $pengeluaran_data[] = (float)$row['total_pengeluaran'];
    $saldo += ((float)$row['total_pemasukan'] - (float)$row['total_pengeluaran']);
    $saldo_cumulative[] = $saldo;
}

$query_perhari = "SELECT tanggal, SUM(pemasukan) AS total_pemasukan, SUM(pengeluaran) AS total_pengeluaran FROM transaksi_keuangan GROUP BY tanggal ORDER BY tanggal ASC";
$result_perhari = mysqli_query($conn, $query_perhari);
$hari_labels = [];
$hari_pemasukan = [];
$hari_pengeluaran = [];
while ($r = mysqli_fetch_assoc($result_perhari)) {
    $hari_labels[] = date('d-m-Y', strtotime($r['tanggal']));
    $hari_pemasukan[] = (float)$r['total_pemasukan'];
    $hari_pengeluaran[] = (float)$r['total_pengeluaran'];
}

$query_perminggu = "SELECT YEAR(tanggal) AS tahun, WEEK(tanggal,1) AS minggu, SUM(pemasukan) AS total_pemasukan, SUM(pengeluaran) AS total_pengeluaran FROM transaksi_keuangan GROUP BY YEAR(tanggal), WEEK(tanggal,1) ORDER BY YEAR(tanggal) ASC, WEEK(tanggal,1) ASC";
$result_perminggu = mysqli_query($conn, $query_perminggu);
$minggu_labels = [];
$minggu_pemasukan = [];
$minggu_pengeluaran = [];
while ($r = mysqli_fetch_assoc($result_perminggu)) {
    $label = $r['tahun'] . ' - Minggu ' . sprintf('%02d', $r['minggu']);
    $minggu_labels[] = $label;
    $minggu_pemasukan[] = (float)$r['total_pemasukan'];
    $minggu_pengeluaran[] = (float)$r['total_pengeluaran'];
}

$query_pertahun = "SELECT YEAR(tanggal) AS tahun, SUM(pemasukan) AS total_pemasukan, SUM(pengeluaran) AS total_pengeluaran FROM transaksi_keuangan GROUP BY YEAR(tanggal) ORDER BY tahun ASC";
$result_pertahun = mysqli_query($conn, $query_pertahun);
$tahun_labels = [];
$tahun_pemasukan = [];
$tahun_pengeluaran = [];
while ($r = mysqli_fetch_assoc($result_pertahun)) {
    $tahun_labels[] = $r['tahun'];
    $tahun_pemasukan[] = (float)$r['total_pemasukan'];
    $tahun_pengeluaran[] = (float)$r['total_pengeluaran'];
}

$result_perhari_for_table = mysqli_query($conn, "SELECT tanggal, SUM(pemasukan) AS total_pemasukan, SUM(pengeluaran) AS total_pengeluaran FROM transaksi_keuangan GROUP BY tanggal ORDER BY tanggal DESC");
$result_perminggu_for_table = mysqli_query($conn, "SELECT YEAR(tanggal) AS tahun, WEEK(tanggal,1) AS minggu, SUM(pemasukan) AS total_pemasukan, SUM(pengeluaran) AS total_pengeluaran FROM transaksi_keuangan GROUP BY YEAR(tanggal), WEEK(tanggal,1) ORDER BY tahun DESC, minggu DESC");
$result_perbulan_for_table = mysqli_query($conn, "SELECT DATE_FORMAT(tanggal,'%Y-%m') AS bulan, SUM(pemasukan) AS total_pemasukan, SUM(pengeluaran) AS total_pengeluaran FROM transaksi_keuangan GROUP BY DATE_FORMAT(tanggal,'%Y-%m') ORDER BY bulan DESC");
$result_pertahun_for_table = mysqli_query($conn, "SELECT YEAR(tanggal) AS tahun, SUM(pemasukan) AS total_pemasukan, SUM(pengeluaran) AS total_pengeluaran FROM transaksi_keuangan GROUP BY YEAR(tanggal) ORDER BY tahun DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan - Laporan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../assets/css/keuangan.css?v=<?php echo filemtime('../assets/css/keuangan.css'); ?>">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <?php include 'sidebar.php'; ?>
    <main class="main-grid">
        <div class="header">
            <h1>Data Keuangan</h1>
            <div style="display:flex;gap:10px;align-items:center">
                <div style="display:flex;flex-direction:column;text-align:right">
                    <span style="font-weight:700">Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?></span>
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
            <div class="form-container">
                <form method="POST">
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <div style="flex:1;min-width:180px">
                            <label>Tanggal</label>
                            <input type="text" id="tanggal" name="tanggal" required value="<?= date('Y-m-d'); ?>" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
                        </div>
                        <div style="flex:1;min-width:160px">
                            <label>Tipe</label>
                            <select name="tipe" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
                                <option value="">-- Pilih --</option>
                                <option value="pemasukan">Pemasukan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                        <div style="flex:1;min-width:160px">
                            <label>Jumlah (Rp)</label>
                            <input type="number" name="jumlah" required min="0" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd">
                        </div>
                    </div>
                    <div style="margin-top:8px">
                        <label>Keterangan</label>
                        <textarea name="keterangan" required style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd"></textarea>
                    </div>
                    <div style="margin-top:10px">
                        <button type="submit" name="simpan_transaksi" class="tab-btn" style="background:#4CAF50;color:#fff;border:none">Simpan</button>
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
                    <div class="amount">Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?></div>
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
                        while ($r = mysqli_fetch_assoc($result_perhari_for_table)): $laba = (float)$r['total_pemasukan'] - (float)$r['total_pengeluaran']; ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($r['tanggal'])); ?></td>
                                <td>Rp <?php echo number_format($r['total_pemasukan'], 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($r['total_pengeluaran'], 0, ',', '.'); ?></td>
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
                        while ($r = mysqli_fetch_assoc($result_perminggu_for_table)): $laba = (float)$r['total_pemasukan'] - (float)$r['total_pengeluaran']; ?>
                            <tr>
                                <td><?php echo $r['tahun']; ?></td>
                                <td><?php echo $r['minggu']; ?></td>
                                <td>Rp <?php echo number_format($r['total_pemasukan'], 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($r['total_pengeluaran'], 0, ',', '.'); ?></td>
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
                        while ($r = mysqli_fetch_assoc($result_perbulan_for_table)): list($y, $m) = explode('-', $r['bulan']);
                            $label = $bulan_indo[$m] . ' ' . $y;
                            $laba = (float)$r['total_pemasukan'] - (float)$r['total_pengeluaran']; ?>
                            <tr>
                                <td><?php echo $label; ?></td>
                                <td>Rp <?php echo number_format($r['total_pemasukan'], 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($r['total_pengeluaran'], 0, ',', '.'); ?></td>
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
                        while ($r = mysqli_fetch_assoc($result_pertahun_for_table)): $laba = (float)$r['total_pemasukan'] - (float)$r['total_pengeluaran']; ?>
                            <tr>
                                <td><?php echo $r['tahun']; ?></td>
                                <td>Rp <?php echo number_format($r['total_pemasukan'], 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($r['total_pengeluaran'], 0, ',', '.'); ?></td>
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

        const hariLabels = <?= json_encode($hari_labels); ?>;
        const hariPemasukan = <?= json_encode($hari_pemasukan); ?>;
        const hariPengeluaran = <?= json_encode($hari_pengeluaran); ?>;

        const mingguLabels = <?= json_encode($minggu_labels); ?>;
        const mingguPemasukan = <?= json_encode($minggu_pemasukan); ?>;
        const mingguPengeluaran = <?= json_encode($minggu_pengeluaran); ?>;

        const bulanLabels = <?= json_encode($bulan_labels); ?>;
        const bulanPemasukan = <?= json_encode($pemasukan_data); ?>;
        const bulanPengeluaran = <?= json_encode($pengeluaran_data); ?>;

        const tahunLabels = <?= json_encode($tahun_labels); ?>;
        const tahunPemasukan = <?= json_encode($tahun_pemasukan); ?>;
        const tahunPengeluaran = <?= json_encode($tahun_pengeluaran); ?>;

        const saldoCumulative = <?= json_encode($saldo_cumulative); ?>;

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
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12,
                                    weight: 500
                                },
                                color: '#333'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#333',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#555',
                                font: {
                                    size: 12,
                                    weight: 500
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: v => 'Rp ' + v.toLocaleString('id-ID'),
                                color: '#555',
                                font: {
                                    size: 12,
                                    weight: 500
                                }
                            }
                        }
                    }
                }, options)
            });
        }

        function createBarChart(ctx, labels, datasets, options = {}) {
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: Object.assign({
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12,
                                    weight: 500
                                },
                                color: '#333'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#333',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#555',
                                font: {
                                    size: 12,
                                    weight: 500
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: v => 'Rp ' + v.toLocaleString('id-ID'),
                                color: '#555',
                                font: {
                                    size: 12,
                                    weight: 500
                                }
                            }
                        }
                    }
                }, options)
            });
        }

        const ctxHarian = document.getElementById('chartHarian').getContext('2d');
        createLineChart(ctxHarian, hariLabels, [{
                label: 'Pemasukan',
                data: hariPemasukan,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(16,163,74,0.15)',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Pengeluaran',
                data: hariPengeluaran,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,0.12)',
                tension: 0.3,
                fill: true
            }
        ]);

        const ctxMingguan = document.getElementById('chartMingguan').getContext('2d');
        createLineChart(ctxMingguan, mingguLabels, [{
                label: 'Pemasukan',
                data: mingguPemasukan,
                borderColor: '#0ea5a4',
                backgroundColor: 'rgba(14,165,164,0.12)',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Pengeluaran',
                data: mingguPengeluaran,
                borderColor: '#f97316',
                backgroundColor: 'rgba(249,115,22,0.12)',
                tension: 0.3,
                fill: true
            }
        ]);

        const ctxBulanan = document.getElementById('chartBulanan').getContext('2d');
        const gradIncome = ctxBulanan.createLinearGradient(0, 0, 0, 400);
        gradIncome.addColorStop(0, 'rgba(22,163,74,0.7)');
        gradIncome.addColorStop(1, 'rgba(22,163,74,0.2)');
        const gradExpense = ctxBulanan.createLinearGradient(0, 0, 0, 400);
        gradExpense.addColorStop(0, 'rgba(239,68,68,0.7)');
        gradExpense.addColorStop(1, 'rgba(239,68,68,0.2)');
        createBarChart(ctxBulanan, bulanLabels, [{
                label: 'Pemasukan',
                data: bulanPemasukan,
                backgroundColor: gradIncome,
                borderColor: '#16a34a',
                borderWidth: 1
            },
            {
                label: 'Pengeluaran',
                data: bulanPengeluaran,
                backgroundColor: gradExpense,
                borderColor: '#ef4444',
                borderWidth: 1
            }
        ]);

        const ctxTahunan = document.getElementById('chartTahunan').getContext('2d');
        createBarChart(ctxTahunan, tahunLabels, [{
                label: 'Pemasukan',
                data: tahunPemasukan,
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderColor: '#3b82f6',
                borderWidth: 1
            },
            {
                label: 'Pengeluaran',
                data: tahunPengeluaran,
                backgroundColor: 'rgba(234,88,12,0.7)',
                borderColor: '#ea580c',
                borderWidth: 1
            }
        ]);

        const ctxSaldo = document.getElementById('chartSaldo').getContext('2d');
        createLineChart(ctxSaldo, bulanLabels, [{
            label: 'Saldo Kumulatif',
            data: saldoCumulative,
            borderColor: '#0f172a',
            backgroundColor: 'rgba(15,23,42,0.08)',
            tension: 0.3,
            fill: true
        }], {
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: v => 'Rp ' + v.toLocaleString('id-ID'),
                        color: '#555',
                        font: {
                            size: 12,
                            weight: 500
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            }
        });
    </script>
</body>

</html>