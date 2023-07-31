<?php
session_start();
require_once 'koneksi.php';

// Jika pengguna belum login atau bukan admin, arahkan ke halaman login.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Tambah barang baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_barang'])) {
    $nama_barang = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $harga_pokok = $_POST['harga_pokok'];

    $sql = "INSERT INTO barang (nama_barang, harga, stok, harga_pokok) VALUES ('$nama_barang', '$harga', '$stok', '$harga_pokok')";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Barang berhasil ditambahkan!";
    } else {
        $error_message = "Gagal menambahkan barang. Silakan coba lagi.";
    }
}

// Tambah pengguna baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'pembeli')";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Pengguna berhasil ditambahkan!";
    } else {
        $error_message = "Gagal menambahkan pengguna. Silakan coba lagi.";
    }
}

// Hapus barang
if (isset($_GET['delete_barang'])) {
    $barang_id = $_GET['delete_barang'];

    $sql = "DELETE FROM barang WHERE id = $barang_id";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Barang berhasil dihapus!";
    } else {
        $error_message = "Gagal menghapus barang. Silakan coba lagi.";
    }
}

// Hapus pengguna
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];

    $sql = "DELETE FROM users WHERE id = $user_id";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Pengguna berhasil dihapus!";
    } else {
        $error_message = "Gagal menghapus pengguna. Silakan coba lagi.";
    }
    
}

// Ambil daftar barang dari tabel barang
$sql_barang = "SELECT * FROM barang";
$result_barang = mysqli_query($conn, $sql_barang);

// Ambil daftar pengguna dari tabel users
$sql_users = "SELECT * FROM users";
$result_users = mysqli_query($conn, $sql_users);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Halaman Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h2>Selamat datang, Admin!</h2>
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
                    <th>Harga Pokok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result_barang)) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nama_barang"] . "</td>";
                    echo "<td>" . $row["harga"] . "</td>";
                    echo "<td>" . $row["stok"] . "</td>";
                    echo "<td>" . $row["harga_pokok"] . "</td>";
                    echo '<td>';
                    echo '<a href="edit_barang.php?id=' . $row["id"] . '" class="btn btn-primary">Edit</a>';
                    echo '<a href="admin.php?delete_barang=' . $row["id"] . '" class="btn btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus barang ini?\')">Hapus</a>';
                    echo '</td>';
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <h3>Tambah Barang Baru</h3>
        <form action="admin.php" method="POST">
            <div class="form-group">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga:</label>
                <input type="number" class="form-control" id="harga" name="harga" required>
            </div>
            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" class="form-control" id="stok" name="stok" required>
            </div>
            <div class="form-group">
                <label for="stok">Harga Pokok:</label>
                <input type="number" class="form-control" id="harga_pokok" name="harga_pokok" required>
            </div>
            <button type="submit" name="add_barang" class="btn btn-success">Tambah Barang</button>
        </form>

        <h3>Daftar Pengguna</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result_users)) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["username"] . "</td>";
                    echo "<td>" . $row["role"] . "</td>";
                    echo '<td>';
                    echo '<a href="edit_user.php?id=' . $row["id"] . '" class="btn btn-primary">Edit</a>';
                    echo '<a href="admin.php?delete_user=' . $row["id"] . '" class="btn btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus pengguna ini?\')">Hapus</a>';
                    echo '</td>';
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <h3>Tambah Pengguna Baru</h3>
        <form action="admin.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="add_user" class="btn btn-success">Tambah Pengguna</button>
        </form>
    </div>
    <div class="container">
        <h2>Halaman Admin - Daftar Transaksi</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Nama Pembeli</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT t.id AS transaksi_id, u.username AS nama_pembeli, b.nama_barang, t.jumlah, (t.jumlah * b.harga) AS total_harga
                    FROM transaksi t
                    INNER JOIN users u ON t.user_id = u.id
                    INNER JOIN barang b ON t.barang_id = b.id";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row["transaksi_id"] . "</td>";
                    echo "<td>" . $row["nama_pembeli"] . "</td>";
                    echo "<td>" . $row["nama_barang"] . "</td>";
                    echo "<td>" . $row["jumlah"] . "</td>";
                    echo "<td>" . 'Rp ' . number_format($row["total_harga"], 2) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="container">
        <h2>Laporan Laba Rugi</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Pendapatan</h5>
                        <?php
                            $sql_pendapatan = "SELECT SUM(harga * jumlah) AS total_pendapatan FROM transaksi 
                            INNER JOIN barang ON transaksi.barang_id = barang.id";
                            $result_pendapatan = mysqli_query($conn, $sql_pendapatan);
                            $row_pendapatan = mysqli_fetch_assoc($result_pendapatan);
                            $total_pendapatan = $row_pendapatan['total_pendapatan'];
                            
                            // Ambil total biaya (harga barang * stok)
                            $sql_biaya = "SELECT SUM(harga * stok) AS total_biaya FROM barang";
                            $result_biaya = mysqli_query($conn, $sql_biaya);
                            $row_biaya = mysqli_fetch_assoc($result_biaya);
                            $total_biaya = $row_biaya['total_biaya'];
                            
                            // Hitung laba rugi
                            $laba_rugi = $total_pendapatan - $total_biaya;
                        ?>
                        <p class="card-text"><?php echo 'Rp ' . number_format($total_pendapatan, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Biaya</h5>
                        <p class="card-text"><?php echo 'Rp ' . number_format($total_biaya, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Laba Rugi</h3>
        <div class="card">
            <div class="card-body">
                <p class="card-text"><?php echo 'Rp ' . number_format($laba_rugi, 2); ?></p>
            </div>
        </div>

        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
