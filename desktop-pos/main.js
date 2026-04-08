const { app, BrowserWindow, globalShortcut } = require('electron');
const path = require('path');

let mainWindow;

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1280,
    height: 800,
    kiosk: true, // Modo Quiosque/Full Screen para Frente de Caixa
    autoHideMenuBar: true,
    webPreferences: {
      nodeIntegration: false,
      contextIsolation: true
    },
    icon: path.join(__dirname, 'icon.ico') // Caso possua no futuro
  });

  // URL Raiz do ERP, supondo localhost (pode ser configurado via prompt futuramente)
  const serverUrl = 'http://localhost/vendas/pdv/terminal';
  
  mainWindow.loadURL(serverUrl).catch(err => {
    // Tela de Falha caso o Docker Server do Laravel esteja Desligado
    mainWindow.loadURL(`data:text/html;charset=utf-8,
      <html>
        <body style="background: #0f172a; color: #fff; font-family: sans-serif; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh;">
          <h1 style="color: #ef4444;">Falha de Conexão com Servidor ERP</h1>
          <p>Verifique se o Docker/Servidor Laravel está rodando em http://localhost</p>
          <button onclick="window.location.reload()" style="padding: 1rem 2rem; background: #3b82f6; border: none; color: white; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 1rem;">Tentar Novamente</button>
        </body>
      </html>
    `);
  });

  // Hotkey Especial do Gerente (Ex: Escapar do FullScreen e Fechar)
  globalShortcut.register('CommandOrControl+Shift+Q', () => {
    app.quit();
  });
}

app.whenReady().then(() => {
  createWindow();

  app.on('activate', function () {
    if (BrowserWindow.getAllWindows().length === 0) createWindow();
  });
});

app.on('window-all-closed', function () {
  if (process.platform !== 'darwin') app.quit();
});
