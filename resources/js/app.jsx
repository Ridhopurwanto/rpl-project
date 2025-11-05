import { createRoot } from 'react-dom/client';
import { useState } from 'react';
import Login from '@/pages/login.jsx';
import Home from '@/pages/home.jsx';
import '../css/app.css';

function App() {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [username, setUsername] = useState('');

  function handleLogin(user) {
    setUsername(user);
    setIsLoggedIn(true);
  }

  function handleLogout() {
    setIsLoggedIn(false);
    setUsername('');
  }

  if (isLoggedIn) {
    return <Home username={username} onLogout={handleLogout} />;
  }
  
  return <Login onLogin={handleLogin} />;
}

const container = document.getElementById('app');
if (container) {
  const root = createRoot(container);
  root.render(<App />);
}