<?php
require_once __DIR__ . '/../controllers/Controller.php';
require_once __DIR__ . '/../models/PeminjamanModel.php';
require_once __DIR__ . '/../models/AlatModel.php';

class PeminjamanController extends Controller {
    private $model;
    private $alatModel;

    public function __construct($conn) {
        $this->model     = new PeminjamanModel($conn);
        $this->alatModel = new AlatModel($conn);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function getByPeminjam($user_id) {
        return $this->model->getByPeminjam($user_id);
    }

    public function getByStatus($status) {
        return $this->model->getByStatus($status);
    }

    public function getDetail($peminjaman_id) {
        return $this->model->getDetail($peminjaman_id);
    }

    // Buat peminjaman baru (dari peminjam)
    public function buat($postData, $alatList) {
        $this->requireRole('peminjam');

        $total_biaya = 0;
        $tgl_pinjam  = $postData['tanggal_pinjam'];
        $tgl_kembali = $postData['tanggal_kembali'];
        $durasi      = (strtotime($tgl_kembali) - strtotime($tgl_pinjam)) / 86400;

        // Hitung total biaya
        foreach ($alatList as $item) {
            $alat = $this->alatModel->getById($item['alat_id']);
            $total_biaya += $alat['harga_sewa'] * $item['jumlah'] * $durasi;
        }

        $postData['peminjam_id'] = $_SESSION['user_id'];
        $postData['total_biaya'] = $total_biaya;

        $peminjaman_id = $this->model->create($postData);

        // Simpan detail — TIDAK kurangi stok dulu, stok dikurangi saat disetujui/dipinjam
        foreach ($alatList as $item) {
            $alat     = $this->alatModel->getById($item['alat_id']);
            $subtotal = $alat['harga_sewa'] * $item['jumlah'] * $durasi;
            $this->model->addDetail($peminjaman_id, $item['alat_id'], $item['jumlah'], $alat['harga_sewa'], $subtotal);
        }

        logActivity($_SESSION['user_id'], 'Buat Peminjaman', "Membuat peminjaman baru ID: $peminjaman_id");
        $this->redirect('peminjaman.php');
    }

    // Setujui peminjaman (petugas/admin)
    public function setujui($id, $role = 'petugas') {
        $this->requireRole($role);
        $this->model->updateStatus($id, 'disetujui', $_SESSION['user_id']);
        logActivity($_SESSION['user_id'], 'Setujui Peminjaman', "Menyetujui peminjaman ID: $id");
        $this->redirect('peminjaman.php');
    }

    // Tolak peminjaman — stok tidak perlu dikembalikan karena belum dikurangi saat pending
    public function tolak($id, $role = 'petugas') {
        $this->requireRole($role);
        $this->model->updateStatus($id, 'ditolak', $_SESSION['user_id']);
        logActivity($_SESSION['user_id'], 'Tolak Peminjaman', "Menolak peminjaman ID: $id");
        $this->redirect('peminjaman.php');
    }

    // Tandai sedang dipinjam — kurangi stok saat ini
    public function dipinjam($id, $role = 'petugas') {
        $this->requireRole($role);
        // Kurangi stok alat
        $details = $this->model->getDetail($id);
        foreach ($details as $d) {
            $this->alatModel->kurangiStok($d['alat_id'], $d['jumlah']);
        }
        $this->model->updateStatus($id, 'dipinjam', $_SESSION['user_id']);
        logActivity($_SESSION['user_id'], 'Dipinjam', "Alat peminjaman ID: $id sedang dipinjam");
        $this->redirect('peminjaman.php');
    }

    // Hapus peminjaman
    public function delete($id, $role = 'admin') {
        $this->requireRole($role);
        $this->model->delete($id);
        logActivity($_SESSION['user_id'], 'Hapus Peminjaman', "Menghapus peminjaman ID: $id");
        $this->redirect('peminjaman.php');
    }
}
?>
