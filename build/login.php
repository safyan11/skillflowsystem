<?php
error_reporting(0);
session_start();
// $_SESSION['user_id'] = $row['id'];
// $_SESSION['user_name'] = $row['name'];
// $_SESSION['user_email'] = $row['email'];
// $_SESSION['user_role'] = $row['role']; 
// Adjust this path if needed
require_once "inc/header.php";
$loginError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]);

    if (empty($email) || empty($password) || empty($role)) {
        $loginError = "Please fill all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check if account is approved
            if ($user['verify_status'] !== 'approved') {
                $loginError = "Your account is not approved yet.";
            }
            // Check password
            elseif (!password_verify($password, $user['password'])) {
                $loginError = "Invalid password.";
            }
            // Success
            else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($role === 'admin') {
                    header("Location: admin/admindashboard.php");
                } elseif ($role === 'teacher') {
                    header("Location: teacher/teacherdashboard.php");
                } else {
                    header("Location: student/studentdashboard.php");
                }
                exit();
            }
        } else {
            $loginError = "No user found with provided credentials.";
        }
        $stmt->close();
    }
}

$conn->close();
?>


<body class="bg-[#fbfbfb]">
<?php require_once "inc/nav.php"; ?>
    <section class="2xl:px-16 xl:px-16 2xl:py-20 xl:py-20 lg:px-10 lg:py-16">
        <div class="pop flex flex-col lg:flex-row justify-between items-center 2xl:gap-24 bg-white dark:border-black border md:rounded-3xl 2xl:px-32 md:px-16 w-full">
            <div class="2xl:py-56 xl:py-36 md:w-3/5 p-5">
                <p class="2xl:text-2xl xl:text-xl md:text-sm font-semibold 2xl:pl-20 text-center lg:text-left text-xs text-black">Log in to Your Account for Seamless Access</p>
            </div>
            <div class="lg:w-2/5 px-5 lg:px-0">
                <form action="" method="POST" class="md:py-20 py-10">
                    <div class="bg-white shadow-xl border border-black rounded-3xl 2xl:px-10 py-10 md:px-5 md:py-20">
                        <div class="text-center pt-5 lg:pt-0 text-black">
                            <h2 class="2xl:text-4xl md:text-lg font-bold">Welcome Back!</h2>
                            <p class="2xl:text-sm md:text-xs font-medium 2xl:pt-2 md pt-1 text-xs text-black">Don’t have an account? <a href="./signup.php"><span class="text-sm font-bold hover:underline">Sign Up</span></a></p>
                        </div>
                        <?php if (!empty($loginError)): ?>
                            <p style="color: red; text-align: center; margin-top: 10px; font-size: 14px;"><?php echo $loginError; ?></p>
                        <?php endif; ?>
                        <div class="space-y-4 2xl:pt-20 md:pt-10 pt-5 px-2 md:px-0">
                              <div class="space-y-2">
                                <label class=" mb-2 text-sm font-semibold text-black">Email</label>
                                <input name="email" class="focus:bg-white focus:text-black border border-gray-200 text-gray-900 sm:text-sm rounded-lg block w-full 2xl:px-3 2xl:py-4 md:px-2 md:py-2 px-1 py-2" placeholder="Enter your email" id="name" type="text" required>
                              </div>
            <div class="space-y-2" x-data="{ show: false }">
              <label class="mb-2 text-sm font-semibold text-black">Password</label>
              <div class="relative">
                  <input name="password" :type="show ? 'text' : 'password'" class="focus:text-black border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full 2xl:px-3 2xl:py-4 md:px-2 md:py-2 px-1 py-2 pr-10" placeholder="Enter your password" id="password" required>
                  <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i :class="show ? 'fa fa-eye' : 'fa fa-eye-slash'" class="text-gray-500 dark:text-gray-600"></i>
                  </button>
              </div>
          </div>
          <div class="flex flex-col">
              <label class="mb-2 text-sm font-semibold text-black">User Role</label>
              <select name="role" class="focus:bg-white focus:text-black border border-gray-200 text-gray-900 sm:text-sm rounded-lg block w-full 2xl:px-3 2xl:py-4 md:px-2 md:py-2 px-1 py-2" required>
                  <option value="">Select your role</option>
                  <option value="admin">Admin</option>
                  <option value="teacher">Teacher</option>
                  <option value="student">Student</option>
              </select>
          </div>
          <div>
              <button type="submit" class="text-base font-semibold 2xl:py-3 md:py-1 py-1 bg-white hover:bg-black text-black w-full hover:text-white border rounded-lg mt-5">Log in</button>
          </div>
          <a href="./forgetpassword.html">
              <p class="md:text-xs text-[8px] pb-5 md:pb-0 text-black hover:underline pt-2 text-end">Forget Password</p>
          </a>
          <div class="flex items-center gap-3">
              <input class="-mt-6 md:-mt-0 w-5 h-5 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 bg-gray-700 border-gray-600 focus:ring-primary-600 ring-offset-gray-800 accent-black" type="checkbox" aria-describedby="terms" id="terms">
              <p class="md:text-xs text-[8px] pb-5 md:pb-0 text-black">By clicking Create account, I agree that I have read and accepted the <a href="" class="font-semibold hover:underline text-black">Terms of Use</a> and <a href="" class="font-semibold text-black hover:underline">Privacy Policy.</a></p>
          </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
