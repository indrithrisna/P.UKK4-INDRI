<?php
session_start();
require_once '../config/database.php';

if (!isLoggedIn() || !hasRole('peminjam')) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle batalkan dengan alasan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batal_id'])) {
    $id     = (int)$_POST['batal_id'];
    $alasan = clean($_POST['alasan_batal'] ?? '');
    mysqli_query($conn, "UPDATE peminjaman SET status='ditolak', keterangan='[Dibatalkan peminjam] $alasan' WHERE id=$id AND peminjam_id=$user_id AND status='pending'");
    logActivity($_SESSION['user_id'], 'Batalkan Peminjaman', "Membatalkan peminjaman ID: $id. Alasan: $alasan");
    header("Location: peminjaman.php");
    exit();
}

$page_title = "Peminjaman Saya";
include '../includes/header.php';

// Statistik
$total_peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE peminjam_id = $user_id"))['total'];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE peminjam_id = $user_id AND status='pending'"))['total'];
$dipinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE peminjam_id = $user_id AND status='dipinjam'"))['total'];
$selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE peminjam_id = $user_id AND status='selesai'"))['total'];

// Hitung yang terlambat
$terlambat = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as total FROM peminjaman 
    WHERE peminjam_id = $user_id AND status='dipinjam' 
    AND tanggal_kembali < CURDATE()
