const { app, BrowserWindow, ipcMain } = require('electron');
const path = require('path');

let mainWindow;

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1280,
        height: 800,
        minWidth: 1024,
        minHeight: 768,
        title: 'Debras PDV Enterprise',
        backgroundColor: '#1e293b',
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        }
    });

    // Em Produção você pode descomentar para tela cheia e kiosk:
    // mainWindow.setFullScreen(true);

    mainWindow.loadFile('index.html');
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

// Registrar comandos IPC (Ponto de Fuga para Sistema Operacional Ex: Impressão Serial)
ipcMain.handle('print-receipt', async (event, data) => {
    console.log("Recebida Ordem de Impressão de Cupom (Emulação RAW)");
    // A implementação nativa chamaria as rotinas ESC/POS USB daqui, para o modelo Bematech/Epson
    return { success: true, message: 'Impresso com Sucesso na COM3' };
});
