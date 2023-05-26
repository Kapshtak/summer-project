import * as React from 'react';
import '../LoginForm.css';
import { useState, useEffect } from 'react';
import axios from 'axios';

function LoginForm(props: any) {

    const [data, setData] = useState({})

    const inputHandler = (e: any) => {
        setData({
            ...data,
            [e.target.id]: e.target.value
        })
    }
    const headie = {
        headers: {
            "Content-Type": "applicaton/json"
        }
    };
    const url = 'http://localhost:8007/api/login'
    const theData = {
        body:{
            data
        }
    }
    
    const handleSubmit = (e: any) => {
        
        e.preventDefault()
        axios
        .post(url, data, {headers: {'Content-Type': 'application/json'}})
        .then(response => console.log(response))
        .catch(e => console.log(e))

    }
    
    
    return(
        <form className='login-form' onSubmit={handleSubmit}>
            <fieldset>
            <legend>Sign in</legend>

            {/* <label htmlFor="fname">Name</label>
            <input type="text" id="fname" onChange={props.inputHandler}/> */}
            <label htmlFor="email">Your Email</label>
            <input type="email" id="email" onChange={inputHandler}/>
            <label htmlFor="password">Password</label>
            <input type="password" id="password" onChange={inputHandler}/>
            <input className="submit-button" type="submit" value="submit" />
            </fieldset>
        </form>
    )
}

export default LoginForm