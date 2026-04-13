<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/PeminjamanController.php';

if (!isLoggedIn() || !hasRole('petugas')) {
    header("Location: ../auth/login.php");
    exit();
}

$controller = new PeminjamanController($conn);

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id     = (int)$_GET['id'];
    $action = $_GET['action'];
    if ($action == 'approve') $controller->dipinjam($id, 'petugas');
}

// Handle tolak dengan alasan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tolak_id'])) {
    $id     = (int)$_POST['tolak_id'];
    $alasan = clean($_POST['alasan_tolak'] ?? '');
    // Simpan alasan ke kolom keterangan
    mysqli_query($conn, "UPDATE peminjaman SET status='ditolak', keterangan='$alasan', petugas_id={$_SESSION['user_id']} WHERE id=$id AND status='pending'");
    logActivity($_SESSION['user_id'], 'Tolak Peminjaman', "Menolak peminjaman ID: $id. Alasan: $alasan");
    header("Location: peminjaman.php");
    exit();
}

$peminjaman_list = $controller->index();
$page_title = "Kelola Peminjaman";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/petugas_sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2>Kelola Peminjaman</h2>
            <hr>
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Total Biaya</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($peminjaman_list as $row): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                <td><span class="badge bg-<?php
                                    $colors = ['pending'=>'warning','disetujui'=>'info','dipinjam'=>'primary','selesai'=>'success','ditolak'=>'danger'];
                                    echo $colors[$row['status']] ?? 'secondary';
                                ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td>Rp <?php echo number_format($row['total_biaya'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($row['status'] == 'ditolak' && !empty($row['keterangan'])): ?>
                                    <small class="text-danger"><i class="bi bi-info-circle"></i> <?php echo htmlspecialchars($row['keterangan']); ?></small>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'pending'): ?>
                                    <button class="btn btn-sm btn-success" onclick="if(confirm('Setujui peminjaman ini?')) location.href='?action=approve&id=<?php echo $row['id']; ?>'">
                                        <i class="bi bi-check"></i> Setujui
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="showTolakModal(<?php echo $row['id']; ?>)">
                                        <i class="bi bi-x"></i> Tolak
                                    </button>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
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

<!-- Modal Tolak -->
<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-circle"></i> Tolak Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="tolak_id" id="tolakId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_tolak" id="alasanTolak" class="form-control" rows="4"
                            placeholder="Contoh: Stok tidak mencukupi, tanggal bentrok, dll..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Tolak Peminjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTolakModal(id) {
    document.getElementById('tolakId').value = id;
    document.getElementById('alasanTolak').value = '';
    new bootstrap.Modal(document.getElementById('modalTolak')).show();
}
</script>

<?php include '../includes/footer.php'; ?>
