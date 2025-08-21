<?php require_once "inc/header.php"; ?>
<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
 <?php require_once "inc/sidebar.php"; ?>
<?php
require_once "../inc/db.php";
// Fetch all materials to display
$materials = [];
$sql = "SELECT * FROM materials ORDER BY uploaded_at DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }
}
?>
    <!-- Overlay for mobile when sidebar open -->
   
 
</body>
</html>
