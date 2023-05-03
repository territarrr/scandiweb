import React from "react";

function Header(props) {
    const {title, actions} = props
    return (
        <header>
            <div>
                <h1>{title}</h1>
                <hr className="thick"/>
                {actions}
            </div>
        </header>
    );
}

export default Header;