import React, { useState } from 'react';

function Product({ product }) {
    const {name, sku, price, size, weight, height, width, length} = product;
    const [isChecked, setIsChecked] = useState(false);

    return (
        <div className="col-md-3">
            <div className="card">
                <input type="checkbox" className="form-check-input delete-checkbox" id={sku} checked={isChecked} onChange={() => setIsChecked(!isChecked)}/>
                <div className="card-body">
                    <p className="card-sku">{sku}</p>
                    <p className="card-name">{name}</p>
                    <p className="card-price">{price} USD</p>
                    {size && <p className="size">{size} MB</p>}
                    {width && <p className="dimensions">{height} x {width} x {length} cm</p>}
                    {weight && <p className="weight">{weight} kg</p>}
                </div>
            </div>
        </div>

    );
}

export default Product;