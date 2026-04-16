<?php
require_once "inc/header.php";
require_once "../inc/db.php";

// Stats extraction
$total_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_teachers = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='teacher'")->fetch_assoc()['c'];
$total_students = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student'")->fetch_assoc()['c'];
$total_admins = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='admin'")->fetch_assoc()['c'];

$pending_complaints = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE status='pending'")->fetch_assoc()['c'];
$total_certificates = $conn->query("SELECT COUNT(*) as c FROM certificates")->fetch_assoc()['c'];

// Chart Data
$roleData = [$total_admins, $total_teachers, $total_students];

$visitsLabels = [];
$visitsValues = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $visitsLabels[] = date('D', strtotime($d));
    $v = $conn->query("SELECT visit_count FROM visit_statistics WHERE visit_date = '$d'")->fetch_assoc()['visit_count'] ?? 0;
    $visitsValues[] = $v;
}
?>

<body class="bg-slate-50 relative before:fixed before:inset-0 before:-z-10 before:w-full before:h-full before:bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] before:from-blue-100 before:via-white before:to-emerald-50">
    <div class="flex">
        <?php require_once "inc/sidebar.php"; ?>
        <div class="flex-1">
            <?php require_once "inc/topbar.php"; ?>
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-8">Dashboard Overview</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="glass-card stat-card p-8 rounded-[1.5rem]">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Total Users</p>
                        <h3 class="text-3xl font-bold"><?= $total_users ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem]">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Pending Complaints</p>
                        <h3 class="text-3xl font-bold"><?= $pending_complaints ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem]">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Certificates Issued</p>
                        <h3 class="text-3xl font-bold"><?= $total_certificates ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem]">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Total Teachers</p>
                        <h3 class="text-3xl font-bold"><?= $total_teachers ?></h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="glass-card stat-card p-8 rounded-[1.5rem]">
                        <h2 class="text-xl font-bold mb-6">Visit Statistics</h2>
                        <canvas id="visitsChart" height="200"></canvas>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem]">
                        <h2 class="text-xl font-bold mb-6">User Roles</h2>
                        <canvas id="rolesChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Visits Chart
        const ctxVisits = document.getElementById('visitsChart').getContext('2d');
        new Chart(ctxVisits, {
            type: 'line',
            data: {
                labels: <?= json_encode($visitsLabels) ?>,
                datasets: [{
                    label: 'Daily Visits',
                    data: <?= json_encode($visitsValues) ?>,
                    borderColor: '#000',
                    backgroundColor: 'rgba(0, 0, 0, 0.1)',
                    fill: true,
                    tension: 0.1
                }]
            }
        });

        // Roles Chart
        const ctxRoles = document.getElementById('rolesChart').getContext('2d');
        new Chart(ctxRoles, {
            type: 'pie',
            data: {
                labels: ['Admins', 'Teachers', 'Students'],
                datasets: [{
                    data: <?= json_encode($roleData) ?>,
                    backgroundColor: ['#000', '#4B5563', '#9CA3AF']
                }]
            }
        });
    </script>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
