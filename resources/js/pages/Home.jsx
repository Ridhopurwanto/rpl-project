import React, { useState, useEffect, useRef } from 'react';

function Home({ username, onLogout, onNavigate }) {
  const [currentTime, setCurrentTime] = useState(new Date());
  const [showProfileMenu, setShowProfileMenu] = useState(false);
  const profileMenuRef = useRef(null);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentTime(new Date());
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  // Close dropdown when clicking outside
  useEffect(() => {
    function handleClickOutside(event) {
      if (profileMenuRef.current && !profileMenuRef.current.contains(event.target)) {
        setShowProfileMenu(false);
      }
    }
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const timeString = currentTime.toLocaleTimeString('id-ID', { 
    hour: '2-digit', 
    minute: '2-digit' 
  });

  const dateString = currentTime.toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).toUpperCase();

  const menuItems = [
    { title: 'PRESENSI', icon: '✓', page: 'presensi' },
    { title: 'PATROLI', icon: '👮', page: 'patroli' },
    { title: 'KENDARAAN', icon: '🚗', page: 'kendaraan' },
    { title: 'TAMU', icon: '📋', page: 'tamu' },
    { title: 'BARANG', icon: '📦', page: 'barang' },
    { title: 'GANGGUAN KAMTIBMAS', icon: '⚠️', page: 'kamtibmas' },
  ];

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 p-3 md:p-4 lg:p-6 xl:p-8">
      <div className="w-full max-w-md lg:max-w-2xl xl:max-w-4xl mx-auto">
        <div className="bg-gradient-to-r from-blue-800 to-blue-900 rounded-t-3xl p-4 md:p-6 lg:p-8 shadow-xl">
          {/* Header Icons */}
          <div className="flex items-center justify-between mb-4 md:mb-5 lg:mb-6">
            <button className="w-10 h-10 md:w-11 md:h-11 lg:w-14 lg:h-14 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 active:scale-95 transition touch-manipulation">
              <span className="text-xl md:text-2xl lg:text-3xl">🏠</span>
            </button>
            <div className="flex gap-2 md:gap-2.5 lg:gap-3">
              <button className="w-10 h-10 md:w-11 md:h-11 lg:w-14 lg:h-14 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 active:scale-95 transition touch-manipulation">
                <span className="text-xl md:text-2xl lg:text-3xl">📅</span>
              </button>
              <button className="w-10 h-10 md:w-11 md:h-11 lg:w-14 lg:h-14 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 active:scale-95 transition touch-manipulation">
                <span className="text-xl md:text-2xl lg:text-3xl">🔔</span>
              </button>
              
              {/* Profile Button with Dropdown */}
              <div className="relative" ref={profileMenuRef}>
                <button 
                  onClick={() => setShowProfileMenu(!showProfileMenu)}
                  className="w-10 h-10 md:w-11 md:h-11 lg:w-14 lg:h-14 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 active:scale-95 transition touch-manipulation"
                >
                  <span className="text-xl md:text-2xl lg:text-3xl">👤</span>
                </button>

                {/* Dropdown Menu */}
                {showProfileMenu && (
                  <div className="absolute right-0 mt-2 w-64 md:w-72 lg:w-80 bg-white rounded-2xl shadow-2xl overflow-hidden z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                    {/* Profile Header */}
                    <div className="bg-gradient-to-r from-blue-800 to-blue-900 p-4 md:p-5 lg:p-6">
                      <div className="flex items-center gap-3 md:gap-4">
                        <div className="w-12 h-12 md:w-14 md:h-14 lg:w-16 lg:h-16 bg-white rounded-full flex items-center justify-center text-2xl md:text-3xl lg:text-4xl">
                          👤
                        </div>
                        <div className="flex-1 text-white">
                          <p className="text-xs md:text-sm opacity-80">Pengguna</p>
                          <p className="text-base md:text-lg lg:text-xl font-bold">{username}</p>
                        </div>
                      </div>
                    </div>

                    {/* Menu Items */}
                    <div className="p-2 md:p-3">
                      <button className="w-full flex items-center gap-3 px-4 py-3 md:py-3.5 rounded-xl hover:bg-gray-100 transition text-left group">
                        <span className="text-xl md:text-2xl">👤</span>
                        <span className="text-sm md:text-base lg:text-lg font-semibold text-gray-700 group-hover:text-blue-800">Info Profil</span>
                      </button>
                      
                      <button className="w-full flex items-center gap-3 px-4 py-3 md:py-3.5 rounded-xl hover:bg-gray-100 transition text-left group">
                        <span className="text-xl md:text-2xl">⚙️</span>
                        <span className="text-sm md:text-base lg:text-lg font-semibold text-gray-700 group-hover:text-blue-800">Pengaturan</span>
                      </button>

                      <div className="h-px bg-gray-200 my-2"></div>

                      <button 
                        onClick={onLogout}
                        className="w-full flex items-center gap-3 px-4 py-3 md:py-3.5 rounded-xl hover:bg-red-50 transition text-left group"
                      >
                        <span className="text-xl md:text-2xl">🚪</span>
                        <span className="text-sm md:text-base lg:text-lg font-semibold text-red-600 group-hover:text-red-700">Keluar</span>
                      </button>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* Date Time Card - Tombol KELUAR dihapus */}
          <div className="bg-blue-700 rounded-2xl p-4 md:p-5 lg:p-6 mb-4 md:mb-5 lg:mb-6">
            <div className="text-white">
              <p className="text-xs md:text-sm lg:text-base opacity-80">{dateString}</p>
              <p className="text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold">{timeString}</p>
            </div>
          </div>

          {/* Welcome Card */}
          <div className="bg-blue-700 rounded-2xl p-4 md:p-5 lg:p-6">
            <p className="text-white text-sm md:text-base lg:text-lg mb-1">SELAMAT DATANG,</p>
            <p className="text-white text-2xl md:text-3xl lg:text-4xl font-bold">{username}</p>
          </div>
        </div>

        {/* Menu Section */}
        <div className="bg-white rounded-b-3xl shadow-2xl p-5 md:p-6 lg:p-8 xl:p-10">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-4 lg:gap-5">
            {menuItems.map((item, index) => (
              <button
                key={index}
                onClick={() => onNavigate && onNavigate(item.page)}
                className="bg-gradient-to-r from-blue-800 to-blue-900 hover:from-blue-700 hover:to-blue-800 active:scale-98 text-white rounded-full py-3 md:py-4 lg:py-5 px-5 md:px-6 lg:px-8 flex items-center justify-between transition-all shadow-md hover:shadow-lg group touch-manipulation"
              >
                <span className="text-base md:text-lg lg:text-xl font-bold">{item.title}</span>
                <span className="text-xl md:text-2xl lg:text-3xl group-hover:translate-x-1 transition-transform">≫</span>
              </button>
            ))}
          </div>

          <div className="mt-6 md:mt-8 lg:mt-10 text-center">
            <p className="text-gray-600 text-xs md:text-sm lg:text-base font-semibold">Siap v 1.0.0</p>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Home;