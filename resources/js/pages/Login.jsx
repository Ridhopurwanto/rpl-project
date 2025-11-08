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
    <div className="min-h-screen bg-gray-200 flex items-center justify-center p-4">
      <div className="w-full max-w-sm mx-auto">
        {/* Header dengan background gradient biru gelap */}
        <div className="bg-gradient-to-b from-[#1a2847] via-[#1a2d4d] to-[#2a4a6f] rounded-t-[2.5rem] pt-8 pb-12 px-6 text-center relative overflow-visible">
          <div className="absolute top-0 left-0 w-16 h-32 bg-gradient-to-br from-gray-900 to-transparent"></div>
          <div className="absolute top-0 right-0 w-16 h-32 bg-gradient-to-bl from-[#1e3a5f] to-transparent"></div>

          {/* Logo dan teks */}
          <div className="relative">
            <div className="w-32 h-32 mx-auto mb-4 bg-white rounded-full flex items-center justify-center shadow-lg relative z-10">
              <img
                src="https://image2url.com/images/1762600110881-a57c9232-5a2a-4988-babd-941384796a15.png"
                alt="Logo SIAP"
                className="w-28 h-28 object-contain"
                onError={(e) => {
                  e.target.style.display = 'none';
                }}
              />
            </div>

            {/* Background putih melengkung besar di belakang logo */}
            <div className="absolute -top-8 left-1/2 -translate-x-1/2 w-56 h-52 bg-white rounded-b-full z-0"></div>
          </div>

          {/* Judul SIAP */}
          <div className="relative z-10 mt-16">
            <h1 className="text-5xl font-bold text-white tracking-[0.2em] mb-1">SIAP</h1>
            <p className="text-2xs text-white/90 font-light">Sistem Informasi Administrasi dan Pelaporan</p>
          </div>
        </div>

        {/* Login Form Card dengan latar belakang biru dan rounded */}
        <div className="bg-gradient-to-b from-[#FFFFFF] to-[#FFFFFF] rounded-t-[2.5rem] rounded-b-[2.5rem] shadow-xl px-8 py-10 -mt-8 z-10 relative">
          <h2 className="text-3xl font-bold text-center text-black mb-8 tracking-wide">LOGIN</h2>

          <div className="space-y-6">
            {/* Username Input */}
            <div>
              <label className="block text-center text-sm font-semibold text-black mb-2 tracking-wide">
                USERNAME
              </label>
              <input
                type="text"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                className="w-full py-3 px-4 rounded-full bg-gray-300 text-base font-bold text-center text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-900 transition"
              />
            </div>

            {/* Password Input */}
            <div>
              <label className="block text-center text-sm font-semibold text-black mb-2 tracking-wide">
                Password
              </label>
              <div className="relative">
                <input
                  type={showPassword ? "text" : "password"}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  onKeyPress={(e) => e.key === 'Enter' && handleSubmit()}
                  className="w-full py-3 px-4 pr-12 rounded-full bg-gray-300 text-center font-semibold text-base text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-900 transition"
                  placeholder="••••••••••"
                />
                {/* Eye Icon Button */}
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-700 hover:text-gray-900 transition"
                >
                  {showPassword ? (
                    <EyeOff size={20} strokeWidth={2.5} />
                  ) : (
                    <Eye size={20} strokeWidth={2.5} />
                  )}
                </button>
              </div>
            </div>

            {/* Submit Button */}
            <div className="pt-2">
              <button
                onClick={handleSubmit}
                className="w-36 mx-auto block bg-[#152238] text-white font-bold py-2.5 px-8 rounded-lg hover:bg-[#1a2d4d] active:scale-95 transition-all shadow-md hover:shadow-lg text-base tracking-widest"
              >
                MASUK
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Login;
