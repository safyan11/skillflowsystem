<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 

// DELETE complaint
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM complaints WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Complaint deleted successfully');window.location='complaints.php';</script>";
    } else {
        echo "<script>alert('Error deleting complaint');</script>";
    }
    $stmt->close();
}

// FETCH complaints
$result = $conn->query("SELECT c.*, u.name AS student_name 
                        FROM complaints c
                        LEFT JOIN users u ON c.student_id = u.id
                        ORDER BY c.created_at DESC");
?>
<body class="bg-gray-50 font-sans antialiased">
<div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
        <?php require_once "inc/topbar.php"; ?>
        <div class="p-6">
            <h2 class="text-2xl font-semibold mb-4">Complaints</h2>

            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4"><?= $row['id'] ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['student_name'] ?? 'Unknown') ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['subject']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['message']) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded 
                                            <?= $row['status'] == 'resolved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
                                    <td class="px-6 py-4 flex gap-2 justify-center">
                                        <a href="complaint_edit.php?id=<?= $row['id'] ?>" 
                                           class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">Edit</a>
                                        <a href="?delete_id=<?= $row['id'] ?>" 
                                           onclick="return confirm('Are you sure you want to delete this complaint?')" 
                                           class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No complaints found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
