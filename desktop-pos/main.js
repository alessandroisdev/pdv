const { app, BrowserWindow, Menu, ipcMain, screen, dialog } = require('electron');
const path = require('path');
const fs = require('fs');
const axios = require('axios');

// Configure connection to local server Matriz
const MATRIZ_URL = 'http://localhost:8000'; // Em Produção, isso viria de um .env ou input

let mainWindow;
let standbyWindow;
let idleTimer;
let inactivityTimeout = 60000; // 60 segundos padrão, atualizado via API
let appDataPath = '';

// Variáveis de Configuração Local
let localSettings = {
    fiscalPrinterIP: '',
    nonFiscalPrinterIP: ''
};

function loadLocalSettings() {
    try {
        const settingsPath = path.join(app.getPath('userData'), 'printerSettings.json');
        if (fs.existsSync(settingsPath)) {
            const data = fs.readFileSync(settingsPath, 'utf8');
            localSettings = JSON.parse(data);
        }
    } catch (e) {
        console.error('Erro ao ler configs da impressora', e);
    }
}

function saveLocalSettings(settings) {
    try {
        localSettings = { ...localSettings, ...settings };
        const settingsPath = path.join(app.getPath('userData'), 'printerSettings.json');
        fs.writeFileSync(settingsPath, JSON.stringify(localSettings));
    } catch (e) {
        console.error('Erro ao salvar configs', e);
    }
}

async function fetchSignageConfig() {
    try {
        const response = await axios.get(`${MATRIZ_URL}/api/v1/signage`);
        const { timeout_seconds, medias } = response.data;
        inactivityTimeout = timeout_seconds * 1000;
        
        // Cachear as mídias localmente!
        const cacheDir = path.join(appDataPath, 'SignageCache');
        if (!fs.existsSync(cacheDir)) {
            fs.mkdirSync(cacheDir, { recursive: true });
        }

        const cachedFiles = [];

        for (let media of medias) {
            const fileName = path.basename(media.url);
            const filePath = path.join(cacheDir, fileName);
            let finalUri = '';
            
            // Usa .tmp para evitar que o player tente ler arquivos não finalizados e corrompa a execução
            if (!fs.existsSync(filePath)) {
                console.log(`Baixando mídia para Standby: ${media.url}`);
                const tmpPath = filePath + '.tmp';
                try {
                    const writer = fs.createWriteStream(tmpPath);
                    const req = await axios.get(media.url, { responseType: 'stream', timeout: 30000 });
                    req.data.pipe(writer);
                    await new Promise((resolve, reject) => {
                        writer.on('finish', resolve);
                        writer.on('error', reject);
                    });
                    fs.renameSync(tmpPath, filePath); // Renomeia atômicamente ao finalizar download
                    finalUri = 'file://' + filePath;
                } catch (err) {
                    console.error(`Erro ao baixar ${media.url}:`, err.message);
                    if (fs.existsSync(tmpPath)) fs.unlinkSync(tmpPath);
                    finalUri = media.url; // Resiliência: em caso de falha de download, tenta streaming
                }
            } else {
                finalUri = 'file://' + filePath;
            }
            
            cachedFiles.push({
                type: media.type,
                file_path: finalUri,
                duration_seconds: media.duration_seconds || 15
            });
        }
        
        return cachedFiles;

    } catch (e) {
        console.log('API Indisponível ou sem rede. Fallback para cache local...', e.message);
        // Fallback reading existing directory
        const cacheDir = path.join(appDataPath, 'SignageCache');
        if (fs.existsSync(cacheDir)) {
            const files = fs.readdirSync(cacheDir);
            return files.filter(f => !f.endsWith('.tmp')).map(f => ({
                type: (f.endsWith('.mp4') || f.endsWith('.webm')) ? 'VIDEO' : 'IMAGE',
                file_path: 'file://' + path.join(cacheDir, f),
                duration_seconds: 15
            }));
        }
        return [];
    }
}