"))['total'];
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/peminjam_sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2><i class="bi bi-clipboard-check"></i> Peminjaman Saya</h2>
            <p class="text-muted">Monitor dan kelola peminjaman alat Anda</p>
            <hr>
            
            <!-- Statistik Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-primary shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-clipboard-data text-primary" style="font-size: 2.5rem;"></i>
                            <h2 class="mt-2 mb-0 fw-bold"><?php echo $total_peminjaman; ?></h2>
                            <small class="text-muted">Total Peminjaman</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-history text-warning" style="font-size: 2.5rem;"></i>
                            <h2 class="mt-2 mb-0 fw-bold"><?php echo $pending; ?></h2>
                            <small class="text-muted">Menunggu Persetujuan</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-box-seam text-info" style="font-size: 2.5rem;"></i>
                            <h2 class="mt-2 mb-0 fw-bold"><?php echo $dipinjam; ?></h2>
                            <small class="text-muted">Sedang Dipinjam</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                            <h2 class="mt-2 mb-0 fw-bold"><?php echo $selesai; ?></h2>
                            <small class="text-muted">Sudah Dikembalikan</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alur Proses -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Alur Proses Peminjaman</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="process-step">
                                <div class="process-icon bg-primary">
                                    <i class="bi bi-person-check"></i>
                                </div>
                                <h6 class="mt-2">1. Login</h6>
                                <small class="text-muted">Login ke sistem</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="process-step">
                                <div class="process-icon bg-info">
                                    <i class="bi bi-search"></i>
                                </div>
                                <h6 class="mt-2">2. Pilih Alat</h6>
                                <small class="text-muted">Browse katalog alat</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="process-step">
                                <div class="process-icon bg-warning">
                                    <i class="bi bi-send"></i>
                                </div>
                                <h6 class="mt-2">3. Ajukan</h6>
                                <small class="text-muted">Submit peminjaman</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="process-step">
                                <div class="process-icon bg-secondary">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <h6 class="mt-2">4. Approval</h6>
                                <small class="text-muted">Petugas setujui</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="process-step">
                                <div class="process-icon bg-primary">
                                    <i class="bi bi-box-arrow-right"></i>
                                </div>
                                <h6 class="mt-2">5. Pinjam</h6>
                                <small class="text-muted">Alat diserahkan</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="process-step">
                                <div class="process-icon bg-success">
                                    <i class="bi bi-arrow-return-left"></i>
                                </div>
                                <h6 class="mt-2">6. Kembali</h6>
                                <small class="text-muted">Alat dikembalikan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="alat.php" class="btn btn-primary mb-3">
                <i class="bi bi-plus-circle"></i> Ajukan Peminjaman Baru
            </a>
            
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#semua">
                        <i class="bi bi-list-ul"></i> Semua Peminjaman
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dipinjam">
                        <i class="bi bi-box-seam"></i> Sedang Dipinjam
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dikembalikan">
                        <i class="bi bi-check-circle"></i> Sudah Dikembalikan
                    </button>
                </li>
            </ul>
            
            <div class="tab-content">
                <!-- Tab Semua -->
                <div class="tab-pane fade show active" id="semua">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Alat</th>
                                            <th>Jumlah</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tgl Kembali</th>
                                            <th>Status</th>
                                            <th>Total Biaya</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT p.* FROM peminjaman p
                                                 WHERE p.peminjam_id = $user_id
                                                 ORDER BY p.id DESC";
                                        $result = mysqli_query($conn, $query);
                                        
                                        if (mysqli_num_rows($result) == 0):
                                        ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">Belum ada peminjaman</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)):
                                            // Get alat details
                                            $detail_q = "SELECT dp.jumlah, a.nama_alat FROM detail_peminjaman dp
                                                        JOIN alat a ON dp.alat_id = a.id
                                                        WHERE dp.peminjaman_id = {$row['id']}
                                                        LIMIT 1";
                                            $detail = mysqli_fetch_assoc(mysqli_query($conn, $detail_q));
                                            
                                            $status_badge = [
                                                'pending' => 'warning',
                                                'disetujui' => 'info',
                                                'dipinjam' => 'primary',
                                                'selesai' => 'success',
                                                'ditolak' => 'danger'
                                            ];
                                            $badge = $status_badge[$row['status']] ?? 'secondary';
                                        ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $detail['nama_alat'] ?? '-'; ?></td>
                                            <td><?php echo $detail['jumlah'] ?? '-'; ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                                            <td><span class="badge bg-<?php echo $badge; ?>"><?php echo $row['status']; ?></span></td>
                                            <td>Rp <?php echo number_format($row['total_biaya'], 0, ',', '.'); ?></td>
                                            <td>
                                                <?php if ($row['status'] == 'pending'): ?>
                                                <button class="btn btn-sm btn-danger" onclick="showBatalModal(<?php echo $row['id']; ?>)">Batalkan</button>
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Sedang Dipinjam -->
                <div class="tab-pane fade" id="dipinjam">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Alat</th>
                                            <th>Jumlah</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tgl Kembali</th>
                                            <th>Status</th>
                                            <th>Total Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query2 = "SELECT p.* FROM peminjaman p
                                                  WHERE p.peminjam_id = $user_id AND p.status IN ('dipinjam', 'disetujui')
                                                  ORDER BY p.id DESC";
                                        $result2 = mysqli_query($conn, $query2);
                                        
                                        if (mysqli_num_rows($result2) == 0):
                                        ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Tidak ada alat yang sedang dipinjam</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php while ($row = mysqli_fetch_assoc($result2)):
                                            $detail_q = "SELECT dp.jumlah, a.nama_alat FROM detail_peminjaman dp
                                                        JOIN alat a ON dp.alat_id = a.id
                                                        WHERE dp.peminjaman_id = {$row['id']}
                                                        LIMIT 1";
                                            $detail = mysqli_fetch_assoc(mysqli_query($conn, $detail_q));
                                            
                                            $status_badge = [
                                                'disetujui' => 'info',
                                                'dipinjam' => 'primary'
                                            ];
                                            $badge = $status_badge[$row['status']] ?? 'secondary';
                                        ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $detail['nama_alat'] ?? '-'; ?></td>
                                            <td><?php echo $detail['jumlah'] ?? '-'; ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                                            <td><span class="badge bg-<?php echo $badge; ?>"><?php echo $row['status']; ?></span></td>
                                            <td>Rp <?php echo number_format($row['total_biaya'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Sudah Dikembalikan -->
                <div class="tab-pane fade" id="dikembalikan">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Alat</th>
                                            <th>Jumlah</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tgl Kembali</th>
                                            <th>Status</th>
                                            <th>Denda</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query3 = "SELECT p.* FROM peminjaman p
                                                  WHERE p.peminjam_id = $user_id AND p.status = 'selesai'
                                                  ORDER BY p.id DESC";
                                        $result3 = mysqli_query($conn, $query3);
                                        
                                        if (mysqli_num_rows($result3) == 0):
                                        ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Belum ada alat yang dikembalikan</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php while ($row = mysqli_fetch_assoc($result3)):
                                            $detail_q = "SELECT dp.jumlah, a.nama_alat FROM detail_peminjaman dp
                                                        JOIN alat a ON dp.alat_id = a.id
                                                        WHERE dp.peminjaman_id = {$row['id']}
                                                        LIMIT 1";
                                            $detail = mysqli_fetch_assoc(mysqli_query($conn, $detail_q));
                                        ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $detail['nama_alat'] ?? '-'; ?></td>
                                            <td><?php echo $detail['jumlah'] ?? '-'; ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                                            <td><span class="badge bg-success">selesai</span></td>
                                            <td>
                                                <?php if ($row['denda'] > 0): ?>
                                                <span class="text-danger">Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></span>
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.process-step {
    padding: 15px;
}

.process-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin: 0 auto;
}
</style>


<!-- Modal Batalkan -->
<div class="modal fade" id="modalBatal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-circle"></i> Batalkan Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="batal_id" id="batalId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="alasan_batal" class="form-control" rows="4"
                            placeholder="Contoh: Acara dibatalkan, salah pilih tanggal, dll..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Batalkan Peminjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showBatalModal(id) {
    document.getElementById('batalId').value = id;
    new bootstrap.Modal(document.getElementById('modalBatal')).show();
}
</script>

<?php include '../includes/footer.php'; ?>


