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

   
<div class=" p-4">
  <div class="bg-white rounded-2xl overflow-hidden shadow-lg flex flex-col md:flex-row h-[90vh]">

    <!-- Left Sidebar -->
    <div class="w-full md:w-1/3 border-r p-4 space-y-4 overflow-y-auto">
      <h2 class="text-xl font-bold">Live Chat</h2>

      <!-- Active Chat -->
      <div class="flex items-center space-x-4 p-3 bg-gray-100 rounded-lg">
        <img src="https://i.pravatar.cc/50?img=32" class="w-10 h-10 rounded-full" />
        <div>
          <div class="font-semibold">Christopher Campbell</div>
          <div class="text-xs text-gray-500">Last seen 02:55 pm</div>
        </div>
      </div>

      <!-- Contacts -->
      <div class="space-y-2">
        <div class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
          <img src="https://i.pravatar.cc/40?img=11" class="w-9 h-9 rounded-full mr-3" />
          <div>
            <div class="font-semibold text-sm">Jake Nackos</div>
            <div class="text-xs text-gray-500 truncate">Did you see that viral YouTube...</div>
          </div>
        </div>
        <div class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
          <img src="https://i.pravatar.cc/40?img=12" class="w-9 h-9 rounded-full mr-3" />
          <div>
            <div class="font-semibold text-sm">Warren Wong</div>
            <div class="text-xs text-gray-500 truncate">What did you want to be when...</div>
          </div>
        </div>
        <div class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
          <img src="https://i.pravatar.cc/40?img=13" class="w-9 h-9 rounded-full mr-3" />
          <div>
            <div class="font-semibold text-sm">Mike Austin</div>
            <div class="text-xs text-gray-500 truncate">Who's your role model?</div>
          </div>
        </div>
        <div class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
          <img src="https://i.pravatar.cc/40?img=14" class="w-9 h-9 rounded-full mr-3" />
          <div>
            <div class="font-semibold text-sm">Daniel Lincoln</div>
            <div class="text-xs text-gray-500 truncate">What's the high point of your...</div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center gap-2 pt-4 text-sm text-gray-600">
        <span class="cursor-pointer">&lt;</span>
        <span class="font-semibold">1</span>
        <span>2</span>
        <span>3</span>
        <span>4</span>
        <span>5</span>
        <span>6</span>
        <span>7</span>
        <span>8</span>
        <span>9</span>
        <span class="cursor-pointer">&gt;</span>
      </div>
    </div>

    <!-- Right Chat Area -->
    <div class="flex-1 flex flex-col">
      <!-- Chat Header -->
      <div class="flex items-center justify-between border-b p-4">
        <div class="flex items-center gap-3">
          <img src="https://i.pravatar.cc/50?img=32" class="w-10 h-10 rounded-full" />
          <div>
            <div class="font-semibold">Christopher Campbell</div>
            <div class="text-xs text-gray-500">Last seen 02:55 pm</div>
          </div>
        </div>
        <div class="flex items-center gap-3 text-gray-600 text-xl">
          <button title="Add">➕</button>
          <button title="Call">📞</button>
          <button title="More">⋮</button>
        </div>
      </div>

      <!-- Chat Messages -->
      <div id="chatWindow" class="flex-1 p-4 space-y-4 overflow-y-auto bg-gray-50">
        <!-- Left message -->
        <div class="flex items-start gap-2">
          <img src="https://i.pravatar.cc/30?img=32" class="w-8 h-8 rounded-full" />
          <div class="bg-gray-600 text-white px-4 py-2 rounded-2xl max-w-sm">Hey, How are you?</div>
        </div>

        <div class="flex items-start gap-2">
          <img src="https://i.pravatar.cc/30?img=32" class="w-8 h-8 rounded-full" />
          <div class="bg-gray-600 text-white px-4 py-2 rounded-2xl max-w-sm">I was asking for your New Year Plans, ask we are going to host a party.</div>
        </div>

        <!-- Right messages -->
        <div class="flex justify-end">
          <div class="bg-black text-white px-4 py-2 rounded-2xl max-w-sm">I am fine, How about you?</div>
        </div>

        <div class="flex justify-end">
          <div class="bg-black text-white px-4 py-2 rounded-2xl max-w-sm">Yayy, Great I would love to join the party!</div>
        </div>

        <!-- Left message -->
        <div class="flex items-start gap-2">
          <img src="https://i.pravatar.cc/30?img=32" class="w-8 h-8 rounded-full" />
          <div class="bg-gray-600 text-white px-4 py-2 rounded-2xl max-w-sm">Great! Let’s meet in the party!</div>
        </div>
      </div>

      <!-- Message Input -->
      <div class="flex items-center gap-2 border-t p-4 bg-white">
        <button title="Attach" class="text-gray-500 text-xl">📎</button>
        <input id="messageInput" type="text" placeholder="Type your Message..." class="flex-1 outline-none px-4 py-2 bg-gray-100 rounded-full" />
        <button onclick="sendMessage()" title="Send" class="text-gray-500 text-xl">📩</button>
      </div>
    </div>
  </div>
</div>

<script>
  function sendMessage() {
    const input = document.getElementById('messageInput');
    const text = input.value.trim();
    if (text === '') return;

    const chat = document.getElementById('chatWindow');
    const message = document.createElement('div');
    message.className = 'flex justify-end';
    message.innerHTML = `<div class="bg-black text-white px-4 py-2 rounded-2xl max-w-sm">${text}</div>`;
    chat.appendChild(message);
    chat.scrollTop = chat.scrollHeight;

    input.value = '';
  }
</script>
    </div>
  </div>

  

  <!-- sidebar menu  -->
  

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
