<?php
session_start();
require_once 'koneksi.php';

// Jika pengguna belum login atau bukan pembeli, arahkan ke halaman login.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pembeli') {
    header("Location: login.php");
    exit();
}

// Proses pembelian barang
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $barang_id = $_POST['barang_id'];
    $jumlah = $_POST['jumlah'];

    // Lakukan validasi apakah jumlah barang yang dibeli lebih dari 0
    if ($jumlah <= 0) {
        $error_message = "Jumlah barang harus lebih dari 0.";
    } else {
        // Lakukan proses pembelian ke database (contoh: tabel transaksi)
        $sql = "INSERT INTO transaksi (user_id, barang_id, jumlah) VALUES ($user_id, $barang_id, $jumlah)";
        if (mysqli_query($conn, $sql)) {
            $sql = "UPDATE barang SET stok = stok - $jumlah WHERE id = $barang_id";
            if (mysqli_query($conn, $sql)) {
            $success_message = "Pembelian berhasil!";
            }
        } else {
            $error_message = "Pembelian gagal. Silakan coba lagi.";
        }
    }
}

// Ambil daftar barang dari tabel barang
$sql = "SELECT * FROM barang";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Halaman Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h2>Selamat datang, Pembeli!</h2>
        <?php
        if (isset($success_message)) {
            echo '<div class="alert alert-success">' . $success_message . '</div>';
        }
        if (isset($error_message)) {
            echo '<div class="alert alert-danger">' . $error_message . '</div>';
        }
        ?>
        <h3>Daftar Barang</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nama_barang"] . "</td>";
                    echo "<td>" . $row["harga"] . "</td>";
                    echo "<td>" . $row["stok"] . "</td>";
                    echo '<td>';
                    echo '<form action="pembeli.php" method="POST">';
                    echo '<input type="hidden" name="barang_id" value="' . $row["id"] . '">';
                    echo '<div class="form-group">';
                    echo '<label for="jumlah">Jumlah:</label>';
                    echo '<input type="number" class="form-control" id="jumlah" name="jumlah" min="1" max="' . $row["stok"] . '" required>';
                    echo '</div>';
                    echo '<button type="submit" class="btn btn-primary">Beli</button>';
                    echo '</form>';
                    echo '</td>';
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
