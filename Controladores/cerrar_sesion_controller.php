<?php
session_start();
session_destroy();
header("Location: ../Interfaces/Interfazlogin.php");
