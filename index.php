<?php 
include 'open/Start.php';
error_reporting(E_ALL & ~E_NOTICE);     //显示E_NOTICE之外的所有错误
// 开启$_SESSION
session_start();
// 开启自动加载
Start::autoload();
// 开启路由
Start::rooter();