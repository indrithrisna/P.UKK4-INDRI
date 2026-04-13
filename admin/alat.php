<?php
session_start();
require_once '../config/database.php';

if (!isLoggedIn() || !hasRole('admin')) {
    header("Location: ../auth/login.php");
    exit();
}

// Handle Delete (Soft Delete)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_alat FROM alat WHERE id=$id"));
    mysqli_query($conn, "UPDATE alat SET deleted_at = NOW() WHERE id = $id");
    logActivity($_SESSION['user_id'], 'Hapus Alat', "Menghapus alat: {$alat['nama_alat']}");
    header("Location: alat.php");
    exit();
}

// Handle Restore
if (isset($_GET['restore'])) {
    $id = (int)$_GET['restore'];
    $alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_alat FROM alat WHERE id=$id"));
    mysqli_query($conn, "UPDATE alat SET deleted_at = NULL WHERE id = $id");
    logActivity($_SESSION['user_id'], 'Restore Alat', "Mengembalikan alat: {$alat['nama_alat']}");
    header("Location: alat.php?show=deleted");
    exit();
}

// Handle Permanent Delete
if (isset($_GET['permanent_delete'])) {
    $id = (int)$_GET['permanent_delete'];
    $alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_alat FROM alat WHERE id=$id"));
    mysqli_query($conn, "DELETE FROM alat WHERE id = $id");
    logActivity($_SESSION['user_id'], 'Hapus Permanen Alat', "Hapus permanen: {$alat['nama_alat']}");
    header("Location: alat.php?show=deleted");
    exit();
}

// Handle Add/Edit
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

$show_deleted = isset($_GET['show']) && $_GET['show'] == 'deleted';
$page_title = "Kelola Alat";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Kelola Alat Event</h2>
                    <p class="text-muted mb-0">Manajemen data alat dan inventori</p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body d-flex gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAlat" onclick="resetForm()">
                        <i class="bi bi-plus-circle"></i> Tambah Alat
                    </button>
                    <?php if (!$show_deleted): ?>
                    <a href="?show=deleted" class="btn btn-outline-info">
                        <i class="bi bi-archive"></i> Lihat Alat Terhapus
                    </a>
                    <?php else: ?>
                    <a href="alat.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($show_deleted): ?>
            <div class="alert alert-warning">
                <i class="bi bi-info-circle"></i> Menampilkan alat yang sudah dihapus.
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
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
                                if ($show_deleted) {
                                    $query = "SELECT a.*, k.nama_kategori FROM alat a LEFT JOIN kategori k ON a.kategori_id = k.id WHERE a.deleted_at IS NOT NULL ORDER BY a.deleted_at DESC";
                                } else {
                                    $query = "SELECT a.*, k.nama_kategori FROM alat a LEFT JOIN kategori k ON a.kategori_id = k.id WHERE a.deleted_at IS NULL ORDER BY a.id ASC";
                                }
                                $result = mysqli_query($conn, $query);
                                $no = 1;
                                if (mysqli_num_rows($result) == 0):
                                ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size:3rem;opacity:0.3;"></i>
                                        <p class="mt-2 mb-0"><?php echo $show_deleted ? 'Tidak ada alat terhapus' : 'Belum ada data alat'; ?></p>
                                    </td>
                                </tr>
                                <?php else: while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nama_alat']); ?></strong></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['merk'] ?? '-'); ?></span></td>
                                    <td><?php echo htmlspecialchars($row['nama_kategori'] ?? '-'); ?></td>
                                    <td><?php echo $row['jumlah_total']; ?></td>
                                    <td>
                                        <?php if ($row['jumlah_tersedia'] == 0): ?>
                                        <span class="badge bg-danger">Habis</span>
                                        <?php else: ?>
                                        <span class="badge bg-success"><?php echo $row['jumlah_tersedia']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-<?php echo $row['kondisi']=='baik'?'success':'warning'; ?>"><?php echo ucfirst($row['kondisi']); ?></span></td>
                                    <td><strong>Rp <?php echo number_format($row['harga_sewa'], 0, ',', '.'); ?></strong></td>
                                    <td>
                                        <?php if ($show_deleted): ?>
                                        <button class="btn btn-sm btn-success" onclick="if(confirm('Restore alat ini?')) location.href='?restore=<?php echo $row['id']; ?>'">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="if(confirm('Hapus permanen? Tidak bisa dikembalikan!')) location.href='?permanent_delete=<?php echo $row['id']; ?>'">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-sm btn-warning" onclick="editAlat(<?php echo $row['id']; ?>,'<?php echo addslashes($row['nama_alat']); ?>','<?php echo addslashes($row['merk']??''); ?>',<?php echo $row['kategori_id']; ?>,<?php echo $row['jumlah_total']; ?>,<?php echo $row['jumlah_tersedia']; ?>,'<?php echo $row['kondisi']; ?>',<?php echo $row['harga_sewa']; ?>,'<?php echo addslashes($row['deskripsi']); ?>')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="if(confirm('Hapus alat ini?')) location.href='?delete=<?php echo $row['id']; ?>'">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Alat -->
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
