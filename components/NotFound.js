import React from 'react';
import Head from 'next/head';

function NotFound() {
    return (
        <>
            <Head>
                <title>Page Not Found</title>
            </Head>
            <div>
                <h1>404 - Page Not Found</h1>
                <p>Sorry, the page you are looking for does not exist.</p>
            </div>
        </>
    );
}

export default NotFound;
