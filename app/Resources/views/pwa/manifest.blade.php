{
  "name": "Lightworx PWA",
  "short_name": "Lightworx",
  "description": "The Lightworx app is for managing tasks and clients",
  "start_url": "/admin",
  "display": "standalone",
  "background_color": "#ffffff",
  "orientation": "portrait-primary",
  "theme_color": "#000000",
  "scope": "/",
  "icons": [
    {
        "src": "../lightworx/images/icons/android/android-launchericon-512-512.png",
        "sizes": "512x512",
        "type": "image/png",
        "purpose": "maskable"
    },
    {
        "src": "../lightworx/images/icons/android/android-launchericon-192-192.png",
        "sizes": "192x192",
        "type": "image/png",
        "purpose": "any"
    }
  ],
  "screenshots": [
      {
          "src": "../lightworx/images/icons/screenshot.png",
          "sizes": "640x320",
          "type": "image/png",
          "description": "App screenshot"
      }
  ],
  "shortcuts": [
      {
          "name":"Admin",
          "url":"/admin",
          "description":"Access the admin panel"
      }
  ]
}
