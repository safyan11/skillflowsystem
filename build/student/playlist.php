<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;

// Course Extraction Orchestration
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $courseId = intval($_GET['id']);
} else {
    $result = $conn->query("SELECT id FROM courses ORDER BY id ASC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $courseId = intval($row['id']);
    } else {
        die("<div class='p-20 text-center font-black text-slate-400'>CRITICAL: ACCESS DENIED - CURRICULUM VACANT</div>");
    }
}

// Fetch Core Course Schema
$sql = "SELECT * FROM courses WHERE id = $courseId";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
    die("<div class='p-20 text-center font-black text-rose-500'>CRITICAL: MODULE NOT FOUND</div>");
}
$course = $result->fetch_assoc();

// Progress Synchronization
$enrollment = $conn->query("SELECT * FROM course_enrollments WHERE student_id = $student_id AND course_id = $courseId")->fetch_assoc();
if (!$enrollment) {
    $conn->query("INSERT INTO course_enrollments (student_id, course_id, progress_percentage) VALUES ($student_id, $courseId, 0)");
    $progress = 0;
} else {
    $progress = (int)$enrollment['progress_percentage'];
}

// Curriculum Inventory
$materials = $conn->query("SELECT * FROM materials WHERE course_id = $courseId ORDER BY uploaded_at ASC");
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Course Command Center -->
            <div class="lg:w-2/3 space-y-8">
                <div class="bg-white rounded-[3rem] overflow-hidden border border-slate-100 shadow-sm transition hover:shadow-2xl duration-700 group">
                    <!-- Premium Hero Section -->
                    <div class="relative h-[25rem] overflow-hidden">
                        <img src="../admin/<?= htmlspecialchars($course['thumbnail']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-1000">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                        <div class="absolute bottom-10 left-10 right-10">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="px-4 py-1.5 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest italic shadow-lg">Active Module</span>
                                <span class="px-4 py-1.5 bg-white/10 backdrop-blur-md text-white rounded-full text-[10px] font-black uppercase tracking-widest border border-white/20 italic">Curriculum V2.1</span>
                            </div>
                            <h1 class="text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight italic"><?= htmlspecialchars($course['title']) ?></h1>
                        </div>
                    </div>

                    <div class="p-10">
                        <!-- Progress Diagnostics -->
                        <div class="bg-slate-50 rounded-[2rem] p-8 mb-10 border border-slate-100 shadow-inner">
                            <div class="flex justify-between items-center mb-4 px-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-8 bg-blue-600 rounded-full"></div>
                                    <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Mastery Orchestration</span>
                                </div>
                                <span class="text-sm font-black text-blue-600 italic"><?= $progress ?>% <span class="text-slate-300">/ SYNC COMPLETE</span></span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-blue-600 h-full rounded-full shadow-lg shadow-blue-500/20 transition-all duration-1000" style="width: <?= $progress ?>%"></div>
                            </div>
                        </div>

                        <!-- Identity & Metadata -->
                        <div class="flex items-center gap-6 mb-10 pb-8 border-b border-slate-50">
                            <div class="relative">
                                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 border-2 border-white shadow-xl">
                                    <i class="fa-solid fa-user-gear text-2xl"></i>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 rounded-full border-4 border-white"></div>
                            </div>
                            <div>
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 italic">Faculty Architect</p>
                                <h3 class="text-xl font-black text-slate-800 leading-none"><?= htmlspecialchars($course['instructor_name']) ?></h3>
                                <p class="text-sm font-bold text-blue-600 mt-1 uppercase italic tracking-tighter"><?= htmlspecialchars($course['instructor_designation']) ?></p>
                            </div>
                        </div>

                        <!-- Technical Synopsis -->
                        <div class="prose prose-slate max-w-none">
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-3 italic">
                                Technical Synopsis
                                <span class="flex-1 h-[1px] bg-slate-100"></span>
                            </h4>
                            <p class="text-slate-600 font-medium leading-relaxed mb-8 italic">
                                <?= nl2br(htmlspecialchars($course['full_description'])) ?>
                            </p>
                        </div>

                        <!-- Learning Objectives -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
                            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl">
                                <div class="absolute top-0 right-0 p-8 opacity-5">
                                    <i class="fa-solid fa-rocket text-8xl"></i>
                                </div>
                                <h4 class="text-lg font-black mb-6 italic flex items-center gap-3">
                                    <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                                    Skill Orbit
                                </h4>
                                <ul class="space-y-4">
                                    <?php
                                    $lessons = explode("\n", $course['what_you_will_learn']);
                                    foreach ($lessons as $lesson): if(trim($lesson)): ?>
                                        <li class="flex items-start gap-4 text-xs font-bold text-slate-300 leading-normal italic">
                                            <i class="fa-solid fa-circle-check text-blue-400 mt-0.5"></i>
                                            <?= htmlspecialchars(trim($lesson)) ?>
                                        </li>
                                    <?php endif; endforeach; ?>
                                </ul>
                            </div>

                            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm flex flex-col justify-center">
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-2xl shadow-xl shadow-blue-50">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                    </div>
                                    <h4 class="text-xl font-black text-slate-800 mb-2 italic tracking-tight">Certification Goal</h4>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Achieve 80% or higher to unlock</p>
                                    <button class="mt-8 w-full bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[10px] py-4 rounded-2xl hover:bg-blue-600 transition duration-500 shadow-lg shadow-slate-200">
                                        Check Eligibility
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Curriculum Sidebar -->
            <aside class="lg:w-1/3 space-y-6">
                <div class="sticky top-24">
                    <div class="flex items-center justify-between mb-8 px-4">
                        <h2 class="text-2xl font-black tracking-tight italic">Curriculum</h2>
                        <span class="px-3 py-1 bg-slate-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest italic"><?= $materials->num_rows ?> Assets</span>
                    </div>

                    <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-4 custom-scrollbar">
                        <?php if ($materials && $materials->num_rows > 0): ?>
                            <?php while ($mat = $materials->fetch_assoc()): ?>
                                <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-xl hover:-translate-x-1 hover:border-blue-100 transition duration-500 group">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition duration-500 shadow-inner">
                                        <i class="fa-solid fa-file-invoice text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-black text-slate-800 mb-1 group-hover:text-blue-600 transition italic"><?= htmlspecialchars($mat['title']) ?></h4>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 italic"><?= number_format($mat['filesize']/1024, 0) ?> KB ARCHIVE</span>
                                            <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                            <span class="text-[9px] font-black uppercase tracking-widest text-blue-500 italic">Core Asset</span>
                                        </div>
                                    </div>
                                    <a href="../teacher/uploads/<?= urlencode($mat['filename']) ?>" download class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition duration-500">
                                        <i class="fa-solid fa-cloud-arrow-down"></i>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="p-10 text-center bg-white rounded-[2.5rem] border border-dashed border-slate-100">
                                <i class="fa-solid fa-inbox text-slate-200 text-4xl mb-4"></i>
                                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Curriculum Payload Pending</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Supplemental Shortcuts -->
                    <div class="mt-8 p-8 bg-blue-600 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:rotate-12 transition duration-500">
                            <i class="fa-solid fa-circle-question text-8xl"></i>
                        </div>
                        <h4 class="text-xl font-black mb-4 italic">Need Support?</h4>
                        <p class="text-xs font-bold text-blue-100 italic mb-6">Connect with faculty architects for immediate diagnostic assistance.</p>
                        <a href="complains.php" class="inline-block bg-white text-blue-600 font-black uppercase tracking-[0.2em] text-[10px] px-6 py-3 rounded-xl hover:bg-slate-900 hover:text-white transition duration-500">
                            Open Ticket
                        </a>
                    </div>
                </div>
            </aside>
        </div>
      </main>
    </div>
  </div>

  <style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
  </style>
  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>html>
