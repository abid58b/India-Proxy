const fetch = require('node-fetch');

module.exports = async (req, res) => {
    // Hum target URL ko query parameter se lenge (e.g., ?url=https://example.com)
    const targetUrl = req.query.url;

    if (!targetUrl) {
        res.status(400).send('Error: "url" query parameter is missing.');
        return;
    }

    try {
        // Target URL se data fetch karein
        const response = await fetch(targetUrl, {
            // Client se aane walay headers ko aagay pass karein
            headers: {
                ...req.headers,
                host: new URL(targetUrl).host, // Host header ko aane wali request k hisaab se set karein
            },
            // Client se aane wala method (GET, POST, etc.) istemal karein
            method: req.method,
            // Agar body hai (e.g., POST request mein), to usay bhi pass karein
            body: req.method !== 'GET' && req.method !== 'HEAD' ? req.body : undefined,
        });

        // Original response se headers lein aur apne response mein set karein
        response.headers.forEach((value, name) => {
            // 'content-encoding' header ko skip karein taake Vercel compression handle kar sakay
            if (name.toLowerCase() !== 'content-encoding') {
                res.setHeader(name, value);
            }
        });
        
        // Original status code aur data wapas client ko bhej dein
        res.status(response.status).send(await response.buffer());

    } catch (error) {
        console.error(error);
        res.status(500).send(`Server error: ${error.message}`);
    }
};