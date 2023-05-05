import React, {useEffect, useState} from "react";
import Head from 'next/head';
import Footer from "./Footer";
import Header from "./Header";
import AddProductActions from "./AddProductActions";
import axios from "axios";

function AddProduct() {
    const [productType, setProductType] = useState("DVD");
    const [productTypeText, setProductTypeText] = useState("Please, provide sizes");
    const [productProperties, setProductProperties] = useState([]);
    const [error, setError] = useState(null);

    const handleInput = (event) => {
        const regex = /^\d*\.?\d{0,2}$/;
        const value = event.target.value;
        if (!regex.test(value)) {
            event.target.value = value.slice(0, -1);
        }
    };

    const handleProductTypeChange = (e) => {
        setProductType(e.target.value);
        const typeTexts = {
            "DVD": "Please, provide sizes",
            "Book": "Please, provide weight",
            "Furniture": "Please, provide dimensions"
        };
        setProductTypeText(typeTexts[e.target.value]);
    };

    useEffect(() => {
        axios
            .get("/api/get_products_properties")
            .then((response) => {
                setProductProperties(response.data);
            })
            .catch((error) => console.log(error));
    }, []);

    const handleSave = () => {
        const form = document.getElementById("product_form");
        const formData = new FormData(form);
        formData.append('type', productType);

        const jsonData = JSON.stringify(Object.fromEntries(formData));

        axios.post('/api/save_product', jsonData,
            {
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.data.error) {
                    const errorMessages = {
                        "already_exists": "Product with such sku already exists",
                        "invalid_data": "Please, provide the data of indicated type",
                        "fields_required": "Please, submit required data"
                    };
                    setError(errorMessages[response.data.error] || "Transaction error: " + response.data.error);
                } else {
                    window.location.href = "/";
                }

            })
            .catch(error => console.log(error));
    };

    return (
        <>
            <Head>
                <title>Add Product</title>
            </Head>
            <Header title={"Add Product"} actions={<AddProductActions handleSave={handleSave}/>}/>
            <main>
                {error && (
                    <div className="alert alert-danger">
                        {error}
                    </div>
                )}
                {productProperties.length > 0 && (
                    <form id="product_form">
                        {productProperties[0]["Product"].map((row) => (
                            row !== "type" && (
                                <div className="form-group row mb-3">
                                    <label className="col-sm-2 col-form-label" htmlFor={row}>{row}</label>
                                    <div className="col-sm-2">
                                        <input className={row === "price" ? "form-control float-input":"form-control"} type="text"
                                               onInput={row === "price" ? handleInput : undefined} name={row}
                                               id={row}/>
                                    </div>
                                </div>
                            )
                        ))}
                        <div className="form-group row mb-3">
                            <label className="col-sm-2 col-form-label" htmlFor="productType">Product Type:</label>
                            <div className="col-sm-2">
                                <select
                                    id="productType"
                                    className="form-select"
                                    value={productType}
                                    onChange={handleProductTypeChange}>
                                    {Object.keys(productProperties[0])
                                        .filter((key) => key !== "Product")
                                        .map((key) => (
                                            <option key={key} value={key}>
                                                {key}
                                            </option>
                                        ))}
                                </select>
                            </div>
                        </div>
                        {Object.keys(productProperties[0])
                            .filter((key) => key !== "Product" && key === productType)
                            .map((key) => (
                                <div className="type-div" key={key} id={key}>
                                    <p>{productTypeText}</p>
                                    {productProperties[0][key].map((row) => (
                                        !productProperties[0]["Product"].includes(row) && (
                                            <div className="form-group row mb-3">
                                                <label className="col-sm-2 col-form-label" htmlFor={row}>{row}</label>
                                                <div className="col-sm-2">
                                                    <input className="form-control float-input" type="text"
                                                           onInput={handleInput} name={row} id={row}/>
                                                </div>
                                            </div>
                                        )
                                    ))}
                                </div>
                            ))
                        }
                    </form>
                )}
            </main>
            <Footer/>
        </>
    );
}

export default AddProduct;
