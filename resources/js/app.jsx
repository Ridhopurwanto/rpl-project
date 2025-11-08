import { useState } from 'react';
import Login from '@/pages/Login.jsx';
import Home from '@/pages/Home.jsx';
import Presensi from '@/pages/Presensi.jsx';
import { createRoot } from 'react-dom/client';

function App() {
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [username, setUsername] = useState('');
    const [currentPage, setCurrentPage] = useState('home');

    function handleLogin(user) {
        setUsername(user);
        setIsLoggedIn(true);
        setCurrentPage('home');
    }

    function handleLogout() {
        setIsLoggedIn(false);
        setUsername('');
        setCurrentPage('home');
    }

    function navigateTo(page) {
        setCurrentPage(page);
    }

    if (!isLoggedIn) {
        return <Login onLogin={handleLogin} />;
    }

    switch(currentPage) {
        case 'presensi':
            return <Presensi username={username} onBack={() => navigateTo('home')} />;
        case 'home':
        default:
            return <Home username={username} onLogout={handleLogout} onNavigate={navigateTo} />;
    }
}

// TAMBAHKAN INI: Mount React ke DOM
const root = createRoot(document.getElementById('app'));
root.render(<App />);

export default App;