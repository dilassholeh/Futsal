<?php
session_start();
include '../../includes/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$query = "SELECT * FROM pengaturan WHERE id = 1";
$result = $conn->query($query);
$pengaturan = $result->fetch_assoc();

if (!$pengaturan) {
    $insert = "INSERT INTO pengaturan (id, nama_website, tagline, alamat, telepon, email, whatsapp, instagram, facebook, jam_buka, jam_tutup, tentang_kami) 
               VALUES (1, 'Zona Futsal', 'Tempat Bermain Futsal Terbaik di Kota', 'Jl. Contoh No. 123, Kota Anda', '081234567890', 'info@zonafutsal.com', '6281234567890', 'zonafutsal', 'zonafutsal', '08:00:00', '22:00:00', 'Zona Futsal adalah tempat terbaik untuk bermain futsal dengan fasilitas lengkap dan lapangan berkualitas.')";
    $conn->query($insert);
    $result = $conn->query($query);
    $pengaturan = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_website = $conn->real_escape_string($_POST['nama_website'] ?? '');
    $tagline      = $conn->real_escape_string($_POST['tagline'] ?? '');
    $alamat       = $conn->real_escape_string($_POST['alamat'] ?? '');
    $telepon      = $conn->real_escape_string($_POST['telepon'] ?? '');
    $email        = $conn->real_escape_string($_POST['email'] ?? '');
    $whatsapp     = $conn->real_escape_string($_POST['whatsapp'] ?? '');
    $instagram    = $conn->real_escape_string($_POST['instagram'] ?? $pengaturan['instagram'] ?? '');
    $facebook     = $conn->real_escape_string($_POST['facebook'] ?? $pengaturan['facebook'] ?? '');
    $jam_buka     = $conn->real_escape_string($_POST['jam_buka'] ?? $pengaturan['jam_buka'] ?? '');
    $jam_tutup    = $conn->real_escape_string($_POST['jam_tutup'] ?? $pengaturan['jam_tutup'] ?? '');
    $tentang_kami = $conn->real_escape_string($_POST['tentang_kami'] ?? '');


    $logo = $pengaturan['logo'];
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/Futsal/uploads/";

        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $error = "Gagal membuat folder uploads! Periksa permission folder.";
            }
        }

        if (!isset($error)) {
            $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $new_filename = "logo_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;

            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_extension, $allowed_types)) {
                if ($_FILES['logo']['size'] <= 2097152) {
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
                        if (!empty($pengaturan['logo']) && file_exists($target_dir . $pengaturan['logo'])) {
                            unlink($target_dir . $pengaturan['logo']);
                        }
                        $logo = $new_filename;
                    } else {
                        $error = "Gagal upload file! Periksa permission folder uploads.";
                    }
                } else {
                    $error = "Ukuran file terlalu besar! Maksimal 2MB.";
                }
            } else {
                $error = "Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.";
            }
        }
    }

    if (!isset($error)) {
        $update = "UPDATE pengaturan SET 
                    nama_website = '$nama_website',
                    tagline = '$tagline',
                    alamat = '$alamat',
                    telepon = '$telepon',
                    email = '$email',
                    whatsapp = '$whatsapp',
                    instagram = '$instagram',
                    facebook = '$facebook',
                    jam_buka = '$jam_buka',
                    jam_tutup = '$jam_tutup',
                    tentang_kami = '$tentang_kami',
                    logo = '$logo'
                    WHERE id = 1";

        if ($conn->query($update)) {
            $success = "Pengaturan berhasil diperbarui!";
            $result = $conn->query($query);
            $pengaturan = $result->fetch_assoc();
        } else {
            $error = "Gagal memperbarui pengaturan: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Website - ZOFA Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Poppins, sans-serif;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            max-width: 100%;
            overflow-x: hidden;
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .bottom {
            padding: 15px;
        }


        .profile-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            padding: 6px 12px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #156732;
        }

        .profile-name {
            font-weight: 600;
            font-size: 14px;
            color: #111;
        }

        .profile-role {
            font-size: 12px;
            color: #666;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            background: #dc3545;
            color: #fff;
            padding: 5px 10px;
            border-radius: 6px;
            text-decoration: none;
            transition: .3s;
        }

        .btn-logout:hover {
            background: #c82333;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .settings-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .form-section {
            margin-bottom: 40px;
            padding-bottom: 40px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4CAF50;
        }

        .section-title i {
            color: #4CAF50;
            font-size: 26px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 25px;
        }

        .form-grid.single {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #555;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .form-group label span {
            color: #999;
            font-weight: 400;
            font-size: 13px;
        }

        .form-group input,
        .form-group textarea {
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
            background: #fafafa;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
            background: white;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
        }

        .logo-preview {
            margin-top: 20px;
            padding: 30px;
            background: #fafafa;
            border-radius: 10px;
            border: 2px dashed #ddd;
            text-align: center;
        }

        .logo-preview img {
            max-width: 250px;
            max-height: 250px;
            border-radius: 10px;
            object-fit: contain;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logo-preview p {
            margin: 0 0 20px 0;
            color: #666;
            font-size: 15px;
            font-weight: 600;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            background: #4CAF50;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .file-input-label h5,
        i {
            color: #ffff;
        }

        .file-input-label:hover {
            background: #156732;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        .file-name {
            display: inline-block;
            margin-left: 15px;
            color: #666;
            font-size: 14px;
            font-style: italic;
        }

        .btn-submit {
            background: linear-gradient(135deg, #4CAF50 0%, #156732 100%);
            color: white;
            padding: 16px 45px;
            border: none;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            margin-top: 30px;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 1;
            font-size: 18px;
        }

        .input-group input {
            padding-left: 50px;
        }

        .char-count {
            font-size: 13px;
            color: #999;
            margin-top: 8px;
            text-align: right;
        }

        @media (max-width: 1200px) {
            .content {
                padding: 20px;
            }

            .settings-container {
                padding: 25px;
            }
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 15px;
                width: 100%;
            }

            .settings-container {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .section-title {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main">
        <div class="header">
            <h1>Pengaturan</h1>
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

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class='bx bx-check-circle'></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class='bx bx-error-circle'></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="bottom">

            <form method="POST" enctype="multipart/form-data">
                <div class="settings-container">
                    <div class="form-section">
                        <div class="section-title">
                            <i class='bx bx-info-circle'></i>
                            Informasi Website
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nama Website *</label>
                                <input type="text" name="nama_website" value="<?php echo htmlspecialchars($pengaturan['nama_website']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Tagline <span>(Slogan Website)</span></label>
                                <input type="text" name="tagline" value="<?php echo htmlspecialchars($pengaturan['tagline']); ?>" placeholder="Contoh: Tempat Bermain Futsal Terbaik">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Email</label>
                                <div class="input-group">
                                    <i class='bx bx-envelope'></i>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($pengaturan['email']); ?>" placeholder="info@zonafutsal.com">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <div class="input-group">
                                    <i class='bx bx-phone'></i>
                                    <input type="text" name="telepon" value="<?php echo htmlspecialchars($pengaturan['telepon']); ?>" placeholder="081234567890">
                                </div>
                            </div>
                        </div>
                        <div class="form-grid single">
                            <div class="form-group">
                                <label>Alamat Lengkap</label>
                                <textarea name="alamat" rows="3" placeholder="Jl. Contoh No. 123, Kota Anda"><?php echo htmlspecialchars($pengaturan['alamat']); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <i class='bx bx-image'></i>
                            Logo Website
                        </div>
                        <div class="form-group">
                            <label>Upload Logo Baru <span>(Max 2MB - JPG, PNG, GIF)</span></label>
                            <div class="file-input-wrapper">
                                <input type="file" name="logo" id="logo" accept="image/*" onchange="updateFileName(this)">
                                <label for="logo" class="file-input-label">
                                    <i class='bx bx-upload'></i>
                                    <h5>Pilih File</h5>
                                </label>
                                <span class="file-name" id="file-name">Tidak ada file dipilih</span>
                            </div>

                            <?php if (!empty($pengaturan['logo'])): ?>
                                <div class="logo-preview">
                                    <p>Logo Saat Ini:</p>
                                    <img src="/Futsal/uploads/<?php echo htmlspecialchars($pengaturan['logo']); ?>" alt="Logo">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <i class='bx bx-phone'></i>
                            Informasi Kontak
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>WhatsApp <span>(Format: 628xxx)</span></label>
                                <div class="input-group">
                                    <i class='bx bxl-whatsapp'></i>
                                    <input type="text" name="whatsapp" value="<?php echo htmlspecialchars($pengaturan['whatsapp']); ?>" placeholder="6281234567890">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-section">
                        <div class="section-title">
                            <i class='bx bx-file-blank'></i>
                            Tentang Kami
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Tentang Website/Bisnis Anda</label>
                            <textarea name="tentang_kami" rows="6" placeholder="Ceritakan tentang bisnis futsal Anda..." id="tentang_kami" oninput="updateCharCount(this, 1000)"><?php echo htmlspecialchars($pengaturan['tentang_kami']); ?></textarea>
                            <div class="char-count">
                                <span id="char-count">0</span> / 1000 karakter
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class='bx bx-save'></i>
                        Simpan Pengaturan
                    </button>
                </div>
        </div>

        </form>
    </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : 'Tidak ada file dipilih';
            document.getElementById('file-name').textContent = fileName;
        }

        function updateCharCount(textarea, maxLength) {
            const currentLength = textarea.value.length;
            document.getElementById('char-count').textContent = currentLength;

            if (currentLength > maxLength) {
                textarea.value = textarea.value.substring(0, maxLength);
                document.getElementById('char-count').textContent = maxLength;
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('tentang_kami');
            if (textarea) {
                updateCharCount(textarea, 1000);
            }
        });

        window.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>

</html>