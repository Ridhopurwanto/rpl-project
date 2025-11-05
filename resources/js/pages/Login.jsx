import { useState } from 'react';
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
    <div className="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <div className="bg-gradient-to-b from-white to-gray-100 rounded-t-3xl pt-12 pb-8 px-8 text-center">
          <div className="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-blue-800 to-blue-900 rounded-full flex items-center justify-center shadow-xl">
            <div className="w-28 h-28 bg-white rounded-full flex items-center justify-center">
              <svg viewBox="0 0 100 100" className="w-20 h-20">
                <path d="M50 10 L50 50 L80 30 Z" fill="#1e3a8a" />
                <path d="M20 30 L50 50 L50 10 Z" fill="#3b82f6" />
                <circle cx="50" cy="70" r="8" fill="#1e3a8a" />
                <path d="M35 65 L50 80 L65 65" stroke="#1e3a8a" strokeWidth="3" fill="none" />
              </svg>
            </div>
          </div>
          
          <h1 className="text-4xl font-bold text-gray-800 mb-2">SIAP</h1>
          <p className="text-sm text-gray-600">Sistem Informasi Administrasi dan Pelaporan</p>
        </div>

        <div className="bg-white rounded-b-3xl shadow-2xl p-8">
          <h2 className="text-3xl font-bold text-center text-gray-800 mb-8">LOGIN</h2>
          
          <div className="space-y-6">
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2 text-center">
                USERNAME
              </label>
              <input
                type="text"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                className="w-full px-4 py-3 bg-gray-100 rounded-full text-center font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="M. SONY"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2 text-center">
                Password
              </label>
              <div className="relative">
                <input
                  type={showPassword ? "text" : "password"}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  onKeyPress={(e) => e.key === 'Enter' && handleSubmit()}
                  className="w-full px-4 py-3 bg-gray-100 rounded-full text-center font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="••••••••••"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                  {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                </button>
              </div>
            </div>

            <button
              onClick={handleSubmit}
              className="w-32 mx-auto block bg-gradient-to-r from-blue-900 to-blue-800 text-white font-bold py-3 px-6 rounded-full hover:from-blue-800 hover:to-blue-700 transition-all shadow-lg hover:shadow-xl"
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