<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

$events = [];
$rows = mysqli_query($conn, "
    SELECT pr.name AS resource, pwo.order_code, pra.*
    FROM production_resource_assignments pra
    JOIN production_resources pr ON pra.resource_id = pr.id
    JOIN production_work_orders pwo ON pra.work_order_id = pwo.id
");

while ($r = mysqli_fetch_assoc($rows)) {
    $events[] = [
        'title' => $r['order_code'] . ' - ' . $r['resource'],
        'start' => $r['assigned_start'],
        'end' => $r['assigned_end'],
        'description' => $r['remarks'],
    ];
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Schedule</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="content-wrapper">
                <section class="content-header"><h1>Job Scheduling Calendar</h1></section>
                <section class="content">
                    <div id="calendar"></div>
                </section>
            </div>

            <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const calendarEl = document.getElementById('calendar');
                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridWeek',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'timeGridWeek,timeGridDay'
                        },
                        events: <?= json_encode($events) ?>,
                        eventClick: function(info) {
                            alert(info.event.title + "\n" + info.event.extendedProps.description);
                        }
                    });
                    calendar.render();
                });
            </script>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
