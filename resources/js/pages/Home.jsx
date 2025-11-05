import React from 'react';

function Home({ username, onLogout }) {
  const currentTime = new Date().toLocaleTimeString('id-ID', { 
    hour: '2-digit', 
    minute: '2-digit' 
  });

  const menuItems = [
    { title: 'PRESENSI', icon: '✓' },
    { title: 'PATROLI', icon: '👮' },
    { title: 'KENDARAAN', icon: '🚗' },
    { title: 'TAHU', icon: '📋' },
    { title: 'BARANG', icon: '📦' },
    { title: 'GANGGUAN KAMTIBMAS', icon: '⚠️' },
  ];

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 p-4">
      <div className="max-w-md mx-auto">
        <div className="bg-gradient-to-r from-blue-800 to-blue-900 rounded-t-3xl p-6 shadow-xl">
          <div className="flex items-center justify-between mb-4">
            <button className="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 transition">
              <span className="text-white text-xl">🏠</span>
            </button>
            <div className="flex gap-2">
              <button className="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 transition">
                <span className="text-white text-xl">📅</span>
              </button>
              <button className="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 transition">
                <span className="text-white text-xl">🔔</span>
              </button>
              <button className="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 transition">
                <span className="text-white text-xl">👤</span>
              </button>
            </div>
          </div>

          <div className="bg-blue-700 rounded-2xl p-4 mb-4">
            <div className="flex items-center justify-between mb-2">
              <div className="text-white">
                <p className="text-xs opacity-80">MINGGU, 24 SEPTEMBER 2023</p>
                <p className="text-3xl font-bold">{currentTime}</p>
              </div>
              <button 
                onClick={onLogout}
                className="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-700 transition"
              >
                KELUAR
              </button>
            </div>
          </div>

          <div className="bg-blue-700 rounded-2xl p-4">
            <p className="text-white text-sm mb-1">SELAMAT DATANG,</p>
            <p className="text-white text-2xl font-bold">{username}</p>
          </div>
        </div>

        <div className="bg-white rounded-b-3xl shadow-2xl p-6">
          <div className="grid grid-cols-1 gap-4">
            {menuItems.map((item, index) => (
              <button
                key={index}
                className="bg-gradient-to-r from-blue-800 to-blue-900 hover:from-blue-700 hover:to-blue-800 text-white rounded-full py-4 px-6 flex items-center justify-between transition-all shadow-md hover:shadow-lg group"
              >
                <span className="text-lg font-bold">{item.title}</span>
                <span className="text-2xl group-hover:translate-x-1 transition-transform">≫</span>
              </button>
            ))}
          </div>

          <div className="mt-8 text-center">
            <p className="text-gray-600 text-sm font-semibold">Siap v 1.0.0</p>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Home;