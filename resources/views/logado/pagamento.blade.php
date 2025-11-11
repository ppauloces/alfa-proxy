@extends('logado.partials.app')

@section('content')

<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

$amount = $_GET['valor'];

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.aproveipay.com.br/v1/transactions",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
    'paymentMethod' => 'pix',
    'customer' => [
        'document' => [
                'type' => 'cpf',
                'number' => '08623407563'
        ],
        'name' => 'SIDNEY APARECIDO LUCIO DE SOUZA',
        'email' => 'teste@gmail.com',
        'phone' => '21999999999'
    ],
    'amount' => $amount * 100,
    'installments' => '0',
    'items' => [
        [
                'title' => 'Produto',
                'unitPrice' => $amount * 100,
                'quantity' => 1,
                'tangible' => false
        ]
    ],
    'postbackUrl' => url('/api/postback/transacao')
  ]),
  CURLOPT_HTTPHEADER => [
    "accept: application/json",
    "authorization: Basic c2tfY21JaWZER0xVQlJyVnpPVUFhbFJUaDlsMFNWS2hmZ3FFRGVwaVU5VTlyX0h5U1RmOg==",
    "content-type: application/json"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {

}

$data = json_decode($response, true); // true makes it an associative array

// Access the qrcode value
$qrcode = $data['pix']['qrcode'];
$valor = $data['amount'] / 100;
$transacao = $data['id'];

Transaction::create([
    'user_id' => Auth::user()->id,
    'transacao' => $transacao,
    'valor' => $valor,
    'status' => 0,
]);


?>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af',
                        dark: '#1f2937',
                        light: '#f9fafb',
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar {
            transition: all 0.3s ease;
        }
        
        .sidebar-link {
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }
        
        .sidebar-link.active {
            background-color: rgba(59, 130, 246, 0.2);
            border-left: 3px solid #3b82f6;
        }
        
        .dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .dropdown-content.show {
            max-height: 500px;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                z-index: 40;
                width: 80%;
                height: 100vh;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 30;
                display: none;
            }
            
            .overlay.show {
                display: block;
            }
        }
        
        .token-display {
            font-family: 'Courier New', monospace;
            background-color: #f3f4f6;
            border: 1px dashed #d1d5db;
            padding: 12px;
            border-radius: 6px;
            position: relative;
        }
        
        .copy-btn {
            position: absolute;
            right: 10px;
            top: 10px;
        }
        
        .api-method {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            margin-right: 8px;
        }
        
        .get-method {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .post-method {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .delete-method {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        /* Custom styles for PIX page */
        .amount-btn.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .pix-key-card {
            transition: all 0.2s ease;
        }
        
        .pix-key-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .pix-key-card.active {
            border-color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.05);
        }
        
        /* QR Code styles */
        .qr-code-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            margin: 0 auto;
        }
        
        .qr-code {
            width: 100%;
            height: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        
        .countdown-timer {
            font-size: 1.2rem;
            font-weight: bold;
            color: #3b82f6;
        }
        
        .pix-copy-code {
            background-color: #f3f4f6;
            border: 1px dashed #d1d5db;
            padding: 15px;
            border-radius: 8px;
            position: relative;
            word-break: break-all;
            font-family: 'Courier New', monospace;
        }
    </style>

            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2 md:mb-0">Pagamento via PIX</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-600">Saldo atual:</span>
                        <span class="font-bold text-blue-600">R$ 0.00</span>
                    </div>
                </div>
                
                <!-- Payment Status -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 p-2 rounded-full">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">Aguardando pagamento</h3>
                            <p class="text-sm text-gray-600">Tempo restante: <span class="countdown-timer">29:59</span></p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column - QR Code -->
                    <div>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Pague escaneando o QR Code</h3>
                            <p class="text-sm text-gray-600">Abra o aplicativo do seu banco, acesse a opção PIX e escaneie o código abaixo:</p>
                        </div>
                        
                        <!-- QR Code Container -->
                        <div class="qr-code-container">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo $qrcode ?>" alt="QR Code PIX" class="qr-code">
                            
                            <div class="mt-4 text-center">
                                <p class="text-lg font-bold text-gray-800">R$ <?php echo $valor ?></p>
                                <p class="text-sm text-gray-600">Valor do pagamento</p>
                            </div>
                        </div>
                        
                        <!-- Instructions -->
                        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">Como pagar com QR Code:</h4>
                            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                <li>Abra o aplicativo do seu banco</li>
                                <li>Acesse a área PIX</li>
                                <li>Selecione "Pagar com QR Code"</li>
                                <li>Aponte a câmera para o código acima</li>
                                <li>Confirme o pagamento</li>
                            </ol>
                        </div>
                    </div>
                    
                    <!-- Right Column - Copy/Paste Code -->
                    <div>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Ou copie o código PIX</h3>
                            <p class="text-sm text-gray-600">Se preferir, copie o código abaixo e cole no aplicativo do seu banco:</p>
                        </div>
                        
                        <!-- PIX Copy Code -->
                        <div class="pix-copy-code mb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Código PIX:</p>
                                    <p class="text-sm font-mono break-all"><?php echo $qrcode ?></p>
                                </div>
                                <button class="copy-btn text-blue-500 hover:text-blue-700" data-code="<?php echo $qrcode ?>">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Copy Instructions -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h4 class="font-medium text-gray-800 mb-2">Como pagar com código copia e cola:</h4>
                            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                <li>Clique no botão "Copiar código" acima</li>
                                <li>Abra o aplicativo do seu banco</li>
                                <li>Acesse a área PIX</li>
                                <li>Selecione "Pagar com código PIX"</li>
                                <li>Cole o código copiado e confirme o pagamento</li>
                            </ol>
                        </div>
                        
                        <!-- Payment Details -->
                <!--        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-3">Detalhes do pagamento</h4>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Valor:</span>
                                    <span class="font-medium">R$ 100,00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Beneficiário:</span>
                                    <span class="font-medium">EMPRESA LTDA</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Chave PIX:</span>
                                    <span class="font-medium">a1b2c3d4-e5f6-g7h8-i9j0-k1l2m3n4o5p6</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Identificador:</span>
                                    <span class="font-medium">PAG-123456</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                                    <span class="text-gray-600">Expira em:</span>
                                    <span class="font-medium text-blue-600">30 minutos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                
                <!-- Important Notes -->
                <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <div class="bg-yellow-100 p-2 rounded-full">
                            <i class="fas fa-exclamation-circle text-yellow-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 mb-1">Importante</h4>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                <li>O pagamento pode levar alguns minutos para ser confirmado</li>
                                <li>O saldo será creditado automaticamente após a confirmação</li>
                                <li>Em caso de dúvidas, entre em contato com nosso suporte</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

    <script>
        // Toggle mobile sidebar
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
        
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
        
        // Toggle dropdown menus
        const ordersDropdownBtn = document.getElementById('ordersDropdownBtn');
        const ordersDropdown = document.getElementById('ordersDropdown');
        
        ordersDropdownBtn.addEventListener('click', () => {
            ordersDropdown.classList.toggle('show');
            ordersDropdownBtn.querySelector('i.fa-chevron-down').classList.toggle('transform');
            ordersDropdownBtn.querySelector('i.fa-chevron-down').classList.toggle('rotate-180');
        });
        
        // Toggle notification dropdown
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });
        
        // Toggle user menu dropdown
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenuDropdown = document.getElementById('userMenuDropdown');
        
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('hidden');
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
            
            if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                userMenuDropdown.classList.add('hidden');
            }
        });
        
        // Active sidebar link
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                sidebarLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                
                // Close sidebar on mobile after clicking a link
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });
        });
        
        // Countdown timer
        let timeLeft = 30 * 60; // 30 minutes in seconds
        const countdownElement = document.querySelector('.countdown-timer');
        
        function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                countdownElement.textContent = "Expirado";
                countdownElement.classList.remove('text-blue-600');
                countdownElement.classList.add('text-red-600');
            } else {
                timeLeft--;
            }
        }
        
        const countdownInterval = setInterval(updateCountdown, 1000);
        updateCountdown();
        
        // Copy PIX code
        const copyButtons = document.querySelectorAll('.copy-btn');
        
        copyButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const code = button.dataset.code || button.parentElement.dataset.code;
                
                // Copy to clipboard
                navigator.clipboard.writeText(code).then(() => {
                    // Change icon temporarily
                    const icon = button.querySelector('i');
                    icon.classList.remove('fa-copy');
                    icon.classList.add('fa-check');
                    
                    // Show tooltip
                    const tooltip = document.createElement('div');
                    tooltip.className = 'absolute -top-8 right-0 bg-gray-800 text-white text-xs px-2 py-1 rounded';
                    tooltip.textContent = 'Copiado!';
                    button.appendChild(tooltip);
                    
                    // Remove tooltip after 2 seconds
                    setTimeout(() => {
                        tooltip.remove();
                        icon.classList.remove('fa-check');
                        icon.classList.add('fa-copy');
                    }, 2000);
                });
            });
        });
        
    </script>

    <script>
  const url = "{{ url('/api/transacao/' . $transacao) }}";
  const intervalo = 5000; // 5 segundos
  let intervaloID = null;

  async function verificarStatus() {
    try {
      const resposta = await fetch(url);
      if (!resposta.ok) {
        console.error('Erro na requisição:', resposta.status);
        return;
      }

      const dados = await resposta.json();
      if (dados.status === 1) {
        clearInterval(intervaloID);
        window.location.href = "{{ url('/dashboard') }}";
      }
    } catch (erro) {
      console.error('Erro ao processar a resposta:', erro);
    }
  }

  // Inicia o polling
  intervaloID = setInterval(verificarStatus, intervalo);
</script>


@endsection