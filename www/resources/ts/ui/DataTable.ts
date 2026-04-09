import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';

// Captura do Token CSRF Nativo do Laravel Header
function getCsrfToken(): string {
    const el = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
    return el ? el.content : '';
}

export interface DataTableColumnConfig {
    data: string;
    name?: string;
    render?: (data: any, type: string, row: any, meta: any) => string;
    orderable?: boolean;
    searchable?: boolean;
    className?: string;
}

export class AppServerTable {
    private tableInstance: typeof DataTable | null = null;
    private selector: string;

    constructor(selector: string, ajaxUrl: string, columns: DataTableColumnConfig[], defaultOrder: any[] = [[0, 'desc']]) {
        this.selector = selector;
        this.initTable(ajaxUrl, columns, defaultOrder);
    }

    private initTable(ajaxUrl: string, columns: DataTableColumnConfig[], defaultOrder: any[]): void {
        const el = document.querySelector(this.selector);
        if(!el) return;

        // @ts-ignore
        this.tableInstance = new DataTable(this.selector, {
            serverSide: true,
            processing: true,
            responsive: true,
            ajax: {
                url: ajaxUrl,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                error: (xhr: any, error: any, code: any) => {
                    console.error("DataTable Error:", error, code, xhr.responseText);
                    alert("Erro interno na listagem de dados (Sessão T expirada ou Falha de DB). Recarregue a página.");
                }
            },
            columns: columns,
            order: defaultOrder,
            language: {
                processing:     "<div class='spinner-pulse'>Carregando registros...</div>",
                search:         "Pesquisar:",
                lengthMenu:     "Exibir _MENU_ linhas",
                info:           "Mostrando _START_ a _END_ de _TOTAL_ linhas",
                infoEmpty:      "Sem registros",
                infoFiltered:   "(filtrado de _MAX_ registros totais)",
                emptyTable:     "Nenhuma informação nesta tabela.",
                zeroRecords:    "Nenhum registro correspondente encontrado"
            },
            // Estilização injetada pro Design System nativo não quebrar
            createdRow: function (row: any, data: any, dataIndex: any) {
                row.classList.add('transition', 'hover:bg-slate-50');
                row.style.borderBottom = '1px solid #f1f5f9';
            }
        });
    }

    public reload() {
        if(this.tableInstance) {
            // @ts-ignore
            this.tableInstance.ajax.reload(null, false);
        }
    }
}

declare global {
    interface Window {
        AppServerTable: typeof AppServerTable;
    }
}

window.AppServerTable = AppServerTable;
