<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" user-scalable="no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Lightworx - {{$pageName}}</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  <!-- Favicons -->
  <link href="{{ asset('lightworx/images/icons/favicon.png') }}" rel="icon">
  <link href="{{ asset('lightworx/images/icons/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- PWA -->
  <link rel="manifest" href="{{ url('/manifest.json') }}" crossorigin="use-credentials" />
  <!-- Chrome for Android theme color -->
  <meta name="theme-color" content="#000000">
  
  <!-- Add to homescreen for Chrome on Android -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="application-name" content="Connexion">
  <link rel="icon" sizes="512x512" href="{{ asset('lightworx/images/icons/android/android-launchericon-512-512.png') }}">
  
  <!-- Add to homescreen for Safari on iOS -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Connexion">
  <link rel="apple-touch-icon" href="{{ asset('lightworx/images/icons/ios/512.png') }}">
  <!-- Tile for Win8 -->
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="{{ asset('lightworx/images/icons/android/android-launchericon-512-512.png') }}">


  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
  <!-- Media player -->  
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/{{setting('site_theme')}}/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
  <main class="main">
    <div class="d-flex justify-content-center my-2">
      <button id="installPwaBtn" class="btn btn-primary btn-md d-none">
          <i class="bi bi-download me-2"></i> Install App
      </button>
    </div>
    {{$slot}}
  </main>

  <footer class="fixed-bottom bg-dark text-center text-white p-3">&copy;{{date('Y')}} Lightworx</footer>

  <!-- Vendor JS Files -->
  <script src="{{ asset('lightworx/js/bootstrap-bundle.min.js') }}"></script>
  <script type="text/javascript">
      // Initialize the service worker
      if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register("{{ url('/service-worker.js') }}", {
            scope: '/'
        }).then(function (registration) {
            // Registration was successful
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('ServiceWorker registration failed: ', err);
        });
      }
    
    // PWA installation prompt
    if (location.protocol === "https:" || location.hostname === "localhost" || location.hostname === "127.0.0.1") {
      let deferredPrompt;
      const installBtn = document.getElementById("installPwaBtn");

      window.addEventListener("beforeinstallprompt", (e) => {
          e.preventDefault();
          deferredPrompt = e;
          installBtn.classList.remove("d-none");
      });

      installBtn.addEventListener("click", async () => {
          if (deferredPrompt) {
              deferredPrompt.prompt();
              const { outcome } = await deferredPrompt.userChoice;
              console.log(`User response to install prompt: ${outcome}`);
              deferredPrompt = null;
              installBtn.classList.add("d-none");
          }
      });

      window.addEventListener("appinstalled", () => {
          console.log("PWA installed successfully");
          installBtn.classList.add("d-none");
      });
    }
  </script>
</body>

</html>
