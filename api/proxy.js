const fetch = require('node-fetch');

module.exports = async (req, res) => {
  const { url } = req.query;

  if (!url) {
    return res.status(400).send('URL nahi di gayi. ?url= parameter use karein.');
  }

  try {
    // Fetch with India-specific headers
    const response = await fetch(url, {
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Accept': '*/*',
        'Accept-Language': 'en-IN,en;q=0.9',
        'Referer': 'https://www.google.com/',
        'Origin': 'https://www.google.com',
        'X-Forwarded-For': '103.21.244.0', // India IP range
        'CF-Connecting-IP': '103.21.244.0'  // Cloudflare India IP
      }
    });

    const data = await response.text();

    // Set proper headers for M3U8
    res.setHeader('Content-Type', 'application/vnd.apple.mpegurl');
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
    
    res.status(200).send(data);
  } catch (error) {
    res.status(500).send('Error fetching stream: ' + error.message);
  }
};