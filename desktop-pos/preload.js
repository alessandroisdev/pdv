const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('electronAPI', {
    // Expondo as funções de controle de janela
    minimize: () => ipcRenderer.send('window-minimize'),
    maximizeRestore: () => ipcRenderer.send('window-maximize-restore'),
    toggleFullscreen: () => ipcRenderer.send('window-toggle-fullscreen'),
    
    // Outros eventos, se necessário no futuro
    userActivity: () => ipcRenderer.send('user-activity')
});
