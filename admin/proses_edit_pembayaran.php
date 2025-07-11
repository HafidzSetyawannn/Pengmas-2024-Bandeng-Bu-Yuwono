<?php
include '../dbconnect.php';

if(isset($_POST['editmethod']))
{
    $edit_id = $_POST['edit_id'];
    $edit_metode = $_POST['edit_metode'];
    $edit_norek = $_POST['edit_norek'];
    $edit_an = $_POST['edit_an'];
    $edit_logo = $_POST['edit_logo'];

    $editmet = mysqli_query($conn, "UPDATE pembayaran SET metode='$edit_metode', norek='$edit_norek', an='$edit_an', logo='$edit_logo' WHERE no='$edit_id'");

    if ($editmet) {
        echo "<meta http-equiv='refresh' content='1; url= pembayaran.php'/>";
    } else {
        echo "<meta http-equiv='refresh' content='1; url= pembayaran.php'/>";
    }
}
?>
