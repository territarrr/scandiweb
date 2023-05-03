import fetch from 'node-fetch';

export default async function handler(req, res) {
    const url = `${process.env.PHP_SERVER_URL}${req.url.replace(/\/?$/, '.php')}`;
    const response = await fetch(url);
    const data = await response.json();
    res.status(200).json(data);
}
