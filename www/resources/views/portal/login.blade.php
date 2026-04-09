<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Cliente - Acesso</title>
    <!-- Usando Tailwind CDN para o Portal isolado -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="h-screen flex items-center justify-center">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-indigo-50 rounded-full mb-4 text-indigo-600">
                <i class="fa fa-user-circle text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Portal do Cliente</h1>
            <p class="text-slate-500 text-sm mt-2">Acesse suas faturas, boletos e contratos.</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4 border border-red-100 text-center font-bold">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('portal.authenticate') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Seu CPF ou CNPJ</label>
                <input type="text" name="document" placeholder="Apenas números..." required 
                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                ACESSAR MEU PAINEL
            </button>
        </form>

        <div class="text-center mt-6 text-xs text-slate-400">
            Ambiente Seguro. As informações são criptografadas end-to-end.
        </div>
    </div>

</body>
</html>
