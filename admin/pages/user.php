<?php
include '../../includes/koneksi.php';
include 'sidebar.php';
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
      <div class="header-left">
        <h1>Data User</h1>
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
      <button class="btn-tambah" id="openModal"><i class='bx bx-plus'></i>Tambah</button>
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
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>

            <?php
            $query = "
            SELECT u.*, g.nama_grup 
            FROM user u 
            LEFT JOIN user_grup g ON u.id_grup = g.id_grup
            ORDER BY u.id DESC
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
                <td>
                  <a href='edit_user.php?id={$row['id']}'><i class='bx bx-edit' style='color:#007bff; font-size:18px;'></i></a>
                  <a href='hapus_user.php?id={$row['id']}' onclick=\"return confirm('Hapus user ini?')\"><i class='bx bx-trash' style='color:#dc3545; font-size:18px;'></i></a>
                </td>
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