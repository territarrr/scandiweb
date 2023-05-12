import React, {useEffect, useState} from 'react';
import axios from 'axios';
import Product from './Product';
import ProductListActions from "./ProductListActions";
import Header from "./Header";
import Footer from "./Footer";

function ProductList() {
    const [products, setProducts] = useState([]);
    const [updateListKey, setUpdateListKey] = useState(false);

    useEffect(() => {
        axios
            .get('/api/get_products')
            .then((response) => {
                setProducts(response.data);
            })
            .catch((error) => console.log(error));
    }, [updateListKey]);

    const handleMassDelete = () => {
        const checkedProducts = document.querySelectorAll('.card input[type="checkbox"]:checked');
        const skus = Array.from(checkedProducts).map((product) => product.id);

        if(skus.length>0) {
            axios
                .post('/api/delete_products', skus, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(() => {
                    setUpdateListKey(!updateListKey);
                })
                .catch((error) => console.log(error));
        }
    };

    const rows = products.reduce((accumulator, product, index) => {
        const rowNum = Math.floor(index / 4);
        if (!accumulator[rowNum]) {
            accumulator[rowNum] = [];
        }
        accumulator[rowNum].push(<Product key={product.sku} product={product}/>);
        return accumulator;
    }, []);

    return (
        <>
            <Header title={"Product List"} actions={<ProductListActions handleMassDelete={handleMassDelete}/>}/>
            <main>
                <div className="container">
                    <div className="products-list">
                        {rows.length === 0 ? (
                            <p id="no_products">No products found</p>
                        ) : (
                            rows.map((row, index) => (
                                <div key={index} className="row product-row">
                                    {row}
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </main>
            <Footer/>
        </>
    );
}

export default ProductList;
