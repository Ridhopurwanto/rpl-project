import React from "react";
import ReactDOM from "react-dom/client";
import "../css/app.css";
import Login from "./pages/Login";
import Home from "./pages/Home";

function App() {
  const [isLoggedIn, setIsLoggedIn] = React.useState(false);
  const [username, setUsername] = React.useState("M. SONY");

  return (
    <div>
      {!isLoggedIn ? (
        <Login onLogin={() => setIsLoggedIn(true)} />
      ) : (
        <Home username={username} />
      )}
    </div>
  );
}

ReactDOM.createRoot(document.getElementById("app")).render(<App />);
