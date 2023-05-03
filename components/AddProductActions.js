import React from 'react';

function AddProductActions({handleSave}) {
    return (
        <div className="action-buttons">
            <button className="btn btn-light" onClick={handleSave}>Save</button>
            <a href="/" className="btn btn-light">Cancel</a>
        </div>
    );
}

export default AddProductActions;
