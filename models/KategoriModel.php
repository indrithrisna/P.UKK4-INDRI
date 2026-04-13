<?php
require_once __DIR__ . '/Model.php';

class KategoriModel extends Model {

    public function getAll() {
        return $this->fetchAll("SELECT * FROM kategori ORDER BY id ASC");
    }

    public function getById($id) {
        return $this->fetchOne("SELECT * FROM kategori WHERE id = " . (int)$id);
    }

    public function create($data) {
        $nama      = $this->escape($data['nama_kategori']);
        $deskripsi = $this->escape($data['deskripsi'] ?? '');
        return $this->query("INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama','$deskripsi')");
    }

    public function update($id, $data) {
        $id        = (int)$id;
        $nama      = $this->escape($data['nama_kategori']);
        $deskripsi = $this->escape($data['deskripsi'] ?? '');
        return $this->query("UPDATE kategori SET nama_kategori='$nama', deskripsi='$deskripsi' WHERE id=$id");
    }

    public function delete($id) {
        $id = (int)$id;
        $this->query("UPDATE alat SET kategori_id = NULL WHERE kategori_id = $id");
        return $this->query("DELETE FROM kategori WHERE id = $id");
    }
}
?>
