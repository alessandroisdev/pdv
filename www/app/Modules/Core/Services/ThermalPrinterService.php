<?php

namespace App\Modules\Core\Services;

use App\Modules\Settings\Models\Setting;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Exception;

class ThermalPrinterService
{
    private $printerIp;
    private $printerPort;
    private $printerWidth;
    
    public function __construct()
    {
        // Resgatar dados de hardware das Configurações
        $this->printerIp = Setting::where('key', 'pos_printer_ip')->value('value');
        $this->printerWidth = Setting::where('key', 'pos_printer_width')->value('value') ?? '80mm';
        
        // Padrão de porta RAW Ethernet Epson/Elgin
        $this->printerPort = 9100;
    }

    /**
     * Inicia a conexão com a Impressora respeitando o Conector correto.
     * 
     * Se o usuário registrar algo com formato IP (192.168...), usaremos NetworkPrintConnector.
     * Caso contrário, tentaremos o WindowsPrintConnector local.
     */
    private function getConnector()
    {
        if (!$this->printerIp) {
            throw new Exception("IP ou Rota da impressora não está configurado. Vá em Configurações Globais.");
        }

        // Simula uma tentativa limite de 3 segundos para não travar a UI (Timeout nativo do Mike42 se não tiver socket pode congelar o script, 
        // mas faremos um ping nativo ou fsockopen() antes para assegurar estabilidade)
        $sock = @fsockopen($this->printerIp, $this->printerPort, $errCode, $errStr, 2);
        if (!$sock) {
            throw new Exception("A Impressora Térmica ($this->printerIp:$this->printerPort) está Desligada ou inalcançável na rede.");
        }
        fclose($sock);

        return new NetworkPrintConnector($this->printerIp, $this->printerPort);
    }

    /**
     * Interface Sandbox para testar o guincho térmico.
     */
    public function testConnection(): array
    {
        try {
            $connector = $this->getConnector();
            $printer = new Printer($connector);
            
            // Centraliza e Imprime Relatório
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text("SUCESSO!\n");
            $printer->setTextSize(1, 1);
            $printer->text("--------------------------------\n");
            $printer->text("GestãoPDV ERP\n");
            $printer->text("A Impressora esta Operante.\n");
            $printer->text("IP: " . $this->printerIp . "\n");
            $printer->text("Papel: " . $this->printerWidth . "\n");
            $printer->text("--------------------------------\n\n\n");
            
            // Acionar Gaveta
            $printer->pulse();
            // Cortar Papel
            $printer->cut();
            // Desligar interface local
            $printer->close();

            return [
                'success' => true,
                'message' => 'Bobina acionada e gaveta disparada com sucesso!'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Imprime o Recibo Fiscal (DANFE NFC-e) ou Auxiliar (Contingência)
     */
    public function printReceipt(\App\Modules\Sales\Models\Sale $sale, $fiscalDoc = null)
    {
        try {
            $connector = $this->getConnector();
            $printer = new Printer($connector);
            
            $cols = $this->printerWidth === '58mm' ? 32 : 48; // 80mm = 48 columns typically

            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text((Setting::where('key', 'store_name')->value('value') ?? 'EMPRESA PADRAO') . "\n");
            $printer->setEmphasis(false);
            $printer->text((Setting::where('key', 'fiscal_cnpj')->value('value') ?? 'CNPJ: 00.000.000/0001-00') . "\n");
            $printer->text("------------------------------------------------\n");
            
            if ($fiscalDoc && $fiscalDoc->status === 'AUTORIZADO') {
                $printer->text("DANFE NFC-e Documento Auxiliar\n");
            } else {
                $printer->setEmphasis(true);
                $printer->text("RECIBO DE VENDA (NAO FISCAL)\n");
                $printer->setEmphasis(false);
            }
            $printer->text("------------------------------------------------\n");

            // Itens
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("COD.   DESC.              QTD   UN    R$ Total\n");
            $printer->text("------------------------------------------------\n");
            foreach ($sale->items as $idx => $item) {
                // Formatting for 48 cols roughly: 
                // Line 1: Code and Name
                $printer->text(str_pad($item->product->id, 5, '0', STR_PAD_LEFT) . " " . substr($item->product->name, 0, 40) . "\n");
                // Line 2: Qty x Unit = Total
                $qtyStr = str_pad($item->quantity, 5, ' ', STR_PAD_LEFT);
                $unitStr = str_pad(format_money($item->unit_price_cents), 10, ' ', STR_PAD_LEFT);
                $totalStr = str_pad(format_money($item->unit_price_cents * $item->quantity), 12, ' ', STR_PAD_LEFT);
                $printer->text("       {$qtyStr} UN  x {$unitStr} {$totalStr}\n");
            }
            $printer->text("------------------------------------------------\n");

            // Total
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("TOTAL " . format_money($sale->total_cents) . "\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text("------------------------------------------------\n");

            // CRM
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            if ($sale->customer_document) {
                $printer->text("Consumidor: " . $sale->customer_document . "\n");
            } else {
                $printer->text("Consumidor Nao Identificado\n");
            }
            $printer->text("------------------------------------------------\n");

            // QrCode / Sefaz
            if ($fiscalDoc && $fiscalDoc->status === 'AUTORIZADO') {
                $printer->text("Chave de Acesso Sefaz:\n");
                $printer->text($fiscalDoc->protocol_number . "\n");
                
                // MOCK QR Code since real generation requires NFePHP Sefaz URL
                $printer->qrCode("https://sefaz.mock.gov.br/nfce?ch=" . $fiscalDoc->protocol_number, Printer::QR_ECLEVEL_L, 4);
                $printer->text("\nProtocolo de Autorizacao: " . $fiscalDoc->protocol_number . "\n");
            }
            
            // Footer
            $printer->text("\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $footer = Setting::where('key', 'pos_receipt_footer')->value('value') ?? 'Volte Sempre!';
            $printer->text(wordwrap($footer, $cols, "\n", true) . "\n");
            
            $printer->text("Data: " . date('d/m/Y H:i:s') . "\n");
            $printer->text("\n\n\n\n");
            
            $printer->pulse();
            $printer->cut();
            $printer->close();
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Thermal Printer Erro: " . $e->getMessage());
            return false;
        }
    }
}
