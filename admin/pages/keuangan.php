
<?php
include '../../includes/koneksi.php'; 
$current_page = basename($_SERVER['PHP_SELF']);

// Proses simpan data transaksi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_transaksi'])) {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $tipe = $_POST['tipe'];
    $jumlah = $_POST['jumlah'];
    
    $pemasukan = ($tipe == 'pemasukan') ? $jumlah : 0;
    $pengeluaran = ($tipe == 'pengeluaran') ? $jumlah : 0;
    
    $sql = "INSERT INTO transaksi_keuangan (tanggal, keterangan, pemasukan, pengeluaran) 
            VALUES ('$tanggal', '$keterangan', $pemasukan, $pengeluaran)";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil disimpan!');</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "');</script>";
    }
}

// Ambil data transaksi dari database
$query_transaksi = "SELECT * FROM transaksi_keuangan ORDER BY tanggal DESC, id DESC";
$result_transaksi = mysqli_query($conn, $query_transaksi);

// Hitung total pemasukan dan pengeluaran
$query_total = "SELECT 
    SUM(pemasukan) as total_pemasukan, 
    SUM(pengeluaran) as total_pengeluaran 
    FROM transaksi_keuangan";
$result_total = mysqli_query($conn, $query_total);
$total_data = mysqli_fetch_assoc($result_total);
$total_pemasukan = $total_data['total_pemasukan'] ?? 0;
$total_pengeluaran = $total_data['total_pengeluaran'] ?? 0;
$saldo_akhir = $total_pemasukan - $total_pengeluaran;

