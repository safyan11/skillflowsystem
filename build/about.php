<?php require_once "inc/header.php"; ?>

<?php require_once "inc/nav.php"; ?>
<body class="bg-[#FBFBFB]">






<!-- about us  -->
<section class="xl:px-20 lg:px-10 px-5 xl:py-20 py-10 ">
    <div class="flex justify-between gap-20 items-center"> 
    <div class="lg:w-1/2 w-full">
        <h1 class="text-4xl font-semibold">About Us</h1>
        <h2 class="text-2xl font-medium pt-2">Learn, Create, and Grow with Us</h2>
        <p class="2xl:text-lg text-base leading-7 text-[#666666] pt-6">At Learning, we believe in empowering individuals with the skills they need to succeed in the digital world. Whether you want to learn Web Development, explore Creative Designing, or master Digital Marketing, our expert led courses are designed for all levels—beginners to advanced learners.</p>
    </div>
    <div class="lg:w-1/2 w-full">
        <img src="./assets/img/aboutimg.avif" alt="">
    </div>
   </div>
   
</section>

<section class="xl:px-20 lg:px-10 px-5 xl:py-20 py-10 ">
    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-10">
        <div class="text-center bg-white p-6 rounded-lg shadow-md hover:bg-black hover:text-white transition duration-300 group">
            <i class="fa fa-pencil text-4xl"></i>
            <h2 class="xl:text-3xl text-2xl pt-2">Achievement</h2>
            <p class="2xl:text-lg lg:text-base text-sm leading-6 lg:leading-7 2xl:leading-8 text-[#666666] pt-2">We are proud of our achievements in the field of online learning. With over 5000+ satisfied students trained in web development, designing, and digital marketing, our platform has become a trusted name for learners across the globe.</p>
        </div>

        <div class="text-center bg-white p-6 rounded-lg shadow-md hover:bg-black transition duration-300 group hover:text-white">
            <i class="fa fa-book text-4xl"></i>
            <h2 class="xl:text-3xl text-2xl pt-2">Achievement</h2>
            <p class="2xl:text-lg lg:text-base text-sm leading-6 lg:leading-7 2xl:leading-8 text-[#666666] pt-2">Our vision is to empower individuals worldwide by providing accessible, high-quality digital education. We aim to break barriers of location and background, making advanced tech skills available to everyone.</p>
        </div>

        <div class="text-center bg-white p-6 rounded-lg shadow-md hover:bg-black transition duration-300 group hover:text-white">
            <i class="fa fa-globe text-4xl"></i>
            <h2 class="xl:text-3xl text-2xl pt-2">Achievement</h2>
            <p class="2xl:text-lg lg:text-base text-sm leading-6 lg:leading-7 2xl:leading-8 text-[#666666] pt-2">Our mission is to deliver practical, skill-based training in the fields of web development, creative designing, and digital marketing. We strive to create a supportive learning environment where students not only gain knowledge but also build careers.</p>
        </div>
    </div>
</section>
<!-- testimonials  -->
<section class="xl:py-20 py-10 xl:px-20 lg:px-10 px-5">
  <h2 class="text-4xl font-bold text-center text-black mb-12">Testimonials</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    
    <!-- Testimonial Card -->
    <div class="bg-white p-6 rounded-lg shadow-md hover:bg-black transition duration-300 group">
      <p class="text-[#666666] group-hover:text-[#666666] mb-6 text-sm">
        "Learn2Earn Hub made me a confident frontend developer. The instructors are amazing and the content is very practical!"
      </p>
      <div class="flex items-center space-x-4">
        <img src="https://via.placeholder.com/60" alt="User" class="w-14 h-14 rounded-full object-cover border-2 border-white">
        <div>
          <h4 class="text-lg font-semibold text-black group-hover:text-white">Mahmoud Mohamed</h4>
          <p class="text-sm text-gray-400 group-hover:text-[#666666]">Frontend Developer</p>
        </div>
      </div>
    </div>

    <!-- Repeat for other testimonials -->

    <div class="bg-white p-6 rounded-lg shadow-md hover:bg-black transition duration-300 group">
      <p class="text-[#666666] group-hover:text-[#666666] mb-6 text-sm">
        "The graphic designing course helped me build a strong portfolio. Highly recommend for creative learners!"
      </p>
      <div class="flex items-center space-x-4">
        <img src="https://via.placeholder.com/60" alt="User" class="w-14 h-14 rounded-full object-cover border-2 border-white">
        <div>
          <h4 class="text-lg font-semibold text-black group-hover:text-white">Sara Ahmed</h4>
          <p class="text-sm text-gray-400 group-hover:text-[#666666]">Graphic Designer</p>
        </div>
      </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md hover:bg-black transition duration-300 group">
      <p class="text-[#666666] group-hover:text-[#666666] mb-6 text-sm">
        "With Learn2Earn's digital marketing course, I grew my freelance career. Great mentorship and support!"
      </p>
      <div class="flex items-center space-x-4">
        <img src="https://via.placeholder.com/60" alt="User" class="w-14 h-14 rounded-full object-cover border-2 border-white">
        <div>
          <h4 class="text-lg font-semibold text-black group-hover:text-gray-300">Usman Raza</h4>
          <p class="text-sm text-gray-400 group-hover:text-gray-300">Digital Marketer</p>
        </div>
      </div>
    </div>

  </div>
</section>


<?php require_once "inc/footer.php"; ?>
</body>
</html>