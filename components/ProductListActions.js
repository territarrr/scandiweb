import React from 'react';

function ProductListActions({handleMassDelete}) {
    return (
        <div className="action-buttons">
        <a href="/add-product/" className="btn btn-light">ADD</a>
        <button className="btn btn-light" id="delete-ptoduct-btn" onClick={handleMassDelete}>MASS DELETE</button>
        </div>
    );
}

export default ProductListActions;