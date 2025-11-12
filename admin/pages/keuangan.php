<?php
<<<<<<< HEAD
// keuangan.php
include '../../includes/koneksi.php'; // opsional kalau kamu sudah punya koneksi database
=======
include '../../includes/koneksi.php'; 
>>>>>>> eb5d623141e5a5ebeed802122f20c580a2280be0
$current_page = basename($_SERVER['PHP_SELF']);
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
            /* height: 100vh; */
            box-sizing: border-box;
            overflow: hidden;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            background: #eee;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 500;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .summary {
            margin-top: 20px;
            background: #e9f5ff;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
        }

        canvas {
            max-width: 100%;
            height: 100px;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <main>
        <h1><i class='bx bx-wallet'></i> Laporan Keuangan</h1>

        <div class="tabs">
            <button class="tab-btn active" data-tab="harian">Transaksi Harian</button>
            <button class="tab-btn" data-tab="bulanan">Laporan Bulanan</button>
            <button class="tab-btn" data-tab="grafik">Grafik Keuangan</button>
        </div>

       
        <div class="tab-content active" id="harian">
            <h2>📅 Transaksi Harian</h2>
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
                    $data = [
                        ["2025-11-08", "Sewa Lapangan A", 500000, 0],
                        ["2025-11-09", "Pembelian Bola", 0, 150000],
                        ["2025-11-10", "Sewa Lapangan B", 400000, 0],
                        ["2025-11-10", "Perawatan Rumput", 0, 200000],
                        ["2025-11-11", "Sewa Lapangan C", 600000, 0]
                    ];
                    $saldo = 0;
                    $no = 1;
                    foreach ($data as $row) {
                        $saldo += $row[2] - $row[3];
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row[0]}</td>
                            <td>{$row[1]}</td>
                            <td>" . number_format($row[2], 0, ',', '.') . "</td>
                            <td>" . number_format($row[3], 0, ',', '.') . "</td>
                            <td>" . number_format($saldo, 0, ',', '.') . "</td>
                        </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>

            <div class="summary">
                Total Saldo Akhir: <strong>Rp <?= number_format($saldo, 0, ',', '.'); ?></strong>
            </div>
        </div>

<<<<<<< HEAD
        <!-- TAB 2: LAPORAN BULANAN -->
=======
>>>>>>> eb5d623141e5a5ebeed802122f20c580a2280be0
        <div class="tab-content" id="bulanan">
            <h2>📊 Laporan Bulanan</h2>
            <?php
            $bulan = [
                ["Oktober 2025", 950000, 300000],
                ["November 2025", 1500000, 400000],
            ];
            ?>
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
                    <?php foreach ($bulan as $b):
                        $laba = $b[1] - $b[2];
                        $status = $laba >= 0 ? 'Laba' : 'Rugi';
                    ?>
                        <tr>
                            <td><?= $b[0]; ?></td>
                            <td><?= number_format($b[1], 0, ',', '.'); ?></td>
                            <td><?= number_format($b[2], 0, ',', '.'); ?></td>
                            <td><strong><?= $status; ?>: Rp <?= number_format(abs($laba), 0, ',', '.'); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

<<<<<<< HEAD
        <!-- TAB 3: GRAFIK KEUANGAN -->
=======
>>>>>>> eb5d623141e5a5ebeed802122f20c580a2280be0
        <div class="tab-content" id="grafik">
            <h2>📈 Grafik Arus Kas</h2>
            <canvas id="financeChart"></canvas>
        </div>
    </main>

    <script>
<<<<<<< HEAD
        // Tab functionality
=======
>>>>>>> eb5d623141e5a5ebeed802122f20c580a2280be0
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

        // Chart.js
        const ctx = document.getElementById('financeChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Oktober 2025', 'November 2025'],
                datasets: [{
                        label: 'Pemasukan',
                        data: [950000, 1500000],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    },
                    {
                        label: 'Pengeluaran',
                        data: [300000, 400000],
                        backgroundColor: 'rgba(255, 99, 132, 0.6)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Grafik Keuangan Bulanan'
                    }
                }
            }
        });
    </script>
</body>

</html>