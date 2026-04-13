<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/UserController.php';

if (!isLoggedIn() || !hasRole('admin')) {
    header("Location: ../auth/login.php");
    exit();
}

$controller = new UserController($conn);

if (isset($_GET['delete']))                $controller->delete((int)$_GET['delete']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') $controller->save($_POST);

$users = $controller->index();
$page_title = "Kelola User";
include '../includes/header.php';
?>
<div class="container-fluid">
<div class="row">
<?php include '../includes/admin_sidebar.php'; ?>
<div class="col-md-10 p-4">
<div class="d-flex justify-content-between align-items-center mb-4">
<div>
<h2 class="mb-1">Kelola User</h2>
<p class="text-muted mb-0">Manajemen pengguna sistem</p>
</div>
</div>
<div class="card mb-3">
<div class="card-body">
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUser">
<i class="bi bi-plus-circle"></i> Tambah User
</button>
</div>
</div>
<div class="card">
<div class="card-body">
<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
<th>No</th><th>Username</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Role</th><th>Aksi</th>
</tr>
</thead>
<tbody>
<?php $no = 1; foreach ($users as $row): ?>
<tr>
<td><?php echo $no++; ?></td>
<td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
<td><?php echo htmlspecialchars($row['nama']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['telepon']); ?></td>
<td>
<span class="badge bg-<?php echo $row['role']=='admin'?'danger':($row['role']=='petugas'?'warning':'info'); ?>">
<?php echo ucfirst($row['role']); ?>
</span>
</td>
<td>
<button class="btn btn-sm btn-warning" onclick="editUser(
<?php echo $row['id']; ?>,
'<?php echo addslashes($row['username']); ?>',
'<?php echo addslashes($row['nama']); ?>',
'<?php echo addslashes($row['email']); ?>',
'<?php echo addslashes($row['telepon']); ?>',
'<?php echo $row['role']; ?>'
)"><i class="bi bi-pencil"></i></button>
<button class="btn btn-sm btn-danger" onclick="if(confirm('Hapus user ini?')) location.href='?delete=<?php echo $row['id']; ?>'">
<i class="bi bi-trash"></i>
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
</div>

<div class="modal fade" id="modalUser" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" id="formUser">
<div class="modal-header">
<h5 class="modal-title" id="modalTitle">Tambah User</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="hidden" name="id" id="userId" value="">
<div class="mb-3">
<label class="form-label">Username</label>
<input type="text" name="username" id="username" class="form-control" required>
</div>
<div class="mb-3">
<label class="form-label">Password <span id="passwordNote">(kosongkan jika tidak diubah)</span></label>
<input type="password" name="password" id="password" class="form-control">
</div>
<div class="mb-3">
<label class="form-label">Nama Lengkap</label>
<input type="text" name="nama" id="nama" class="form-control" required>
</div>
<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" id="email" class="form-control" required oninput="cekEmail(this)">
<div id="emailFeedback" class="mt-1" style="font-size:0.85rem;"></div>
</div>
<div class="mb-3">
<label class="form-label">Telepon</label>
<input type="text" name="telepon" id="telepon" class="form-control">
</div>
<div class="mb-3">
<label class="form-label">Role</label>
<select name="role" id="role" class="form-control" required>
<option value="admin">Admin</option>
<option value="petugas">Petugas</option>
<option value="peminjam">Peminjam</option>
</select>
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
function editUser(id, username, nama, email, telepon, role) {
    document.getElementById('userId').value = id;
    document.getElementById('username').value = username;
    document.getElementById('nama').value = nama;
    document.getElementById('email').value = email;
    document.getElementById('telepon').value = telepon;
    document.getElementById('role').value = role;
    document.getElementById('password').value = '';
    document.getElementById('password').removeAttribute('required');
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('passwordNote').style.display = 'inline';
    new bootstrap.Modal(document.getElementById('modalUser')).show();
}
document.querySelector('[data-bs-target="#modalUser"]').addEventListener('click', function() {
    document.getElementById('formUser').reset();
    document.getElementById('userId').value = '';
    document.getElementById('password').setAttribute('required', 'required');
    document.getElementById('modalTitle').textContent = 'Tambah User';
    document.getElementById('passwordNote').style.display = 'none';
    document.getElementById('emailFeedback').innerHTML = '';
});

let emailTimer;
function cekEmail(input) {
    clearTimeout(emailTimer);
    const email = input.value.trim();
    const excludeId = document.getElementById('userId').value;
    const feedback = document.getElementById('emailFeedback');

    if (!email || !email.includes('@')) {
        feedback.innerHTML = '';
        return;
    }

    feedback.innerHTML = '<span class="text-muted"><i class="bi bi-hourglass-split"></i> Mengecek...</span>';

    emailTimer = setTimeout(() => {
        fetch(`check_email.php?email=${encodeURIComponent(email)}&exclude_id=${excludeId}`)
            .then(r => r.json())
            .then(data => {
                if (data.available) {
                    feedback.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Email tersedia</span>';
                    input.setCustomValidity('');
                } else {
                    feedback.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Email sudah digunakan!</span>';
                    input.setCustomValidity('Email sudah digunakan');
                }
            });
    }, 500);
}
</script>

<?php include '../includes/footer.php'; ?>
