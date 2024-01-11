<?php
session_start();
// koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "db_barang");

//menambah barang baru
if (isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($conn, "insert into stock(namabarang, deskripsi, stock) values('$namabarang','$deskripsi','$stock')");
    if ($addtotable) {
        header('location:index.php');
    } else {
        echo 'gagal';
        header('location:index.php');
    }
}

// menambah barang masuk
if (isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;

    $addtomasuk = mysqli_query($conn, "insert into masuk (idbarang,keterangan,qty) values('$barangnya','$penerima','$qty')");
    $updatestockmasuk = mysqli_query($conn, "update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if ($addtomasuk && $updatestockmasuk) {
        header('location:masuk.php');
    } else {
        echo 'gagal';
        header('location:masuk.php');
    }
}

// menambah barang keluar
if (isset($_POST['barangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];

    if ($stocksekarang >= $qty) {
        //barang cukup
        $tambahkanstocksekarangdenganquantity = $stocksekarang - $qty;

        $addtokeluar = mysqli_query($conn, "insert into keluar (idbarang,penerima,qty) values('$barangnya','$penerima','$qty')");
        $updatestockmasuk = mysqli_query($conn, "update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
        if ($addtokeluar && $updatestockmasuk) {
            header('location:keluar.php');
        } else {
            echo 'gagal';
            header('location:keluar.php');
        }
    } else {
        //barang tidak cukup
        echo '
        <script>
            alert("Stock saat ini tidak mencukupi");
            window.location.href="keluar.php";
        </script>
        ';
    }
}

// update info barang
if (isset($_POST['updatebarang'])) {
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    $update = mysqli_query($conn, "update stock set namabarang='$namabarang', deskripsi='$deskripsi' where idbarang='$idb'");

    if ($update) {
        header('location:index.php');
    } else {
        echo 'gagal';
        header('location:index.php');
    }
}

// Hapus Barang dari stock
if (isset($_POST['hapusbarang'])) {
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from stock where idbarang='$idb'");
    $update = mysqli_query($conn, "update stock set namabarang='$namabarang', deskripsi='$deskripsi' where idbarang='$idb'");

    if ($update) {
        header('location:index.php');
    } else {
        echo 'gagal';
        header('location:index.php');
    }
}


// Mengubah Data Barang Masuk
if (isset($_POST['updatebm'])) {
    $idb = $_POST['idb'];
    $idm = $_POST['idmasuk'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrng = $stocknya['stock'];

    $qtyskrng = mysqli_query($conn, "select * from masuk where idmasuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrng);
    $qtyskrng = $qtynya['qty'];

    if ($qty > $qtyskrng) {
        $selisih = $qty - $qtyskrng;
        $kurangi = $stockskrng + $selisih;
        $kurangistock = mysqli_query($conn, "update stock set stock ='$kurangi' where idbarang='$idb'");
        $update = mysqli_query($conn, "update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
        if ($kurangistock && $update) {
            header('location:masuk.php');
        } else {
            echo 'gagal';
            header('loaction:masuk.php');
        }
    } else {
        $selisih = $qtyskrng - $qty;
        $kurangi = $stockskrng - $selisih;
        $kurangistock = mysqli_query($conn, "update stock set stock ='$kurangi' where idbarang='$idb'");
        $update = mysqli_query($conn, "update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
        if ($kurangistock && $update) {
            header('location:masuk.php');
        } else {
            echo 'gagal';
            header('loaction:masuk.php');
        }
    }
}

// menghapus barang masuk
if (isset($_POST['hapusbm'])) {
    $idm = $_POST['idmasuk'];
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];

    $getdatastock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock - $qty;
    $update = mysqli_query($conn, "update stock set stock='$selisih' where idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "delete from masuk where idmasuk='$idm'");

    if ($update && $hapusdata) {
        header('location:masuk.php');
    } else {
        echo 'gagal';
        header('location:masuk.php');
    }
}

// mengubah data barang keluar
if (isset($_POST['updatebk'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idkeluar'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrng = $stocknya['stock'];

    $qtyskrng = mysqli_query($conn, "select * from keluar where idkeluar='$idk'");
    $qtynya = mysqli_fetch_array($qtyskrng);
    $qtyskrng = $qtynya['qty'];

    if ($qty > $qtyskrng) {
        $selisih = $qty - $qtyskrng;
        $kurangi = $stockskrng - $selisih;
        $kurangistock = mysqli_query($conn, "update stock set stock ='$kurangi' where idbarang='$idb'");
        $update = mysqli_query($conn, "update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
        if ($kurangistock && $update) {
            header('location:keluar.php');
        } else {
            echo 'gagal';
            header('loaction:keluar.php');
        }
    } else {
        $selisih = $qtyskrng - $qty;
        $kurangi = $stockskrng + $selisih;
        $kurangistock = mysqli_query($conn, "update stock set stock ='$kurangi' where idbarang='$idb'");
        $update = mysqli_query($conn, "update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
        if ($kurangistock && $update) {
            header('location:keluar.php');
        } else {
            echo 'gagal';
            header('loaction:keluar.php');
        }
    }
}

// menghapus barang keluar
if (isset($_POST['hapusbk'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idk = $_POST['idkeluar'];

    $getdatastock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock + $qty;
    $update = mysqli_query($conn, "update stock set stock='$selisih' where idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "delete from keluar where idkeluar='$idk'");

    if ($update && $hapusdata) {
        header('location:keluar.php');
    } else {
        echo 'gagal';
        header('location:keluar.php');
    }
}

// menambah admin baru
if (isset($_POST['addadmin'])) {
    $usrnm = $_POST['username'];
    $password = $_POST['password'];

    $queryinsert = mysqli_query($conn, "insert into login (username, password) values ('$usrnm','$password')");

    if ($queryinsert) {
        header('location:admin.php');
    } else {
        header('location:admin.php');
    }
}

// edit data admin
if (isset($_POST['updateadmin'])) {
    $userbaru = $_POST['username'];
    $pwbaru = $_POST['password'];
    $idnya = $_POST['iduser'];

    $queryupdate = mysqli_query($conn, "update login set username='$userbaru', password='$pwbaru' where iduser='$idnya'");

    if ($queryupdate) {
        header('location:admin.php');
    } else {
        header('location:admin.php');
    }
}

// hapus data admin
if (isset($_POST['hapusadmin'])) {
    $idu = $_POST['iduser'];

    $querydelete = mysqli_query($conn, "delete from login where iduser='$idu'");
    if ($querydelete) {
        header('location:admin.php');
    } else {
        header('location:admin.php');
    }
}
?>