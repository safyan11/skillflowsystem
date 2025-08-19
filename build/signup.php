<?php include("inc/header.php"); ?>

<body class="bg-[#fbfbfb]">
<?php require_once "inc/nav.php"; ?>
<section class="2xl:px-16 xl:px-16 2xl:py-20 xl:py-20 lg:px-10 lg:py-16">
    <div class="pop flex flex-col lg:flex-row justify-between items-center 2xl:gap-24 bg-white dark:border-black border md:rounded-3xl 2xl:px-32 md:px-16 w-full">
        <div class="2xl:py-56 xl:py-36 md:w-3/5 p-5">
            <p class="2xl:text-2xl xl:text-xl md:text-sm font-semibold 2xl:pl-20 text-center lg:text-left text-xs text-black">Create Your Account and Unlock Exciting Features</p>
        </div>
        <div class="lg:w-2/5 px-5 lg:px-0">
            <!-- FORM STARTS -->
            <form action="#" method="POST" class="md:py-20 py-10">
                <div class="bg-white shadow-xl border border-black rounded-3xl 2xl:px-10 py-10 md:px-5 md:py-20">
                    <div class="text-center pt-5 lg:pt-0 text-black">
                        <h2 class="2xl:text-4xl md:text-lg font-bold">Join Us Today</h2>
                        <p class="2xl:text-sm md:text-xs font-medium 2xl:pt-2 md pt-1 text-xs text-black">Already have an account? <a href="./login.php"><span class="text-sm font-bold hover:underline">Login</span></a></p>
                    </div>

                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        // Get form data
                        $name = trim($_POST["name"]);
                        $email = trim($_POST["email"]);
                        $password = trim($_POST["password"]);

                        // Hash the password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // Insert into database
                        $sql = "INSERT INTO users (name, email, password, role, verify_status) VALUES (?, ?, ?, 'student', 'pending')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $name, $email, $hashed_password);

                        if ($stmt->execute()) {
                            echo "<p class='text-green-600 text-sm text-center pt-4'>Account created successfully!</p>";
                        } else {
                            echo "<p class='text-red-600 text-sm text-center pt-4'>Error: " . $stmt->error . "</p>";
                        }

                        $stmt->close();
                    }
                    ?>

                    <div class="space-y-4 2xl:pt-20 md:pt-10 pt-5 px-2 md:px-0">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-black">Name</label>
                            <input name="name" required class="border border-gray-200 text-gray-900 sm:text-sm rounded-lg block w-full 2xl:px-3 2xl:py-4 md:px-2 md:py-2 px-1 py-2" placeholder="Enter your name" type="text">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-black">Email</label>
                            <input name="email" required type="email" class="border border-gray-200 text-gray-900 sm:text-sm rounded-lg block w-full 2xl:px-3 2xl:py-4 md:px-2 md:py-2 px-1 py-2" placeholder="Enter your email">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-black">Password</label>
                            <input name="password" required type="password" class="border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full 2xl:px-3 2xl:py-4 md:px-2 md:py-2 px-1 py-2" placeholder="Enter your password">
                        </div>

                        <div>
                            <button type="submit" class="text-base font-semibold 2xl:py-3 md:py-1 py-1 bg-white hover:bg-black text-black w-full hover:text-white border rounded-lg mt-5">Sign Up</button>
                        </div>

                        <div class="flex items-center gap-3">
                            <input type="checkbox" class="w-5 h-5" required>
                            <p class="text-[10px] text-black">By clicking Create account, I agree to the <a href="#" class="font-semibold underline">Terms</a>.</p>
                        </div>
                    </div>
                </div>
            </form>
            <!-- FORM ENDS -->
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
