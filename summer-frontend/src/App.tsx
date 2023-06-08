import React, { useEffect, useState } from 'react'
import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Home from './pages/Home'
import Faq from './pages/Faq'
import Event from './pages/Event'
import Profile from './pages/Profile'
import Layout from './pages/Layout'
import LoginForm from './components/LoginForm'
import Helsinki from './components/Helsinki'
import { CurrentUserContext } from './context/context'
import { CurrentUserType } from './types/users'

const App = () => {
  const [currentUser, setCurrentUser] = useState<CurrentUserType>(null)

  const logout = () => {
    setCurrentUser(null)
    localStorage.removeItem('user')
    localStorage.removeItem('roles')
    localStorage.removeItem('token')
  }

  useEffect(() => {
    const checkLocalStorage = () => {
      const storedUser = localStorage.getItem('user')
      const storedToken = localStorage.getItem('token')
      const storedRoles = localStorage.getItem('roles')
      if (storedUser && storedToken && storedRoles) {
        const userData = {
          user: Number(storedUser),
          token: storedToken,
          roles: JSON.parse(storedRoles)
        }
        setCurrentUser(userData)
      }
    }
    checkLocalStorage()
  }, [])

  return (
    <BrowserRouter>
      <CurrentUserContext.Provider
        value={{ currentUser, setCurrentUser, logout }}
      >
        <Routes>
          <Route path="/" element={<Layout />}>
            <Route index element={<Home />} />
            <Route path="faq" element={<Faq />} />
            <Route path="/events/:event" element={<Event />} />
            <Route path="profile" element={<Profile />} />
            <Route path="login" element={<LoginForm />} />
            <Route path="helsinki" element={<Helsinki />} />
            {/* <Route path="poll" element={<Poll data={}/>} /> */}
          </Route>
        </Routes>
      </CurrentUserContext.Provider>
    </BrowserRouter>
  )
}

export default App
