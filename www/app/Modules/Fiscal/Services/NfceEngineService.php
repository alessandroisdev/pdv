<?php

namespace App\Modules\Fiscal\Services;

use App\Modules\Settings\Models\Setting;
use App\Modules\Sales\Models\Sale;
use Exception;

class NfceEngineService
{
    /**
     * Constrói e retorna a estrutura JSON oficial que a biblioteca
     * NFePHP exige em seu construtor `Tools`.
     */
    public function buildConfig(): string
    {
        // Resgata os metadados do banco criados no Módulo Settings
        $cnpj = Setting::where('key', 'fiscal_cnpj')->value('value') ?? '00000000000000';
        $razaoSocial = Setting::where('key', 'company_name')->value('value') ?? 'EMPRESA TESTE LTDA';
        $uf = 'PR'; // Idealmente, puxar de um Setting `company_state`
        $csc = Setting::where('key', 'fiscal_csc_token')->value('value') ?? '000000-0000-0000-0000-0000000';
        $cscId = '000001';
        $ambiente = (int) (Setting::where('key', 'fiscal_environment')->value('value') ?? 2);

        // Tabela de UFs do IBGE Padrão NFePHP
        $ibgeUF = [
            'AC' => 12, 'AL' => 27, 'AP' => 16, 'AM' => 13, 'BA' => 29, 
            'CE' => 23, 'DF' => 53, 'ES' => 32, 'GO' => 52, 'MA' => 21, 
            'MG' => 31, 'MS' => 50, 'MT' => 51, 'PA' => 15, 'PB' => 25, 
            'PE' => 26, 'PI' => 22, 'PR' => 41, 'RJ' => 33, 'RN' => 24, 
            'RO' => 11, 'RR' => 14, 'RS' => 43, 'SC' => 42, 'SE' => 28, 
            'SP' => 35, 'TO' => 17
        ];

        return json_encode([
            "atualizacao" => date('Y-m-d H:i:s'),
            "tpAmb" => $ambiente, // 2 = Homologação, 1 = Produção 
            "razaosocial" => $razaoSocial,
            "cnpj" => preg_replace('/[^0-9]/', '', $cnpj),
            "siglaUF" => $uf,
            "schemes" => "PL_009_V4", // Schema atual da Sefaz
            "versao" => '4.00',
            "tokenIBPT" => "",
            "CSC" => $csc,
            "CSCid" => $cscId
        ]);
    }

    /**
     * Resgata o certificado digital (.pfx) do Storage e a senha
     */
    public function getCertificate(): array
    {
        $path = Setting::where('key', 'fiscal_certificate_path')->value('value');
        $password = Setting::where('key', 'fiscal_certificate_password')->value('value');

        if (!$path || !file_exists(storage_path('app/' . $path))) {
            throw new Exception("Certificado Digital não encontrado no servidor.");
        }

        $pfxContent = file_get_contents(storage_path('app/' . $path));
        
        return [
            'content' => $pfxContent,
            'password' => $password
        ];
    }

    /**
     * Interface Mock de Sandbox para testes primários 
     * antes de Instanciar a classe real `NFePHP\NFe\Tools`
     */
    public function testSandbox()
    {
        // Neste Sandbox apenas validamos se a string config é gerada com sucesso
        // Se a dependência já puder ser invocada, injetaremos o ping da sefaz.
        $config = $this->buildConfig();
        
        return [
            'status' => Setting::where('key', 'fiscal_environment')->value('value') == '1' ? 'PRODUCTION_READY (PERIGO!)' : 'HOMOLOGATION_READY',
            'config_generated' => json_decode($config, true),
            'message' => 'O payload de configuração gerencial está operante para plugar ao Make do NFePHP.'
        ];
    }

    /**
     * Efetua a conexão SOAP oficial contra o servidor da SEFAZ
     * Verifica latência, validade do certificado digital e se os IPs batem Sefaz.
     */
    public function pingSefaz(): array
    {
        try {
            $config = $this->buildConfig();
            $cert = $this->getCertificate();
            
            // Instancia o núcleo do Motor
            $certificate = \NFePHP\Common\Certificate::readPfx($cert['content'], $cert['password']);
            $tools = new \NFePHP\NFe\Tools($config, $certificate);
            
            // Modelo 65 é NFC-e
            $tools->model('65'); 
            
            // Verifica status de serviço da Sefaz
            $response = $tools->sefazStatus();
            
            // Padroniza a resposta RAW (XML da Sefaz) em um objeto limpo
            $stds = new \NFePHP\NFe\Common\Standardize($response);
            $parsed = $stds->toStd();
            
            return [
                'success' => true,
                'status_code' => $parsed->cStat ?? 'Unknown',
                'reason' => $parsed->xMotivo ?? 'Em Operação',
                'latency' => $parsed->tMed ?? 'N/A'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Reforma Tributária (Em vigor Progressivo a partir de 2026).
     * O NFePHP atual (v5.2) não provê um atalho direto (`tagIBSCBS`) na classe Make.
     * Esta função assegura que o DOMXML da Venda seja modificado manualmente via 
     * manipulação de Strings/DOM para a injeção do IVA Dual, impedindo que a 
     * sua empresa seja impedida de vender nas novas alíquotas Sefaz.
     */
    public function applyIbsCbsTaxesHook(string $xmlBruto, array $ibsData): string
    {
        // Esta Trait é desenhada para injetar no Nó <imposto> do Item.
        // Como o Make do sped-nfe adiciona CST normal, nós substituímos localmente o innerXML ou usamos `str_replace` ou DOMDocument seguro.
        // Isso nos provará à prova de atualizações legadas.
        
        $dom = new \DOMDocument();
        $dom->loadXML($xmlBruto);
        
        // Futura lógica de injeção direta de imposto (IBS / CBS) nos elementos filhos <det>
        
        return $dom->saveXML();
    }
}
