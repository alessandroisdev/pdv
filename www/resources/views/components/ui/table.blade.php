<div class="table-responsive" style="width: 100%; overflow-x: auto;">
    <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 2px solid #e2e8f0; color: #455073; background-color: rgba(241, 245, 249, 0.4);">
                {{ $head }}
            </tr>
        </thead>
        <tbody>
            {{ $body ?? $slot }}
        </tbody>
    </table>
</div>

<style>
/* Tabelas de estilo Premium limitadas ao Blade encapsulado (Scope simulação) */
.table th { padding: 1rem 1.5rem; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
.table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
.table tbody tr:hover { background-color: rgba(96, 119, 192, 0.02); }
</style>
