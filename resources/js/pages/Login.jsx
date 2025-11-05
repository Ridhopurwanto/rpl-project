import React, { useState } from "react";

export default function Login({ onLogin }) {
  const [showPassword, setShowPassword] = useState(false);

  return (
    <div className="login-container">
      <div className="login-card">
        <div className="logo-section">
          <div className="logo-shield">S</div>
          <h2 className="logo-text">SIAP</h2>
          <p className="logo-subtext">Sistem Informasi Administrasi dan Pelaporan</p>
        </div>

        <h3 className="login-title">LOGIN</h3>

        <div className="input-group">
          <label>USERNAME</label>
          <input type="text" value="M. SONY" readOnly />
        </div>

        <div className="input-group">
          <label>Password</label>
          <div className="password-wrapper">
            <input type={showPassword ? "text" : "password"} value="password" readOnly />
            <span
              className="toggle-eye"
              onClick={() => setShowPassword(!showPassword)}
            >
              {showPassword ? "🙈" : "👁️"}
            </span>
          </div>
        </div>

        <button className="btn-login" onClick={onLogin}>
          MASUK
        </button>
      </div>
    </div>
  );
}