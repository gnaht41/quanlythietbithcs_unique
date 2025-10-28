<?php
session_start();

include "header.php";
include "left.php";

if (isset($_GET['act'])) {
    $act = $_GET['act'];
    switch ($act) {
        case 'dangnhap':
            include "dangnhap.php";
            break;
    }
} else {
    include "dangnhap.php";
}

include "footer.php";
