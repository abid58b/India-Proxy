<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fancode Live Stream</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: #f0f0f0;
    }
    .container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    #live-video {
      width: 100%;
      max-width: 100%;
      height: auto;
    }
    .input-group {
      margin: 20px 0;
    }
    input {
      width: 70%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    button {
      padding: 10px 20px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      margin: 10px 0;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Fancode Live Stream Player</h1>
    
    <div class="input-group">
      <input type="text" id="streamUrl" placeholder="Enter Fancode M3U8 URL here..." 
             value="https://in-mc-fdlive.fancode.com/mumbai/133774_english_hls_47b8605e7d63293ta-di_h264/index.m3u8">
      <button onclick="loadStream()">Play Stream</button>
    </div>
    
    <div id="error" class="error"></div>
    
    <video id="live-video" controls autoplay style="width: 100%;"></video>
  </div>

  <!-- hls.js load -->
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
  <script>
    const video = document.getElementById('live-video');
    const errorDiv = document.getElementById('error');
    
    function loadStream() {
      const streamUrl = document.getElementById('streamUrl').value.trim();
      
      if (!streamUrl) {
        showError('Please enter a valid stream URL');
        return;
      }
      
      // Clear previous errors
      errorDiv.innerHTML = '';
      
      // Use proxy for non-India users
      const proxyUrl = 'https://indiaproxy.vercel.app/proxy?url=' + encodeURIComponent(streamUrl);
      
      if (Hls.isSupported()) {
        if (window.hls) {
          window.hls.destroy();
        }
        
        const hls = new Hls({
          enableWorker: true,
          lowLatencyMode: true,
          backBufferLength: 90
        });
        
        window.hls = hls;
        
        hls.loadSource(proxyUrl);
        hls.attachMedia(video);
        
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
          console.log('Stream loaded successfully');
          video.play().catch(e => showError('Autoplay failed: ' + e.message));
        });
        
        hls.on(Hls.Events.ERROR, function(event, data) {
          if (data.fatal) {
            switch(data.type) {
              case Hls.ErrorTypes.NETWORK_ERROR:
                showError('Network error - Stream not accessible');
                break;
              case Hls.ErrorTypes.MEDIA_ERROR:
                showError('Media error - Try refreshing');
                break;
              default:
                showError('Error loading stream: ' + data.details);
                break;
            }
          }
        });
        
      } 
      else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        // Safari support
        video.src = proxyUrl;
        video.addEventListener('loadedmetadata', () => {
          video.play().catch(e => showError('Autoplay failed: ' + e.message));
        });
      } 
      else {
        showError('Your browser does not support HLS streaming');
      }
    }
    
    function showError(message) {
      errorDiv.innerHTML = message;
      console.error(message);
    }
    
    // Load default stream on page load
    window.onload = function() {
      loadStream();
    };
  </script>
</body>
</html>