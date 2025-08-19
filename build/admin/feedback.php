<?php require_once "inc/header.php"; ?>
<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
  <?php require_once "inc/sidebar.php"; ?>

    <!-- Overlay for mobile when sidebar open -->
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

    <!-- Main content area -->
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <!-- top bar -->
  
 <?php require_once "inc/topbar.php"; ?>
 
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Review Feedback</h1>

    <!-- Expandable Comment Card -->
    <div class="space-y-4">

     <?php

require '../inc/db.php';
// Fetch feedback only from feedback table
$sql = "SELECT * FROM feedback ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($sql);

function renderStars($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    $starsHtml = str_repeat('★', $fullStars);
    if ($halfStar) $starsHtml .= '☆'; // or half star
    $starsHtml .= str_repeat('☆', $emptyStars);

    return $starsHtml;
}

if ($result && $result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        $ratingStars = renderStars($row['rating']);
?>
    <div class="bg-white rounded-2xl shadow group transition-all duration-300 overflow-hidden p-4 hover:p-6">
      <div class="flex justify-between items-start">
        <div class="flex gap-4">
          <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center font-semibold text-white">
            <?= htmlspecialchars($row['user_id']) ?>
          </div>
          <div>
            <p class="font-semibold">User ID: <?= htmlspecialchars($row['user_id']) ?></p>
            <p class="text-sm text-gray-700">Course ID: <?= htmlspecialchars($row['course_id']) ?></p>
          </div>
        </div>
        <p class="text-sm text-gray-500 whitespace-nowrap"><?= date('j M Y', strtotime($row['created_at'])) ?></p>
      </div>

      <!-- Star Rating -->
      <div class="text-yellow-500 text-lg mb-2"><?= $ratingStars ?></div>

      <div class="mt-4 max-h-0 opacity-0 group-hover:max-h-[500px] group-hover:opacity-100 transition-all duration-500 text-sm text-gray-700">
        <p><?= nl2br(htmlspecialchars($row['comments'])) ?></p>
      </div>
    </div>
<?php
    endwhile;
else:
    echo "<p>No feedback available.</p>";
endif;

$conn->close();
?>





    </div>
  </div>


  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
