<?php
session_start();
include '../../includes/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../login.php");
  exit;
}



?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data User</title>
  <link rel="stylesheet" href="../assets/css/user.css?v=<?php echo filemtime('../assets/css/user.css'); ?>">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  <main class="main">
    <div class="header">
      <h1>Data User</h1>
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
      <div class="latar">

        <div class="table-actions">
          <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari...">
            <i class='bx bx-search'></i>
          </div>

          
        </div>

        <div class="table-container">
          <div class="table-contaier">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama</th>
                  <th>Username</th>
                  <th>No HP</th>
                  <th>Group</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $query = "
    SELECT u.*, g.nama_grup 
    FROM user u 
    LEFT JOIN user_grup g ON u.id_grup = g.id_grup
    ORDER BY 
        CASE 
            WHEN g.nama_grup = 'Admin' THEN 0 
            ELSE 1 
        END,
        u.id DESC
";

                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                    echo "
              <tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['username']}</td>
                <td>{$row['no_hp']}</td>
                <td>{$row['nama_grup']}</td>
              </tr>";
                  }
                } else {
                  echo "<tr><td colspan='6' style='text-align:center;'>Belum ada user</td></tr>";
                }
                ?>

              </tbody>
            </table>
          </div>
        </div>
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