// Data laporan bulanan
$query_bulanan = "SELECT 
    DATE_FORMAT(tanggal, '%Y-%m') as bulan,
    SUM(pemasukan) as total_pemasukan,
    SUM(pengeluaran) as total_pengeluaran
    FROM transaksi_keuangan
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan DESC";
$result_bulanan = mysqli_query($conn, $query_bulanan);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - ZOFA</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css?v=<?php echo filemtime('../assets/css/sidebar.css'); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        :root {
            --primary: #1a1a1a;
            --background: #ffffff;
            --text: #1a1a1a;
            --white: #ffffff;
            --gray-border: #e0e0e0;
        }

        body {
            background: var(--background);
            display: flex;
            height: 100vh;
            overflow: hidden;
            color: var(--text);
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 15px;
            gap: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .tab-btn {
            background: #eee;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .tab-btn:hover {
            background: #ddd;
        }

        .tab-btn.active {
            background: #007bff;
            color: white;
        }

        .tab-content {
            display: none;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .tab-content.active {
            display: block;
        }

        /* Form Styles */
        .form-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: white;
            font-weight: 600;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .summary {
            margin-top: 20px;
            background: #e9f5ff;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .summary-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .summary-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .summary-card .amount {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .summary-card.income .amount {
            color: #28a745;
        }

        .summary-card.expense .amount {
            color: #dc3545;
        }

        .summary-card.balance .amount {
            color: #007bff;
        }

        canvas {
            max-width: 100%;
            height: 400px !important;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-center {
            text-align: center;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <main>
        <h1><i class='bx bx-wallet'></i> Laporan Keuangan</h1>

        <div class="tabs">
            <button class="tab-btn active" data-tab="input">Input Transaksi</button>
            <button class="tab-btn" data-tab="harian">Transaksi Harian</button>
            <button class="tab-btn" data-tab="bulanan">Laporan Bulanan</button>
            <button class="tab-btn" data-tab="grafik">Grafik Keuangan</button>
        </div>

        <!-- TAB 0: INPUT TRANSAKSI -->
        <div class="tab-content active" id="input">
            <h2>💰 Input Transaksi Keuangan</h2>
            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal">Tanggal <span style="color: red;">*</span></label>
                            <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="tipe">Tipe Transaksi <span style="color: red;">*</span></label>
                            <select id="tipe" name="tipe" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="pemasukan">Pemasukan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan <span style="color: red;">*</span></label>
                        <textarea id="keterangan" name="keterangan" required placeholder="Contoh: Sewa Lapangan A, Pembelian Bola, dll."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah (Rp) <span style="color: red;">*</span></label>
                        <input type="number" id="jumlah" name="jumlah" required placeholder="Contoh: 500000" min="0">
                    </div>

                    <button type="submit" name="simpan_transaksi" class="btn btn-primary">
                        <i class='bx bx-save'></i> Simpan Transaksi
                    </button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="summary-grid">
                <div class="summary-card income">
                    <h3>Total Pemasukan</h3>
                    <div class="amount">Rp <?= number_format($total_pemasukan, 0, ',', '.'); ?></div>
                </div>
                <div class="summary-card expense">
                    <h3>Total Pengeluaran</h3>
                    <div class="amount">Rp <?= number_format($total_pengeluaran, 0, ',', '.'); ?></div>
                </div>
                <div class="summary-card balance">
                    <h3>Saldo Akhir</h3>
                    <div class="amount">Rp <?= number_format($saldo_akhir, 0, ',', '.'); ?></div>
                </div>
            </div>
        </div>

        <!-- TAB 1: TRANSAKSI HARIAN -->
        <div class="tab-content" id="harian">
            <h2>📅 Transaksi Harian</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Pemasukan (Rp)</th>
                            <th>Pengeluaran (Rp)</th>
                            <th>Saldo Akhir (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result_transaksi) > 0) {
                            $no = 1;
                            $saldo = 0;
                            // Reset pointer
                            mysqli_data_seek($result_transaksi, 0);
                            while ($row = mysqli_fetch_assoc($result_transaksi)) {
                                $saldo += $row['pemasukan'] - $row['pengeluaran'];
                                echo "<tr>
                                    <td>{$no}</td>
                                    <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>
                                    <td>{$row['keterangan']}</td>
                                    <td class='text-success'>" . number_format($row['pemasukan'], 0, ',', '.') . "</td>
                                    <td class='text-danger'>" . number_format($row['pengeluaran'], 0, ',', '.') . "</td>
                                    <td><strong>" . number_format($saldo, 0, ',', '.') . "</strong></td>
                                </tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Belum ada data transaksi</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="summary">
                Total Saldo Akhir: <strong>Rp <?= number_format($saldo_akhir, 0, ',', '.'); ?></strong>
            </div>
        </div>

        <!-- TAB 2: LAPORAN BULANAN -->
        <div class="tab-content" id="bulanan">
            <h2>📊 Laporan Bulanan</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Total Pemasukan</th>
                            <th>Total Pengeluaran</th>
                            <th>Laba / Rugi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result_bulanan) > 0) {
                            $bulan_indo = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];

                            while ($row = mysqli_fetch_assoc($result_bulanan)) {
                                $laba = $row['total_pemasukan'] - $row['total_pengeluaran'];
                                $status = $laba >= 0 ? 'Laba' : 'Rugi';
                                $status_class = $laba >= 0 ? 'text-success' : 'text-danger';
                                
                                // Format bulan
                                list($tahun, $bulan) = explode('-', $row['bulan']);
                                $nama_bulan = $bulan_indo[$bulan] . ' ' . $tahun;

                                echo "<tr>
                                    <td>{$nama_bulan}</td>
                                    <td class='text-success'>Rp " . number_format($row['total_pemasukan'], 0, ',', '.') . "</td>
                                    <td class='text-danger'>Rp " . number_format($row['total_pengeluaran'], 0, ',', '.') . "</td>
                                    <td class='{$status_class}'><strong>{$status}: Rp " . number_format(abs($laba), 0, ',', '.') . "</strong></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>Belum ada data laporan bulanan</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: GRAFIK KEUANGAN -->
        <div class="tab-content" id="grafik">
            <h2>📈 Grafik Arus Kas</h2>
            <canvas id="financeChart"></canvas>
        </div>
    </main>

    <script>
        // Tab functionality
        const buttons = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(btn.dataset.tab).classList.add('active');
            });
        });

        // Format input rupiah
        const inputJumlah = document.getElementById('jumlah');
        inputJumlah.addEventListener('input', function(e) {
            let value = this.value.replace(/[^0-9]/g, '');
            this.value = value;
        });

        // Chart.js - Data dari database
        <?php
        // Siapkan data untuk chart
        mysqli_data_seek($result_bulanan, 0);
        $labels = [];
        $pemasukan_data = [];
        $pengeluaran_data = [];

        while ($row = mysqli_fetch_assoc($result_bulanan)) {
            list($tahun, $bulan) = explode('-', $row['bulan']);
            $labels[] = $bulan_indo[$bulan] . ' ' . $tahun;
            $pemasukan_data[] = $row['total_pemasukan'];
            $pengeluaran_data[] = $row['total_pengeluaran'];
        }

        // Reverse array agar urutan dari lama ke baru
        $labels = array_reverse($labels);
        $pemasukan_data = array_reverse($pemasukan_data);
        $pengeluaran_data = array_reverse($pengeluaran_data);
        ?>

        const ctx = document.getElementById('financeChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels); ?>,
                datasets: [{
                        label: 'Pemasukan',
                        data: <?= json_encode($pemasukan_data); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pengeluaran',
                        data: <?= json_encode($pengeluaran_data); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
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
                        text: 'Grafik Keuangan Bulanan',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>
