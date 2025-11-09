<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }

    $username = $input['username'] ?? null;
    $password = $input['password'] ?? null;

    if ($username == 'admin' && $password == 'admin123') {
        $_SESSION['logged_in'] = true;
        echo json_encode(['redirect' => 'admin.php']);
        exit;
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
      }

      .container {
        width: 100%;
        max-width: 400px;
      }

      .card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        padding: 2rem;
      }

      .header {
        text-align: center;
        margin-bottom: 2rem;
      }

      .icon-circle {
        width: 64px;
        height: 64px;
        background: #1e293b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
      }

      .icon-circle svg {
        width: 32px;
        height: 32px;
        color: white;
      }

      h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
      }

      .subtitle {
        font-size: 0.875rem;
        color: #64748b;
      }

      .form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }

      .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }

      label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #475569;
      }

      .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
      }

      .input-wrapper svg {
        position: absolute;
        left: 0.75rem;
        width: 20px;
        height: 20px;
        color: #cbd5e1;
        pointer-events: none;
      }

      input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: all 0.2s;
      }

      input:focus {
        outline: none;
        border-color: transparent;
        box-shadow: 0 0 0 3px rgba(30, 41, 59, 0.1), 0 0 0 5px rgba(30, 41, 59, 0.05);
      }

      input::placeholder {
        color: #cbd5e1;
      }

      .error-message {
        display: none;
        gap: 0.5rem;
        align-items: flex-start;
        padding: 0.75rem;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 0.5rem;
        animation: slideDown 0.3s ease-out;
      }

      .error-message.show {
        display: flex;
      }

      .error-message svg {
        width: 20px;
        height: 20px;
        color: #dc2626;
        flex-shrink: 0;
        margin-top: 0.125rem;
      }

      .error-message p {
        font-size: 0.875rem;
        color: #991b1b;
      }

      @keyframes slideDown {
        from {
          opacity: 0;
          transform: translateY(-0.5rem);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      button {
        width: 100%;
        padding: 0.75rem 1rem;
        background: #1e293b;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        focus: outline;
      }

      button:hover:not(:disabled) {
        background: #0f172a;
      }

      button:focus-visible {
        outline: 2px solid #1e293b;
        outline-offset: 2px;
      }

      button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }

      .footer {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.875rem;
        color: #64748b;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="header">
          <div class="icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
          </div>
          <h1>Welcome Back</h1>
          <p class="subtitle">Please sign in to your account</p>
        </div>

        <form class="form" onsubmit="handleLogin(event)">
          <div class="form-group">
            <label for="username">Username</label>
            <div class="input-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <input
                id="username"
                type="text"
                placeholder="Enter your username"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <input
                id="password"
                type="password"
                placeholder="Enter your password"
                required
              />
            </div>
          </div>

          <div class="error-message" id="error">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <p id="error-text"></p>
          </div>

          <button type="submit" id="submit-btn">Sign In</button>
        </form>
      </div>

      <p class="footer">Protected by secure authentication</p>
    </div>

    <script>
      async function handleLogin(event) {
        event.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const errorDiv = document.getElementById('error');
        const errorText = document.getElementById('error-text');
        const submitBtn = document.getElementById('submit-btn');

        errorDiv.classList.remove('show');
        errorText.textContent = '';
        submitBtn.disabled = true;
        submitBtn.textContent = 'Signing in...';

        try {
          const response = await fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
          });

          const result = await response.json();
          if (result.redirect) {
            window.location.href = result.redirect;
          } else {
            errorText.textContent = result.error || 'Login failed';
            errorDiv.classList.add('show');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Sign In';
          }
        } catch (err) {
          errorText.textContent = 'An error occurred. Please try again.';
          errorDiv.classList.add('show');
          submitBtn.disabled = false;
          submitBtn.textContent = 'Sign In';
        }
      }
    </script>
  </body>
</html>


