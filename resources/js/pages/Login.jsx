import React, { useState } from 'react';
import { Eye, EyeOff } from 'lucide-react';

function Login({ onLogin }) {
  const [username, setUsername] = useState('M. SONY');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);

  const handleSubmit = () => {
    if (username && password) {
      onLogin(username);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 flex items-center justify-center p-4 lg:p-8">
      {/* Container responsif: mobile (full), tablet (max-w-md), desktop (max-w-lg) */}
      <div className="w-full max-w-md lg:max-w-lg xl:max-w-xl mx-auto">
        <div className="bg-gradient-to-b from-white to-gray-100 rounded-t-3xl pt-10 md:pt-12 lg:pt-16 pb-6 md:pb-8 lg:pb-10 px-6 md:px-8 lg:px-12 text-center">
          {/* Logo dengan ukuran responsif */}
          <div className="w-24 h-24 md:w-28 md:h-28 lg:w-36 lg:h-36 xl:w-40 xl:h-40 mx-auto mb-4 md:mb-6 lg:mb-8 bg-gradient-to-br from-blue-800 to-blue-900 rounded-full flex items-center justify-center shadow-xl">
            <div className="w-20 h-20 md:w-24 md:h-24 lg:w-32 lg:h-32 xl:w-36 xl:h-36 bg-white rounded-full flex items-center justify-center">
              <svg viewBox="0 0 100 100" className="w-16 h-16 md:w-18 md:h-18 lg:w-24 lg:h-24 xl:w-28 xl:h-28">
                <path d="M50 10 L50 50 L80 30 Z" fill="#1e3a8a" />
                <path d="M20 30 L50 50 L50 10 Z" fill="#3b82f6" />
                <circle cx="50" cy="70" r="8" fill="#1e3a8a" />
                <path d="M35 65 L50 80 L65 65" stroke="#1e3a8a" strokeWidth="3" fill="none" />
              </svg>
            </div>
          </div>
          
          <h1 className="text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold text-gray-800 mb-2 lg:mb-3">SIAP</h1>
          <p className="text-xs md:text-sm lg:text-base xl:text-lg text-gray-600">Sistem Informasi Administrasi dan Pelaporan</p>
        </div>

        <div className="bg-white rounded-b-3xl shadow-2xl p-6 md:p-8 lg:p-10 xl:p-12">
          <h2 className="text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold text-center text-gray-800 mb-6 md:mb-8 lg:mb-10">LOGIN</h2>
          
          <div className="space-y-5 md:space-y-6 lg:space-y-8">
            <div>
              <label className="block text-xs md:text-sm lg:text-base font-semibold text-gray-700 mb-2 lg:mb-3 text-center">
                USERNAME
              </label>
              <input
                type="text"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                className="w-full px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 bg-gray-100 rounded-full text-center font-semibold text-sm md:text-base lg:text-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                placeholder="M. SONY"
              />
            </div>

            <div>
              <label className="block text-xs md:text-sm lg:text-base font-semibold text-gray-700 mb-2 lg:mb-3 text-center">
                Password
              </label>
              <div className="relative">
                <input
                  type={showPassword ? "text" : "password"}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  onKeyPress={(e) => e.key === 'Enter' && handleSubmit()}
                  className="w-full px-4 lg:px-6 py-2.5 md:py-3 lg:py-4 bg-gray-100 rounded-full text-center font-semibold text-sm md:text-base lg:text-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                  placeholder="••••••••••"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 md:right-4 lg:right-6 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 touch-manipulation transition"
                >
                  {showPassword ? 
                    <EyeOff size={18} className="md:w-5 md:h-5 lg:w-6 lg:h-6" /> : 
                    <Eye size={18} className="md:w-5 md:h-5 lg:w-6 lg:h-6" />
                  }
                </button>
              </div>
            </div>

            <button
              onClick={handleSubmit}
              className="w-32 md:w-36 lg:w-44 xl:w-52 mx-auto block bg-gradient-to-r from-blue-900 to-blue-800 text-white font-bold py-2.5 md:py-3 lg:py-4 px-5 md:px-6 lg:px-8 rounded-full hover:from-blue-800 hover:to-blue-700 active:scale-95 transition-all shadow-lg hover:shadow-xl touch-manipulation text-sm md:text-base lg:text-lg"
            >
              MASUK
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Login;