import React from "react";

export default function Home({ username }) {
  return (
    <div className="home-container">
      <div className="home-header">
        <button className="home-btn">HOME</button>
        <div className="home-icons">
          <span>🛡️</span>
          <span>🔔</span>
          <span>👤</span>
        </div>
      </div>

      <div className="home-body">
        <div className="welcome-box">
          <p>SELAMAT DATANG,</p>
          <h3>{username}</h3>
          <div className="clock">17:00</div>
        </div>

        <div className="menu-buttons">
          {["PRESENSI", "PATROLI", "KENDARAAN", "TAMU", "BARANG", "GANGGUAN KAMTIBMAS"].map((item) => (
            <button key={item} className="menu-btn">
              {item} &gt;&gt;
            </button>
          ))}
        </div>
      </div>

      <footer className="footer">Siap v 1.0.0</footer>
    </div>
  );
}
