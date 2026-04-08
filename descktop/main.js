const { app, BrowserWindow, Menu, ipcMain, screen, dialog } = require('electron');
const path = require('path');
const fs = require('fs');
const axios = require('axios');

// Configure connection to local server Matriz
const MATRIZ_URL = 'http://localhost'; // Em Produção, isso viria de um .env ou input

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
            
            // Verifica se o arquivo local já existe (Evita re-download e buffering)
            if (!fs.existsSync(filePath)) {
                console.log(`Baixando mídia para Standby: ${media.url}`);
                const writer = fs.createWriteStream(filePath);
                const req = await axios.get(media.url, { responseType: 'stream' });
                req.data.pipe(writer);
                await new Promise((resolve, reject) => {
                    writer.on('finish', resolve);
                    writer.on('error', reject);
                });
            }
            
            cachedFiles.push({
                type: media.type,
                file_path: filePath,
                duration_seconds: media.duration_seconds
            });
        }
        
        return cachedFiles;

    } catch (e) {
        console.log('API Indisponível ou sem rede. Fallback para cache local...', e.message);
        // Fallback reading existing directory
        const cacheDir = path.join(appDataPath, 'SignageCache');
        if (fs.existsSync(cacheDir)) {
            const files = fs.readdirSync(cacheDir);
            return files.map(f => ({
                type: f.endsWith('.mp4') ? 'VIDEO' : 'IMAGE',
                file_path: path.join(cacheDir, f),
                duration_seconds: 10
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
        fullscreen: true,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true
        }
    });

    // Injeta script local para forçar captura de mouse mesmo dentro da WebView WebHost
    mainWindow.webContents.on('cursor-changed', () => resetIdleTimer());
    mainWindow.webContents.on('before-input-event', () => resetIdleTimer());

    mainWindow.loadURL(`${MATRIZ_URL}`); // Abre a tela de Login do ERP
    
    const template = [
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
