const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('pdvAPI', {
    printReceipt: (data) => ipcRenderer.invoke('print-receipt', data)
});
