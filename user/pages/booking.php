<?php
session_start();
include '../../includes/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_GET['id'])) die("ID lapangan tidak ditemukan");

$pendingWarning = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cekPending = $conn->query("
        SELECT id FROM transaksi
        WHERE user_id = '$user_id'
        AND status_pembayaran = 'pending'
        AND expire_at > NOW()
        LIMIT 1
    ");
    if ($cekPending->num_rows > 0) {
        $pendingWarning = "Anda masih memiliki pesanan yang belum dibayar.";
    }
}

$conn->query("
    UPDATE transaksi 
    SET status_pembayaran='expired'
    WHERE status_pembayaran IN ('pending','cart')
    AND expire_at <= NOW()
");

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM lapangan WHERE id='$id'");
if (mysqli_num_rows($query) == 0) die("Lapangan tidak ditemukan");
$lapangan = mysqli_fetch_assoc($query);

$resultJam = mysqli_query($conn, "SELECT jam FROM jam ORDER BY jam ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Booking Lapangan - <?= htmlspecialchars($lapangan['nama_lapangan']); ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/green.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{margin:0;padding:20px;background:linear-gradient(135deg,#e6f9e8,#f9fff9);}
.wrapper{display:flex;gap:20px;flex-wrap:wrap;max-width:1200px;margin:0 auto;}
.left,.right{flex:1 1 45%;}
.left img{width:100%;border-radius:10px;object-fit:cover;}
h2{margin-top:0;color:#2e7d32;font-weight:600;}
.form-group{margin-bottom:15px;}
label{font-weight:500;margin-bottom:5px;display:block;color:#333;}
input,select,textarea{width:100%;padding:10px;border-radius:10px;border:1px solid #ccc;outline:none;}
textarea{resize:vertical;min-height:70px;}
.summary div{display:flex;justify-content:space-between;font-weight:500;color:#2e7d32;margin:5px 0;}
.btn-group{display:flex;gap:10px;margin-top:10px;flex-wrap:wrap;}
.btn{padding:10px 20px;border-radius:25px;border:none;cursor:pointer;font-weight:500;}
.btn-success{background:#43a047;color:#fff;}
.btn-danger{background:#e53935;color:#fff;}
.btn-warning{background:#cddc39;color:#333;}
#popupAlert{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);display:none;justify-content:center;align-items:center;z-index:9999;}
#popupAlert .box{background:#fff;width:90%;max-width:350px;padding:20px;border-radius:12px;text-align:center;animation:fadeIn .2s ease-out;}
@keyframes fadeIn{from{opacity:0;transform:scale(0.9);}to{opacity:1;transform:scale(1);}}
@media(max-width:768px){.wrapper{flex-direction:column;}.left,.right{flex:1 1 100%;}}
</style>
</head>
<body>

<div id="popupAlert">
    <div class="box">
        <p id="popupMessage" style="margin-bottom:20px;font-weight:500;"></p>
        <button onclick="closePopup()" style="padding:8px 20px;border:none;background:#4CAF50;color:#fff;border-radius:8px;cursor:pointer;">OK</button>
    </div>
</div>

<div class="wrapper">
    <div class="left">
        <img src="../../uploads/lapangan/<?= htmlspecialchars($lapangan['foto']); ?>">
    </div>

    <div class="right">
        <h2>Booking Lapangan: <?= htmlspecialchars($lapangan['nama_lapangan']); ?></h2>

        <form action="../includes/booking/invoice_redirect.php" method="POST" id="bookingForm">

            <div class="form-group">
                <label>Tanggal</label>
                <input type="text" id="tanggal" name="tanggal" required>
            </div>

            <div class="form-group">
                <label>Jam Mulai</label>
                <select id="jamMulai" name="jam_mulai" required>
                    <option value="" disabled selected>- Pilih Jam -</option>
                    <?php while ($r = $resultJam->fetch_assoc()): ?>
                        <option value="<?= substr($r['jam'],0,5); ?>"><?= substr($r['jam'],0,5); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Durasi (jam)</label>
                <input type="number" id="durasi" name="durasi" min="1" max="12" required>
            </div>

            <div class="form-group">
                <label>Jam Selesai</label>
                <input type="text" id="jamSelesai" readonly>
            </div>

            <div class="summary">
                <div><span>Harga/Jam</span><span id="harga">Rp 0</span></div>
                <div><span>Total</span><span id="total">Rp 0</span></div>
            </div>

            <div class="form-group">
                <label>Catatan</label>
                <textarea id="catatan" name="catatan"></textarea>
            </div>

            <div class="btn-group">
                <button type="reset" class="btn btn-danger">Batal</button>
                <button type="submit" class="btn btn-success">Checkout</button>
                <button type="button" class="btn btn-warning" onclick="tambahKeKeranjang()">Keranjang</button>
            </div>

            <input type="hidden" id="lapangan_id" name="lapangan_id" value="<?= $lapangan['id']; ?>">
            <input type="hidden" name="nama_lapangan" value="<?= $lapangan['nama_lapangan']; ?>">
            <input type="hidden" id="hargaValue" name="harga">
            <input type="hidden" id="jamMulaiValue" name="jam_mulai">
            <input type="hidden" id="durasiValue" name="durasi">
            <input type="hidden" id="jamSelesaiValue" name="jam_selesai">
            <input type="hidden" id="totalValue" name="total">
        </form>
    </div>
</div>

<script>
function showPopup(msg, redirect=null){
    document.getElementById("popupMessage").innerText=msg;
    const p=document.getElementById("popupAlert");
    p.dataset.redirect=redirect;
    p.style.display="flex";
}
function closePopup(){
    const p=document.getElementById("popupAlert");
    p.style.display="none";
    if(p.dataset.redirect) window.location.href=p.dataset.redirect;
}

document.addEventListener('DOMContentLoaded',()=>{
    const hargaPagi=<?= (int)$lapangan['harga_pagi']; ?>;
    const hargaMalam=<?= (int)$lapangan['harga_malam']; ?>;
    const hargaDisplay=document.getElementById('harga');
    const totalDisplay=document.getElementById('total');
    const hargaValue=document.getElementById('hargaValue');
    const totalValue=document.getElementById('totalValue');
    const jamMulai=document.getElementById('jamMulai');
    const durasi=document.getElementById('durasi');
    const jamSelesai=document.getElementById('jamSelesai');
    const jamMulaiValue=document.getElementById('jamMulaiValue');
    const durasiValue=document.getElementById('durasiValue');
    const jamSelesaiValue=document.getElementById('jamSelesaiValue');

    function hitung(){
        const jm=jamMulai.value;
        const dr=parseInt(durasi.value);
        if(!jm||isNaN(dr))return;
        const h=parseInt(jm.split(':')[0]);
        const end=h+dr;
        const endStr=(end<10?'0':'')+end+':00';
        jamSelesai.value=endStr;
        jamSelesaiValue.value=endStr;
        const hargaPerJam=h<18?hargaPagi:hargaMalam;
        hargaDisplay.textContent='Rp '+hargaPerJam.toLocaleString('id-ID');
        hargaValue.value=hargaPerJam;
        const total=hargaPerJam*dr;
        totalDisplay.textContent='Rp '+total.toLocaleString('id-ID');
        totalValue.value=total;
        jamMulaiValue.value=jm;
        durasiValue.value=dr;
    }

    jamMulai.addEventListener('change',hitung);
    durasi.addEventListener('input',hitung);
});

function loadAvailableHours(){
    const lap=document.getElementById("lapangan_id").value;
    const tgl=document.getElementById("tanggal").value;
    if(!lap||!tgl)return;

    fetch("../includes/booking/get_available_jam.php?lapangan_id="+lap+"&tanggal="+tgl)
    .then(res=>res.json())
    .then(data=>{
        const select=document.getElementById("jamMulai");
        const prev=select.value;
        select.innerHTML="<option value='' disabled selected>- Pilih Jam -</option>";
        if(data.empty){
            select.innerHTML="<option value=''>Tidak ada jam tersedia</option>";
            return;
        }
        data.forEach(j=>{
            const opt=document.createElement("option");
            opt.value=j;
            opt.textContent=j;
            select.appendChild(opt);
        });
        if(Array.from(select.options).map(o=>o.value).includes(prev)){
            select.value=prev;
        }
    });
}

function tambahKeKeranjang(){
    const form=document.getElementById("bookingForm");
    const formData=new FormData(form);
    fetch('../includes/booking/add_to_cart.php',{
        method:'POST',
        body:formData
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.status==='success'){
            showPopup('Berhasil ditambahkan ke keranjang');
            form.reset();
            document.getElementById('harga').textContent='Rp 0';
            document.getElementById('total').textContent='Rp 0';
            document.getElementById('jamSelesai').value='';
        } else {
            showPopup('Gagal menambahkan ke keranjang');
        }
    });
}

flatpickr("#tanggal",{
    dateFormat:"Y-m-d",
    altInput:true,
    altFormat:"d F Y",
    minDate:"today",
    onChange:()=>loadAvailableHours()
});

document.addEventListener("DOMContentLoaded",()=>{
    if(document.getElementById("tanggal").value!=="") loadAvailableHours();
});
</script>

<?php if ($pendingWarning): ?>
<script>
window.onload = function(){
    showPopup("<?= $pendingWarning ?>","../sewa.php");
};
</script>
<?php endif; ?>

</body>
</html>
