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
    console.log("Recebida Ordem de Impressão de Cupom");

    return new Promise((resolve) => {
        // Criação da Janela Fantasma para Renderizar o HTML do Recibo em C++ Runtime
        let workerWindow = new BrowserWindow({
            show: false, // Oculte do usuário final - Operação em Retaguarda
            webPreferences: {
                nodeIntegration: true,
                contextIsolation: false
            }
        });

        workerWindow.loadFile('receipt.html');

        workerWindow.webContents.on('did-finish-load', () => {
            // Emite o payload (JSON dos itens) para dentro da Janela HTML para atualizar as TRs
            const jsInjection = `window.fillReceipt(${JSON.stringify(data)});`;
            
            workerWindow.webContents.executeJavaScript(jsInjection).then(() => {
                // Acionar a Porta USB/Comando de Spooler Padrão do Windows Silenciosamente
                workerWindow.webContents.print({ 
                    silent: true, 
                    printBackground: true, 
                    deviceName: '', // Envia pra default impressora de Cúpom
                    margins: { marginType: 'none' } 
                }, (success, failureReason) => {
                    if (!success) console.log("Spooler rejeitou a impressão: ", failureReason);
                    workerWindow.close(); // Destroi a janela fantasma liberando Ram
                    resolve({ success: success, message: failureReason || 'OK' });
                });
            });
        });
    });
});
