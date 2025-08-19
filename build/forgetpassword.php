<?php require_once "inc/header.php"; ?>



<body class="bg-[#fbfbfb]">

    <section class="2xl:px-16 xl:px-16 2xl:py-20 xl:py-20 lg:px-10 lg:py-16">
        <div class="pop flex flex-col lg:flex-row justify-between items-center 2xl:gap-24 bg-white dark:border-black border md:rounded-3xl 2xl:px-32 md:px-16 w-full">
            <div class="2xl:py-56 xl:py-36 md:w-3/5 p-5">
              <!-- <img src="assets/img/blacklogo.png" class="dark:block hidden 2xl:h-[322px] xl:h-[222px] lg:h-[122px] lg:w-[332px] xl:w-[532px] 2xl:w-[732px]" alt="">
              <img src="assets/img/risslogo.png.png" class="dark:hidden block 2xl:h-[322px] xl:h-[222px] lg:h-[122px] lg:w-[332px] xl:w-[532px] 2xl:w-[732px]" alt=""> -->
                <p class=" 2xl:text-2xl xl:text-xl md:text-sm font-semibold 2xl:pl-20 text-center lg:text-left text-xs text-black">Log in to Your Account for Seamless Access</p>
            </div>
            <div class="lg:w-2/5 px-5 lg:px-0">

                <form action="" method="" class="md:py-20 py-10">
                    <div class="bg-white shadow-xl border border-black rounded-3xl 2xl:px-10 py-10 md:px-5 md:py-20">
                        <div class="text-center pt-5 lg:pt-0 text-black">
                            <h2 class="2xl:text-4xl md:text-lg font-bold">Reset Your Password</h2>
                            <p class="2xl:text-sm md:text-xs font-medium 2xl:pt-2 md pt-1 text-xs text-black"> Recover Access to Your Account by Resetting Your Password </p>
                        </div>
                        <div class="space-y-4 2xl:pt-20 md:pt-10 pt-5 px-2 md:px-0">
                            
                                <div class="space-y-2" x-data="{ show: false }">
              <label class="mb-2 text-sm font-semibold text-black">New Password</label>
              <div class="relative">
                  <input :type="show ? 'text' : 'password'" class="focus:text-black dark:text-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full 2xl:px-3 2xl:py-4 md:px-2 md:py-2 px-1 py-2 pr-10" placeholder="Enter new password" id="password">
                  <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i :class="show ? 'fa fa-eye' : 'fa fa-eye-slash'" class="text-gray-500 dark:text-gray-600"></i>
                  </button>
              </div>
          </div>
                             
            <!-- Password Field with Show/Hide -->
            <div class="space-y-2" x-data="{ show: false }">
              <label class="mb-2 text-sm font-semibold text-black">Confirm Password</label>
              <div class="relative">
                  <input :type="show ? 'text' : 'password'" class="focus:text-black dark:text-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full 2xl:px-3 2xl:py-4 md:px-2 md:py-2 px-1 py-2 pr-10" placeholder="Confirm password" id="password">
                  <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-3">
                      <i :class="show ? 'fa fa-eye' : 'fa fa-eye-slash'" class="text-gray-500 dark:text-gray-600"></i>
                  </button>
              </div>
          </div>
                              <div>
                                <a href="./login.html">
                              <button class="text-base font-semibold 2xl:py-3 md:py-1 py-1 bg-white hover:bg-black text-black w-full hover:text-white border rounded-lg mt-5">Update Password</button>
                            </a>
                            </div>
                           
                            <div class="flex items-center gap-3">
                              <input class="-mt-6 md:-mt-0 w-5 h-5 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 bg-gray-700 border-gray-600 focus:ring-primary-600 ring-offset-gray-800 accent-black" type="checkbox" aria-describedby="terms" id="terms">
                              <p class="md:text-xs text-[8px] pb-5 md:pb-0 text-black">By clicking Create account, I agree that I have read and accepted the <a href="" class="font-semibold hover:underline text-black">Terms of Use</a> and <a href="" class="font-semibold text-black hover:underline">Privacy Policy.</a> </p>
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