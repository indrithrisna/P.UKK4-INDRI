<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/KategoriController.php';

if (!isLoggedIn() || !hasRole('admin')) {
    header("Location: ../auth/login.php");
    exit();
}

$controller = new KategoriController($conn);
$error_msg = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Cek apakah masih ada alat yang pakai kategori ini
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alat WHERE kategori_id = $id AND deleted_at IS NULL"));
    if ($cek['total'] > 0) {
        $error_msg = "Kategori tidak bisa dihapus karena masih digunakan oleh {$cek['total']} alat!";
    } else {
        $controller->delete($id);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') $controller->save($_POST);

$kategori_list = $controller->index();
$page_title = "Kelola Kategori";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2>Kelola Kategori</h2>
            <hr>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalKategori">
                <i class="bi bi-plus"></i> Tambah Kategori
            </button>
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($kategori_list as $row): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                                <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editKategori(<?php echo $row['id']; ?>,'<?php echo addslashes($row['nama_kategori']); ?>','<?php echo addslashes($row['deskripsi']); ?>')">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="if(confirm('Hapus kategori ini?')) location.href='?delete=<?php echo $row['id']; ?>'">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="kategoriId" value="">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi_kategori" class="form-control" rows="3"></textarea>
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
function editKategori(id, nama, deskripsi) {
    document.getElementById('kategoriId').value = id;
    document.getElementById('nama_kategori').value = nama;
    document.getElementById('deskripsi_kategori').value = deskripsi;
    document.getElementById('modalTitle').textContent = 'Edit Kategori';
    new bootstrap.Modal(document.getElementById('modalKategori')).show();
}
document.querySelector('[data-bs-target="#modalKategori"]').addEventListener('click', function() {
    document.getElementById('kategoriId').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Kategori';
});
</script>

<?php include '../includes/footer.php'; ?>
