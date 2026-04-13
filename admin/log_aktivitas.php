<?php
session_start();
require_once '../config/database.php';

if (!isLoggedIn() || !hasRole('admin')) {
    header("Location: ../auth/login.php");
    exit();
}

$filter_user = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$filter_date = isset($_GET['date']) ? clean($_GET['date']) : '';

$page_title = "Log Aktivitas";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="col-md-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-1"><i class="bi bi-clock-history text-primary"></i> Log Aktivitas User</h2>
                    <p class="text-muted mb-0">Rekam jejak aktivitas pengguna sistem</p>
                </div>
                <button class="btn btn-success" onclick="cetakLog()">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </button>
            </div>
            <hr>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter User</label>
                            <select name="user_id" class="form-control">
                                <option value="0">Semua User</option>
                                <?php
                                $users = mysqli_query($conn, "SELECT id, nama, username FROM users ORDER BY nama");
                                while ($u = mysqli_fetch_assoc($users)):
                                ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo $filter_user == $u['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u['nama']); ?> (<?php echo htmlspecialchars($u['username']); ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Filter Tanggal</label>
                            <input type="date" name="date" class="form-control" value="<?php echo $filter_date; ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Aktivitas</th>
                                    <th>Keterangan</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $where = "1=1";
                                if ($filter_user > 0) $where .= " AND l.user_id = $filter_user";
                                if ($filter_date)     $where .= " AND DATE(l.created_at) = '$filter_date'";

                                $result = mysqli_query($conn, "SELECT l.*, u.nama, u.username FROM log_aktivitas l
                                                               JOIN users u ON l.user_id = u.id
                                                               WHERE $where
                                                               ORDER BY l.created_at DESC
                                                               LIMIT 100");
                                $count = mysqli_num_rows($result);
                                if ($count == 0):
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Tidak ada data log aktivitas</td>
                                </tr>
                                <?php else: while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['nama']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['username']); ?></small>
                                    </td>
                                    <td><span class="badge bg-primary"><?php echo htmlspecialchars($row['aktivitas']); ?></span></td>
                                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                    <td><small><?php echo $row['ip_address']; ?></small></td>
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

<style>
@media print {
    .navbar, .sidebar, form, .card.mb-3, button { display: none !important; }
    .col-md-10 { width: 100% !important; max-width: 100% !important; }
    .container-fluid { padding: 0 !important; }
    h2 { text-align: center; margin-bottom: 20px; }
}
</style>

<script>
function cetakLog() {
    const originalTitle = document.title;
    document.title = 'Log Aktivitas - <?php echo date("d/m/Y"); ?>';
    window.print();
    document.title = originalTitle;
}
</script>

<?php include '../includes/footer.php'; ?>
