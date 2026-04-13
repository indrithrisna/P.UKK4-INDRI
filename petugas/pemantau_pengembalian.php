<?php
session_start();
require_once '../config/database.php';

if (!isLoggedIn() || !hasRole('petugas')) {
    header("Location: ../auth/login.php");
    exit();
}

// Handle pengembalian dengan denda
if (isset($_POST['proses_pengembalian'])) {
    $id = (int)$_POST['peminjaman_id'];
    $kondisi = clean($_POST['kondisi_pengembalian']);
    $catatan = clean($_POST['catatan_pengembalian']);
    $denda_keterlambatan = (float)$_POST['denda_keterlambatan'];
    $denda_kondisi = (float)$_POST['denda_kondisi'];
    $total_denda = $denda_keterlambatan + $denda_kondisi;
    
    // Kembalikan stok alat
    $detail_query = "SELECT alat_id, jumlah FROM detail_peminjaman WHERE peminjaman_id = $id";
    $detail_result = mysqli_query($conn, $detail_query);
    while ($detail = mysqli_fetch_assoc($detail_result)) {
        $alat_id = $detail['alat_id'];
        $jumlah = $detail['jumlah'];
        mysqli_query($conn, "UPDATE alat SET jumlah_tersedia = jumlah_tersedia + $jumlah WHERE id = $alat_id");
    }
    
    // Update peminjaman
    mysqli_query($conn, "UPDATE peminjaman SET 
                        status='selesai', 
                        tanggal_pengembalian=NOW(),
                        kondisi_pengembalian='$kondisi',
                        catatan_pengembalian='$catatan',
                        denda=$total_denda
                        WHERE id=$id");
    
    logActivity($_SESSION['user_id'], 'Pengembalian Alat', "Memproses pengembalian ID: $id dengan denda Rp " . number_format($total_denda, 0, ',', '.'));
    header("Location: pemantau_pengembalian.php?success=1");
    exit();
}

// Get pengaturan denda
$pengaturan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengaturan_denda LIMIT 1"));
if (!$pengaturan) {
    mysqli_query($conn, "INSERT INTO pengaturan_denda (denda_per_hari, denda_rusak_ringan, denda_rusak_berat, denda_hilang_persen) VALUES (10000, 50000, 100000, 100)");
    $pengaturan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengaturan_denda LIMIT 1"));
}

// Statistik
$sedang_dipinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam'"))['total'];
$terlambat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam' AND tanggal_kembali < CURDATE()"))['total'];
$hampir_jatuh_tempo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam' AND tanggal_kembali = CURDATE()"))['total'];
$total_denda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(denda) as total FROM peminjaman WHERE status='selesai'"))['total'] ?? 0;

$page_title = "Pemantau Pengembalian";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/petugas_sidebar.php'; ?>
        
        <div class="col-md-10 p-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1"><i class="bi bi-arrow-return-left text-primary"></i> Pemantau Pengembalian</h2>
                    <p class="text-muted mb-0">Monitor dan kelola pengembalian alat secara real-time</p>
                </div>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> Pengembalian berhasil diproses!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Statistik Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #42A5F5 0%, #1E88E5 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $sedang_dipinjam; ?></h3>
                                    <p class="mb-0 opacity-75">Sedang Dipinjam</p>
                                </div>
                                <div>
                                    <i class="bi bi-box-seam" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #EF5350 0%, #E53935 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $terlambat; ?></h3>
                                    <p class="mb-0 opacity-75">Terlambat</p>
                                </div>
                                <div>
                                    <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #FFA726 0%, #FB8C00 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $hampir_jatuh_tempo; ?></h3>
                                    <p class="mb-0 opacity-75">Hampir Jatuh Tempo</p>
                                </div>
                                <div>
                                    <i class="bi bi-clock-history" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #66BB6A 0%, #43A047 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">Rp<?php echo number_format($total_denda, 0, ',', '.'); ?></h3>
                                    <p class="mb-0 opacity-75">Total Denda</p>
                                </div>
                                <div>
                                    <i class="bi bi-cash-coin" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-0" role="tablist" style="border-bottom: 2px solid #E3F2FD;">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#terlambat" style="font-weight: 600;">
                        <i class="bi bi-exclamation-triangle text-danger"></i> Terlambat <span class="badge bg-danger"><?php echo $terlambat; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#hampir" style="font-weight: 600;">
                        <i class="bi bi-clock text-warning"></i> Hampir Jatuh Tempo <span class="badge bg-warning"><?php echo $hampir_jatuh_tempo; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#dipinjam" style="font-weight: 600;">
                        <i class="bi bi-box-seam text-info"></i> Semua Dipinjam <span class="badge bg-info"><?php echo $sedang_dipinjam; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#riwayat" style="font-weight: 600;">
                        <i class="bi bi-check-circle text-success"></i> Riwayat
                    </a>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content" style="margin-top: -1px;">
                <!-- Terlambat -->
                <div id="terlambat" class="tab-pane fade show active">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Peminjam</th>
                                            <th>Alat</th>
                                            <th>Jumlah</th>
                                            <th>Tgl Kembali</th>
                                            <th>Terlambat</th>
                                            <th>Denda</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT p.*, u.nama FROM peminjaman p 
                                                 JOIN users u ON p.peminjam_id = u.id 
                                                 WHERE p.status='dipinjam' AND p.tanggal_kembali < CURDATE()
                                                 ORDER BY p.tanggal_kembali ASC";
                                        $result = mysqli_query($conn, $query);
                                        $data_terlambat = [];
                                        
                                        if (mysqli_num_rows($result) == 0) {
                                            echo '<tr><td colspan="7" class="text-center">Tidak ada peminjaman yang terlambat</td></tr>';
                                        }
                                        
                                        while ($row = mysqli_fetch_assoc($result)):
                                            $data_terlambat[] = $row;
                                            $hari_terlambat = (strtotime(date('Y-m-d')) - strtotime($row['tanggal_kembali'])) / (60 * 60 * 24);
                                            $denda_terlambat = $hari_terlambat * $pengaturan['denda_per_hari'];
                                            
                                            $detail_query = "SELECT dp.*, a.nama_alat FROM detail_peminjaman dp
                                                            JOIN alat a ON dp.alat_id = a.id
                                                            WHERE dp.peminjaman_id = {$row['id']}";
                                            $detail_result = mysqli_query($conn, $detail_query);
                                            $alat_list = [];
                                            $total_jumlah = 0;
                                            while ($detail = mysqli_fetch_assoc($detail_result)) {
                                                $alat_list[] = $detail['nama_alat'];
                                                $total_jumlah += $detail['jumlah'];
                                            }
                                        ?>
                                        <tr class="table-danger">
                                            <td><?php echo $row['nama']; ?></td>
                                            <td><?php echo implode(', ', $alat_list); ?></td>
                                            <td><?php echo $total_jumlah; ?> unit</td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                                            <td><span class="badge bg-danger"><?php echo $hari_terlambat; ?> hari</span></td>
                                            <td>Rp<?php echo number_format($denda_terlambat, 0, ',', '.'); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pengembalianModal<?php echo $row['id']; ?>">
                                                    <i class="bi bi-check-circle"></i> Proses
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
                
                <!-- Hampir Jatuh Tempo -->
                <div id="hampir" class="tab-pane fade">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Peminjam</th>
                                            <th>Alat</th>
                                            <th>Jumlah</th>
                                            <th>Tgl Kembali</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT p.*, u.nama FROM peminjaman p 
                                                 JOIN users u ON p.peminjam_id = u.id 
                                                 WHERE p.status='dipinjam' AND p.tanggal_kembali = CURDATE()
                                                 ORDER BY p.created_at ASC";
                                        $result = mysqli_query($conn, $query);
                                        $data_hampir = [];
                                        
                                        if (mysqli_num_rows($result) == 0) {
                                            echo '<tr><td colspan="5" class="text-center">Tidak ada peminjaman yang hampir jatuh tempo</td></tr>';
                                        }
                                        
                                        while ($row = mysqli_fetch_assoc($result)):
                                            $data_hampir[] = $row;
                                            $detail_query = "SELECT dp.*, a.nama_alat FROM detail_peminjaman dp
                                                            JOIN alat a ON dp.alat_id = a.id
                                                            WHERE dp.peminjaman_id = {$row['id']}";
                                            $detail_result = mysqli_query($conn, $detail_query);
                                            $alat_list = [];
                                            $total_jumlah = 0;
                                            while ($detail = mysqli_fetch_assoc($detail_result)) {
                                                $alat_list[] = $detail['nama_alat'];
                                                $total_jumlah += $detail['jumlah'];
                                            }
                                        ?>
                                        <tr class="table-warning">
                                            <td><?php echo $row['nama']; ?></td>
                                            <td><?php echo implode(', ', $alat_list); ?></td>
                                            <td><?php echo $total_jumlah; ?> unit</td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pengembalianModal<?php echo $row['id']; ?>">
                                                    <i class="bi bi-check-circle"></i> Proses
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
                
                <!-- Semua Peminjaman -->
                <div id="dipinjam" class="tab-pane fade">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Peminjam</th>
                                            <th>Alat</th>
                                            <th>Jumlah</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tgl Kembali</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT p.*, u.nama FROM peminjaman p 
                                                 JOIN users u ON p.peminjam_id = u.id 
                                                 WHERE p.status='dipinjam'
                                                 ORDER BY p.tanggal_kembali ASC";
                                        $result = mysqli_query($conn, $query);
                                        $data_dipinjam = [];
                                        
                                        if (mysqli_num_rows($result) == 0) {
                                            echo '<tr><td colspan="7" class="text-center">Tidak ada peminjaman aktif</td></tr>';
                                        }
                                        
                                        while ($row = mysqli_fetch_assoc($result)):
                                            $data_dipinjam[] = $row;
                                            $terlambat = strtotime($row['tanggal_kembali']) < strtotime(date('Y-m-d'));
                                            $detail_query = "SELECT dp.*, a.nama_alat FROM detail_peminjaman dp
                                                            JOIN alat a ON dp.alat_id = a.id
                                                            WHERE dp.peminjaman_id = {$row['id']}";
                                            $detail_result = mysqli_query($conn, $detail_query);
                                            $alat_list = [];
                                            $total_jumlah = 0;
                                            while ($detail = mysqli_fetch_assoc($detail_result)) {
                                                $alat_list[] = $detail['nama_alat'];
                                                $total_jumlah += $detail['jumlah'];
                                            }
                                        ?>
                                        <tr class="<?php echo $terlambat ? 'table-danger' : ''; ?>">
                                            <td><?php echo $row['nama']; ?></td>
                                            <td><?php echo implode(', ', $alat_list); ?></td>
                                            <td><?php echo $total_jumlah; ?> unit</td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                                            <td>
                                                <?php if ($terlambat): ?>
                                                    <span class="badge bg-danger">Terlambat</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Tepat Waktu</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pengembalianModal<?php echo $row['id']; ?>">
                                                    <i class="bi bi-check-circle"></i> Proses
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
                
                <!-- Riwayat -->
                <div id="riwayat" class="tab-pane fade">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Peminjam</th>
                                            <th>Alat</th>
                                            <th>Jumlah</th>
                                            <th>Tgl Dikembalikan</th>
                                            <th>Kondisi</th>
                                            <th>Denda</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT p.*, u.nama FROM peminjaman p 
                                                 JOIN users u ON p.peminjam_id = u.id 
                                                 WHERE p.status='selesai'
                                                 ORDER BY p.tanggal_pengembalian DESC
                                                 LIMIT 20";
                                        $result = mysqli_query($conn, $query);
                                        
                                        while ($row = mysqli_fetch_assoc($result)):
                                            $detail_query = "SELECT dp.*, a.nama_alat FROM detail_peminjaman dp
                                                            JOIN alat a ON dp.alat_id = a.id
                                                            WHERE dp.peminjaman_id = {$row['id']}";
                                            $detail_result = mysqli_query($conn, $detail_query);
                                            $alat_list = [];
                                            $total_jumlah = 0;
                                            while ($detail = mysqli_fetch_assoc($detail_result)) {
                                                $alat_list[] = $detail['nama_alat'];
                                                $total_jumlah += $detail['jumlah'];
                                            }
                                            
                                            $kondisi_badge = [
                                                'baik' => 'success',
                                                'rusak ringan' => 'warning',
                                                'rusak berat' => 'danger',
                                                'hilang' => 'dark'
                                            ];
                                        ?>
                                        <tr>
                                            <td><?php echo $row['nama']; ?></td>
                                            <td><?php echo implode(', ', $alat_list); ?></td>
                                            <td><?php echo $total_jumlah; ?> unit</td>
                                            <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_pengembalian'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $kondisi_badge[$row['kondisi_pengembalian']]; ?>">
                                                    <?php echo ucfirst($row['kondisi_pengembalian']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($row['denda'] > 0): ?>
                                                    <span class="text-danger">Rp<?php echo number_format($row['denda'], 0, ',', '.'); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo !empty($row['catatan_pengembalian']) ? htmlspecialchars($row['catatan_pengembalian']) : '<span class="text-muted">-</span>'; ?>
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
            
            <!-- Render ALL Modals Once at the End -->
            <?php
            // Get all active peminjaman for modals
            $all_query = "SELECT p.*, u.nama FROM peminjaman p 
                         JOIN users u ON p.peminjam_id = u.id 
                         WHERE p.status='dipinjam'
                         ORDER BY p.id ASC";
            $all_result = mysqli_query($conn, $all_query);
            
            while ($row = mysqli_fetch_assoc($all_result)):
                include '../admin/modal_pengembalian.php';
            endwhile;
            ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
