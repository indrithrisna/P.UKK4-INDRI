<?php
session_start();
require_once '../config/database.php';

if (!isLoggedIn() || !hasRole('petugas')) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_alat FROM alat WHERE id=$id"));
    mysqli_query($conn, "UPDATE alat SET deleted_at = NOW() WHERE id = $id");
    logActivity($_SESSION['user_id'], 'Hapus Alat', "Menghapus alat: {$alat['nama_alat']}");
    header("Location: alat.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id              = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama_alat       = clean($_POST['nama_alat']);
    $merk            = clean($_POST['merk'] ?? '');
    $kategori_id     = (int)$_POST['kategori_id'];
    $jumlah_total    = (int)$_POST['jumlah_total'];
    $jumlah_tersedia = (int)$_POST['jumlah_tersedia'];
    $kondisi         = clean($_POST['kondisi']);
    $harga_sewa      = (float)$_POST['harga_sewa'];
    $deskripsi       = clean($_POST['deskripsi'] ?? '');

    if ($id > 0) {
        mysqli_query($conn, "UPDATE alat SET nama_alat='$nama_alat', merk='$merk', kategori_id=$kategori_id,
                             jumlah_total=$jumlah_total, jumlah_tersedia=$jumlah_tersedia,
                             kondisi='$kondisi', harga_sewa=$harga_sewa, deskripsi='$deskripsi'
                             WHERE id=$id");
        logActivity($_SESSION['user_id'], 'Update Alat', "Mengubah alat: $nama_alat");
    } else {
        mysqli_query($conn, "INSERT INTO alat (nama_alat, merk, kategori_id, jumlah_total, jumlah_tersedia, kondisi, harga_sewa, deskripsi)
                             VALUES ('$nama_alat','$merk',$kategori_id,$jumlah_total,$jumlah_tersedia,'$kondisi',$harga_sewa,'$deskripsi')");
        logActivity($_SESSION['user_id'], 'Tambah Alat', "Menambah alat: $nama_alat");
    }
    header("Location: alat.php");
    exit();
}

$page_title = "Kelola Alat";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/petugas_sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2>Kelola Alat Event</h2>
            <hr>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAlat" onclick="resetForm()">
                <i class="bi bi-plus"></i> Tambah Alat
            </button>
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Alat</th>
                                <th>Merk</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Tersedia</th>
                                <th>Kondisi</th>
                                <th>Harga Sewa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT a.*, k.nama_kategori FROM alat a LEFT JOIN kategori k ON a.kategori_id = k.id WHERE a.deleted_at IS NULL ORDER BY a.id ASC");
                            while ($row = mysqli_fetch_assoc($result)):
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_alat']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['merk'] ?? '-'); ?></span></td>
                                <td><?php echo htmlspecialchars($row['nama_kategori'] ?? '-'); ?></td>
                                <td><?php echo $row['jumlah_total']; ?></td>
                                <td><?php echo $row['jumlah_tersedia']; ?></td>
                                <td><span class="badge bg-<?php echo $row['kondisi']=='baik'?'success':'warning'; ?>"><?php echo $row['kondisi']; ?></span></td>
                                <td>Rp <?php echo number_format($row['harga_sewa'], 0, ',', '.'); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editAlat(<?php echo $row['id']; ?>,'<?php echo addslashes($row['nama_alat']); ?>','<?php echo addslashes($row['merk']??''); ?>',<?php echo $row['kategori_id']; ?>,<?php echo $row['jumlah_total']; ?>,<?php echo $row['jumlah_tersedia']; ?>,'<?php echo $row['kondisi']; ?>',<?php echo $row['harga_sewa']; ?>,'<?php echo addslashes($row['deskripsi']); ?>')">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="if(confirm('Hapus alat ini?')) location.href='?delete=<?php echo $row['id']; ?>'">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAlat" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="alat.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Alat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <input type="hidden" name="id" id="alatId" value="">
                    <div class="mb-3">
                        <label class="form-label">Nama Alat</label>
                        <input type="text" name="nama_alat" id="nama_alat" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Merk/Brand</label>
                        <input type="text" name="merk" id="merk" class="form-control" placeholder="Contoh: JBL, Yamaha">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" id="kategori_id" class="form-control" required>
                            <?php
                            $kat = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC");
                            while ($k = mysqli_fetch_assoc($kat)):
                            ?>
                            <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama_kategori']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Total</label>
                        <input type="number" name="jumlah_total" id="jumlah_total" class="form-control" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Tersedia</label>
                        <input type="number" name="jumlah_tersedia" id="jumlah_tersedia" class="form-control" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kondisi</label>
                        <select name="kondisi" id="kondisi" class="form-control" required>
                            <option value="baik">Baik</option>
                            <option value="rusak ringan">Rusak Ringan</option>
                            <option value="rusak berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Sewa (per hari)</label>
                        <input type="number" name="harga_sewa" id="harga_sewa" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAlat(id, nama_alat, merk, kategori_id, jumlah_total, jumlah_tersedia, kondisi, harga_sewa, deskripsi) {
    document.getElementById('alatId').value = id;
    document.getElementById('nama_alat').value = nama_alat;
    document.getElementById('merk').value = merk;
    document.getElementById('kategori_id').value = kategori_id;
    document.getElementById('jumlah_total').value = jumlah_total;
    document.getElementById('jumlah_tersedia').value = jumlah_tersedia;
    document.getElementById('kondisi').value = kondisi;
    document.getElementById('harga_sewa').value = harga_sewa;
    document.getElementById('deskripsi').value = deskripsi;
    document.getElementById('modalTitle').textContent = 'Edit Alat';
    new bootstrap.Modal(document.getElementById('modalAlat')).show();
}
function resetForm() {
    document.getElementById('alatId').value = '';
    document.getElementById('nama_alat').value = '';
    document.getElementById('merk').value = '';
    document.getElementById('deskripsi').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Alat';
}
</script>

<?php include '../includes/footer.php'; ?>
