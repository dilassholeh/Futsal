<?php
include '../../includes/koneksi.php';
include 'sidebar.php';

// Pagination
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // halaman sekarang
$offset = ($page - 1) * $limit;

// Ambil total data
$total_data = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi"));
$total_pages = ceil($total_data / $limit);

// Query data dengan limit & offset
$query = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
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
                <h1>Data Transaksi</h1>
            </div>
            <div class="header-right">
                <div class="notif"><i class='bx bxs-bell'></i></div>
                <div class="profile">
                    <img src="https://i.pravatar.cc/100" alt="Profile">
                    <span>Admin</span>
                </div>
            </div>
        </div>

        <div class="table-actions">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Cari...">
                <i class='bx bx-search'></i>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Transaksi</th>
                            <th>ID User</th>
                            <th>Total (Rp)</th>
                            <th>Tanggal</th>
                            <th>Bukti Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = ($page - 1) * $limit + 1;
                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['id']}</td>
                                <td>{$row['user_id']}</td>
                                <td>Rp " . number_format($row['subtotal'], 0, ',', '.') . "</td>
                                <td>" . date('d-m-Y H:i', strtotime($row['created_at'])) . "</td>
                                <td>";
                                if (!empty($row['bukti_pembayaran'])) {
                                    echo "<a href='../uploads/{$row['bukti_pembayaran']}' target='_blank'>Lihat</a>";
                                } else {
                                    echo "-";
                                }
                                echo "</td>
                            </tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center;'>Belum ada data transaksi</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pagination">
            <a href="?page=<?= max($page - 1, 1) ?>" class="prev" <?= $page <= 1 ? 'style="pointer-events:none; opacity:0.5;"' : '' ?>>&#10094;</a>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <a href="?page=<?= min($page + 1, $total_pages) ?>" class="next" <?= $page >= $total_pages ? 'style="pointer-events:none; opacity:0.5;"' : '' ?>>&#10095;</a>
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

        const leftBtn = document.querySelector('.scroll-btn.left');
        const rightBtn = document.querySelector('.scroll-btn.right');
        const tableContainer = document.querySelector('.table-container');

        leftBtn.addEventListener('click', () => {
            tableContainer.scrollBy({
                left: -200,
                behavior: 'smooth'
            });
        });

        rightBtn.addEventListener('click', () => {
            tableContainer.scrollBy({
                left: 200,
                behavior: 'smooth'
            });
        });
    </script>

</body>

</html>