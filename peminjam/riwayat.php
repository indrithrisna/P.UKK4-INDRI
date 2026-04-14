<?php
session_start();
require_once '../config/database.php';

if (!isLoggedIn() || !hasRole('peminjam')) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle hapus riwayat
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    // Hanya bisa hapus yang sudah selesai atau ditolak
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM peminjaman WHERE id=$id AND peminjam_id=$user_id AND status IN ('selesai','ditolak')"));
    if ($cek) {
        mysqli_query($conn, "DELETE FROM detail_peminjaman WHERE peminjaman_id=$id");
        mysqli_query($conn, "DELETE FROM peminjaman WHERE id=$id");
        logActivity($user_id, 'Hapus Riwayat', "Menghapus riwayat peminjaman ID: $id");
    }
    header("Location: riwayat.php");
    exit();
}

$page_title = "Riwayat Peminjaman";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/peminjam_sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1"><i class="bi bi-clock-history text-primary"></i> Riwayat Peminjaman</h2>
                    <p class="text-muted mb-0">Lihat semua riwayat peminjaman alat Anda</p>
                </div>
            </div>
            
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-hash"></i> ID</th>
                                    <th><i class="bi bi-box-seam"></i> Alat yang Dipinjam</th>
                                    <th><i class="bi bi-calendar-check"></i> Tgl Pinjam</th>
                                    <th><i class="bi bi-calendar-x"></i> Tgl Kembali</th>
                                    <th><i class="bi bi-calendar-event"></i> Tgl Pengembalian</th>
                                    <th><i class="bi bi-info-circle"></i> Status</th>
                                    <th><i class="bi bi-sticky"></i> Catatan</th>
                                    <th><i class="bi bi-cash"></i> Total Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM peminjaman 
                                     WHERE peminjam_id = $user_id AND status IN ('selesai', 'ditolak')
                                     ORDER BY id DESC";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)):
                                // Get equipment details
                                $detail_q = "SELECT dp.*, a.nama_alat FROM detail_peminjaman dp
                                            JOIN alat a ON dp.alat_id = a.id
                                            WHERE dp.peminjaman_id = {$row['id']}";
                                $detail_r = mysqli_query($conn, $detail_q);
                                $alat_list = [];
                                while ($d = mysqli_fetch_assoc($detail_r)) {
                                    $alat_list[] = $d['nama_alat'] . ' (' . $d['jumlah'] . 'x)';
                                }
                                $alat_display = implode(', ', $alat_list);
                            ?>
                            <tr>
                                <td><span class="badge bg-secondary">#<?php echo $row['id']; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-box text-primary me-2" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <div><?php echo strlen($alat_display) > 50 ? substr($alat_display, 0, 50) . '...' : $alat_display; ?></div>
                                            <button class="btn btn-sm btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $row['id']; ?>">
                                                <i class="bi bi-eye"></i> Lihat Detail
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td><i class="bi bi-calendar3 text-muted me-1"></i><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                <td><i class="bi bi-calendar3 text-muted me-1"></i><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                <td>
                                    <?php if ($row['tanggal_pengembalian']): ?>
                                        <i class="bi bi-check-circle text-success me-1"></i><?php echo date('d/m/Y H:i', strtotime($row['tanggal_pengembalian'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'selesai'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Selesai</span>
                                    <?php else: ?>
                                        <?php if (!empty($row['keterangan']) && strpos($row['keterangan'], '[Dibatalkan peminjam]') !== false): ?>
                                            <span class="badge bg-secondary"><i class="bi bi-slash-circle"></i> Dibatalkan</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] != 'selesai' && !empty($row['keterangan'])): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars(str_replace('[Dibatalkan peminjam] ', '', $row['keterangan'])); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong class="text-primary">Rp <?php echo number_format($row['total_biaya'], 0, ',', '.'); ?></strong></td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="if(confirm('Hapus riwayat ini?')) location.href='?hapus=<?php echo $row['id']; ?>'">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            
            <!-- Modals -->
            <?php
            // Render all modals outside the table
            mysqli_data_seek($result, 0);
            while ($row = mysqli_fetch_assoc($result)):
            ?>
                            <!-- Modal Detail -->
                            <div class="modal fade" id="detailModal<?php echo $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title"><i class="bi bi-receipt"></i> Detail Riwayat Peminjaman #<?php echo $row['id']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle"></i> Informasi lengkap tentang peminjaman Anda
                                            </div>
                                            
                                            <h6><i class="bi bi-card-text text-primary"></i> Informasi Peminjaman</h6>
                                            <table class="table table-sm table-bordered">
                                                <tr>
                                                    <td width="40%"><strong><i class="bi bi-calendar-check"></i> Tanggal Pinjam</strong></td>
                                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><i class="bi bi-calendar-x"></i> Tanggal Kembali</strong></td>
                                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><i class="bi bi-calendar-event"></i> Tanggal Pengembalian</strong></td>
                                                    <td><?php echo $row['tanggal_pengembalian'] ? date('d/m/Y H:i', strtotime($row['tanggal_pengembalian'])) : '-'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><i class="bi bi-info-circle"></i> Status</strong></td>
                                                    <td>
                                                        <?php if ($row['status'] == 'selesai'): ?>
                                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Selesai</span>
                                                        <?php else: ?>
                                                            <?php if (!empty($row['keterangan']) && strpos($row['keterangan'], '[Dibatalkan peminjam]') !== false): ?>
                                                                <span class="badge bg-secondary"><i class="bi bi-slash-circle"></i> Dibatalkan</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>

                                                    <!---catatan untuk pembatalan oleh peminjam--->
        
                                                    <td><strong><i class="bi bi-sticky"></i> Catatan</strong></td>
                                                    <td>
                                                        <?php if (!empty($row['keterangan'])): ?>
                                                            <?php echo htmlspecialchars(str_replace('[Dibatalkan peminjam] ', '', $row['keterangan'])); ?>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php if ($row['denda'] > 0): ?>
                                                <tr class="table-danger">
                                                    <td><strong><i class="bi bi-exclamation-triangle"></i> Denda</strong></td>
                                                    <td class="text-danger"><strong>Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></strong></td>
                                                </tr>
                                                <?php endif; ?>
                                            </table>
                                            
                                            <h6 class="mt-3"><i class="bi bi-box-seam text-primary"></i> Alat yang Dipinjam</h6>
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th><i class="bi bi-tag"></i> Nama Alat</th>
                                                        <th><i class="bi bi-123"></i> Jumlah</th>
                                                        <th><i class="bi bi-cash"></i> Harga</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $detail_q2 = "SELECT dp.*, a.nama_alat FROM detail_peminjaman dp
                                                                JOIN alat a ON dp.alat_id = a.id
                                                                WHERE dp.peminjaman_id = {$row['id']}";
                                                    $detail_r2 = mysqli_query($conn, $detail_q2);
                                                    while ($d = mysqli_fetch_assoc($detail_r2)):
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $d['nama_alat']; ?></td>
                                                        <td><?php echo $d['jumlah']; ?> unit</td>
                                                        <td>Rp <?php echo number_format($d['subtotal'], 0, ',', '.'); ?></td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                            
                                            <h6 class="mt-3"><i class="bi bi-calculator text-primary"></i> Rincian Biaya</h6>
                                            <table class="table table-sm table-bordered mb-0">
                                                <tr class="table-light">
                                                    <td width="70%"><strong><i class="bi bi-cash-stack"></i> Total Biaya Sewa</strong></td>
                                                    <td><strong class="text-primary">Rp <?php echo number_format($row['total_biaya'], 0, ',', '.'); ?></strong></td>
                                                </tr>
                                                <?php if ($row['denda'] > 0): ?>
                                                <tr class="table-danger">
                                                    <td><strong><i class="bi bi-exclamation-triangle"></i> Denda</strong></td>
                                                    <td><strong class="text-danger">Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></strong></td>
                                                </tr>
                                                <tr class="table-warning">
                                                    <td><strong><i class="bi bi-receipt"></i> Total Keseluruhan</strong></td>
                                                    <td><strong class="text-dark">Rp <?php echo number_format($row['total_biaya'] + $row['denda'], 0, ',', '.'); ?></strong></td>
                                                </tr>
                                                <?php endif; ?>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>


