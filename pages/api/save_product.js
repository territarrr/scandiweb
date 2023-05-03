import fetch from 'node-fetch';

export default async function handler(req, res) {
    const { body } = req;

    const url = `${process.env.PHP_SERVER_URL}${req.url.replace(/\/?$/, '.php')}`;
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(body),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        res.status(200).json(data);
    } catch (error) {
        console.error(error);
        res.status(500).json({ error: 'Internal Server Error' });
    }
}