function resetIdleTimer() {
    if (idleTimer) clearTimeout(idleTimer);
    
    // Se a janela de Standby estiver visível e o mouse for mexido, esconda-a!
    if (standbyWindow && standbyWindow.isVisible()) {
        standbyWindow.hide();
        mainWindow.focus();
    }
    
    idleTimer = setTimeout(() => {
        showStandbyScreen();
    }, inactivityTimeout);
}

async function showStandbyScreen() {
    console.log(`Entrando em modo Standby (Sinalização Digital Offline ativada). Ocioso por ${inactivityTimeout/1000}s`);
    const cachedMedias = await fetchSignageConfig();
    
    if (cachedMedias.length > 0 && standbyWindow) {
        // Envia as URLs locais `file://` para o renderizador HTML sem depender de internet!
        standbyWindow.webContents.send('load-signage-medias', cachedMedias);
        standbyWindow.show();
        standbyWindow.setFullScreen(true);
    }
}

function createStandbyWindow() {
    const { width, height } = screen.getPrimaryDisplay().workAreaSize;
    standbyWindow = new BrowserWindow({
        width, height,
        show: false, // Escondida inicialmente
        frame: false,
        kiosk: true,
        alwaysOnTop: true,
        webPreferences: {
            nodeIntegration: true,
            contextIsolation: false
        }
    });

    standbyWindow.loadFile('standby.html');
    
    // Mouse movement inside standby forces close
    standbyWindow.webContents.on('before-input-event', () => resetIdleTimer());
    standbyWindow.on('mousemove', () => resetIdleTimer());
}

function createWindow() {
    appDataPath = app.getPath('userData');
    loadLocalSettings();

    const { width, height } = screen.getPrimaryDisplay().workAreaSize;
    
    mainWindow = new BrowserWindow({
        width,
        height,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        }
    });

    // Inicia a janela maximizada (com barra nativa do SO exibindo botões de Minimizar, Restaurar e Fechar)
    mainWindow.maximize();

    // Injeta script local para forçar captura de mouse mesmo dentro da WebView WebHost
    mainWindow.webContents.on('cursor-changed', () => resetIdleTimer());
    mainWindow.webContents.on('before-input-event', () => resetIdleTimer());

    mainWindow.loadURL(`${MATRIZ_URL}/terminal`); // Abre a tela de Login do ERP
    
    const template = [
        {
            label: 'Exibição',
            submenu: [
                { role: 'togglefullscreen', label: 'Tela Cheia (F11)' },
                { type: 'separator' },
                { role: 'minimize', label: 'Minimizar' },
                { role: 'zoom', label: 'Maximizar / Restaurar' },
                { role: 'close', label: 'Fechar Aba' }
            ]
        },
        {
            label: 'Configurações de Hardware',
            submenu: [
                {
                    label: 'Configurar Impressoras (TCP/IP)',
                    click: () => {
                        dialog.showMessageBox(mainWindow, {
                            type: 'info',
                            title: 'Configurações Nativas',
                            message: 'Use a API de Módulos Fiscais para configurar a impressora no back-end, ou injete a lógica local!',
                        });
                    }
                },
                { type: 'separator' },
                { role: 'quit', label: 'Encerrar Caixa (Sair)' }
            ]
        }
    ];

    const menu = Menu.buildFromTemplate(template);
    Menu.setApplicationMenu(menu);

    createStandbyWindow();
    fetchSignageConfig().then(() => resetIdleTimer());
}

app.whenReady().then(createWindow);

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

// IPCListeners
ipcMain.on('user-activity', () => {
    resetIdleTimer();
});

ipcMain.on('window-minimize', () => {
    if (mainWindow) mainWindow.minimize();
});

ipcMain.on('window-maximize-restore', () => {
    if (mainWindow) {
        if (mainWindow.isMaximized()) {
            mainWindow.restore();
        } else {
            mainWindow.maximize();
        }
    }
});

ipcMain.on('window-toggle-fullscreen', () => {
    if (mainWindow) {
        mainWindow.setFullScreen(!mainWindow.isFullScreen());
    }
});
