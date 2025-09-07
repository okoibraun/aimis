<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <a href="#" class="navbar-brand">
        <span class="brand-text font-weight-light">SRMS</span>
    </a>
</nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">Admin</a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="./index.php" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="payroll/add_employee.php" class="nav-link">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>Add Employee</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="payroll/list_employees.php" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>List Employees</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="payroll/generate_pay_slips.php" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>Generate Pay Slips</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="payroll/payroll_reports.php" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Payroll Reports</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="payroll/payroll_accruals.php" class="nav-link">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Payroll Accruals</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Dashboard</h1>
        </div>
    </div> -->
    <div class="content">
        <div class="container-fluid">
