<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Live Stream</title>
</head>
<body>
  <video id="live-video" controls autoplay style="width: 100%; max-width: 640px;"></video>
  
  <!-- hls.js load -->
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
  <script>
    const video = document.getElementById('live-video');
    const streamUrl = 'https://your-cdn.com/path/to/stream.m3u8'; // ekhane nijer stream url diba
    
    if (Hls.isSupported()) {
      const hls = new Hls();
      hls.loadSource(streamUrl);
      hls.attachMedia(video);
      hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
    } 
    else if (video.canPlayType('application/vnd.apple.mpegurl')) {
      video.src = streamUrl;
      video.addEventListener('loadedmetadata', () => video.play());
    } 
    else {
      video.innerHTML = 'Your browser does not support HLS.';
    }
  </script>
</body>
</html>