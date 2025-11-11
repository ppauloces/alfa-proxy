{{-- resources/views/dashboard.blade.php --}}
@extends('logado.partials.app')

@section('title', 'Dashboard')

@section('content')

<div class="bg-white rounded-lg shadow p-4 md:p-6">
    <form action="/pagamento" method="get">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2 md:mb-0">Recarregar Saldo via PIX</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-600">Saldo atual:</span>
                        <span class="font-bold text-blue-600">R$ {{ $usuario->saldo }}</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Payment Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Instruções para pagamento</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Informe o valor que deseja recarregar</li>
                                <li>Escolha uma chave PIX para copiar</li>
                                <li>Clique em "Pagar com PIX" para ser redirecionado</li>
                                <li>O saldo será creditado automaticamente após o pagamento</li>
                            </ol>
                        </div>
                        
                        <!-- Amount Selection -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Valor da Recarga</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <button class="amount-btn bg-white border border-gray-300 rounded-lg p-3 text-center hover:border-blue-500 hover:text-blue-600 transition" data-amount="20">
                                    R$ 20,00
                                </button>
                                <button class="amount-btn bg-white border border-gray-300 rounded-lg p-3 text-center hover:border-blue-500 hover:text-blue-600 transition" data-amount="50">
                                    R$ 50,00
                                </button>
                                <button class="amount-btn bg-white border border-gray-300 rounded-lg p-3 text-center hover:border-blue-500 hover:text-blue-600 transition" data-amount="100">
                                    R$ 100,00
                                </button>
                                <button class="amount-btn bg-white border border-gray-300 rounded-lg p-3 text-center hover:border-blue-500 hover:text-blue-600 transition" data-amount="200">
                                    R$ 200,00
                                </button>
                                <button class="amount-btn bg-white border border-gray-300 rounded-lg p-3 text-center hover:border-blue-500 hover:text-blue-600 transition" data-amount="500">
                                    R$ 500,00
                                </button>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-500">R$</span>
                                    <input type="number" name="valor" id="customAmount" placeholder="Outro valor" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Amount Display -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 hidden" id="selectedAmountContainer">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-600">Valor selecionado:</p>
                                    <p class="text-xl font-bold text-blue-700" id="selectedAmountDisplay">R$ 0,00</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-800" id="clearAmount">
                                    <i class="fas fa-times"></i> Alterar valor
                                </button>
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- Right Column - Summary and Payment Button -->
                    <div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 sticky top-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Resumo do Pagamento</h3>
                            
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Valor:</span>
                                    <span class="font-medium" id="summaryAmount">R$ 0,00</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-3">
                                    <span class="text-gray-600">Total a pagar:</span>
                                    <span class="font-bold text-blue-700" id="summaryTotal">R$ 0,00</span>
                                </div>
                            </div>
                            
                            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <div class="flex items-start space-x-2">
                                    <i class="fas fa-exclamation-circle text-yellow-500 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-yellow-800 font-medium">Importante</p>
                                        <p class="text-xs text-yellow-700">O saldo será creditado automaticamente após a confirmação do pagamento.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg mt-6 font-medium disabled:opacity-50 disabled:cursor-not-allowed transition" id="payWithPix" disabled>
                                <i class="fas fa-qrcode mr-2"></i> Pagar com PIX
                            </button>
    </form>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Instructions -->
                <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Como pagar via PIX</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-mobile-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">1. Abra seu aplicativo de banco</h4>
                                <p class="text-sm text-gray-600 mt-1">Acesse a área PIX no aplicativo do seu banco ou instituição financeira.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-copy text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">2. Copie a chave PIX</h4>
                                <p class="text-sm text-gray-600 mt-1">Selecione uma chave acima e clique no ícone de copiar.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-check-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">3. Confirme o pagamento</h4>
                                <p class="text-sm text-gray-600 mt-1">Cole a chave no campo indicado e confirme o pagamento.</p>
                            </div>
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
        
        // PIX Payment Logic
        let selectedAmount = 0;
        let selectedPixKey = true;
        let selectedPixType = null;
        
        // Amount selection
        const amountButtons = document.querySelectorAll('.amount-btn');
        const customAmountInput = document.getElementById('customAmount');
        const selectedAmountContainer = document.getElementById('selectedAmountContainer');
        const selectedAmountDisplay = document.getElementById('selectedAmountDisplay');
        const summaryAmount = document.getElementById('summaryAmount');
        const summaryTotal = document.getElementById('summaryTotal');
        const summaryKey = document.getElementById('summaryKey');
        const summaryType = document.getElementById('summaryType');
        const clearAmount = document.getElementById('clearAmount');
        const payWithPix = document.getElementById('payWithPix');
        
        amountButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                amountButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                button.classList.add('active');
                
                // Set selected amount
                selectedAmount = parseFloat(button.dataset.amount);
                updateAmountDisplay();
                
                // Show selected amount container
                selectedAmountContainer.classList.remove('hidden');
                
                // Clear custom amount input
                customAmountInput.value = '';
                
                // Enable payment button if PIX key is selected
                if (selectedPixKey) {
                    payWithPix.disabled = false;
                }
            });
        });
        
        // Custom amount input
        customAmountInput.addEventListener('input', () => {
            // Remove active class from all buttons
            amountButtons.forEach(btn => btn.classList.remove('active'));
            
            if (customAmountInput.value) {
                selectedAmount = parseFloat(customAmountInput.value);
                
                // Validate minimum amount
                if (selectedAmount < 10) {
                    selectedAmount = 10;
                    customAmountInput.value = 10;
                }
                
                updateAmountDisplay();
                
                // Show selected amount container
                selectedAmountContainer.classList.remove('hidden');
                
                // Enable payment button if PIX key is selected
                if (selectedPixKey) {
                    payWithPix.disabled = false;
                }
            } else {
                selectedAmount = 0;
                selectedAmountContainer.classList.add('hidden');
                payWithPix.disabled = false;
            }
        });
        
        // Update amount display in summary
        function updateAmountDisplay() {
            selectedAmountDisplay.textContent = `R$ ${selectedAmount.toFixed(2).replace('.', ',')}`;
            summaryAmount.textContent = `R$ ${selectedAmount.toFixed(2).replace('.', ',')}`;
            summaryTotal.textContent = `R$ ${selectedAmount.toFixed(2).replace('.', ',')}`;
        }
        
        // Clear amount selection
        clearAmount.addEventListener('click', () => {
            // Remove active class from all buttons
            amountButtons.forEach(btn => btn.classList.remove('active'));
            
            // Reset amount
            selectedAmount = 0;
            selectedAmountContainer.classList.add('hidden');
            customAmountInput.value = '';
            
            // Reset summary
            summaryAmount.textContent = 'R$ 0,00';
            summaryTotal.textContent = 'R$ 0,00';
            
            // Disable payment button
            payWithPix.disabled = false;
        });
        
        // PIX key selection
        const pixKeyCards = document.querySelectorAll('.pix-key-card');
        
        pixKeyCards.forEach(card => {
            card.addEventListener('click', () => {
                // Remove active class from all cards
                pixKeyCards.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked card
                card.classList.add('active');
                
                // Set selected key and type
                selectedPixKey = card.dataset.key;
                selectedPixType = card.dataset.type;
                
                // Update summary
                summaryKey.textContent = selectedPixKey;
                
                // Set type display text
                let typeText = '';
                switch(selectedPixType) {
                    case 'cpf':
                        typeText = 'CPF';
                        break;
                    case 'email':
                        typeText = 'E-mail';
                        break;
                    case 'random':
                        typeText = 'Chave Aleatória';
                        break;
                }
                summaryType.textContent = typeText;
                
                // Enable payment button if amount is selected
                if (selectedAmount > 0) {
                    payWithPix.disabled = false;
                }
            });
        });
        
        // Copy PIX key
        const copyButtons = document.querySelectorAll('.copy-btn');
        
        copyButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const key = button.dataset.key;
                
                // Copy to clipboard
                navigator.clipboard.writeText(key).then(() => {
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
        
        // Pay with PIX button
        payWithPix.addEventListener('click', () => {
            if (selectedAmount > 0) {
                // In a real application, this would redirect to the payment gateway
                // For demonstration, we'll show a confirmation modal
                showPaymentConfirmation();
            }
        });
        
        // Show payment confirmation modal
        function showPaymentConfirmation() {
            // Create modal overlay
            const modalOverlay = document.createElement('div');
            modalOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modalOverlay.id = 'paymentModal';
            
            // Create modal content
            const modalContent = document.createElement('div');
            modalContent.className = 'bg-white rounded-lg shadow-xl p-6 w-full max-w-md';
            
            // Modal header
            const modalHeader = document.createElement('div');
            modalHeader.className = 'flex justify-between items-center mb-4';
            
            const modalTitle = document.createElement('h3');
            modalTitle.className = 'text-xl font-bold text-gray-800';
            modalTitle.textContent = 'Confirmar Pagamento';
            
            const closeButton = document.createElement('button');
            closeButton.className = 'text-gray-500 hover:text-gray-700';
            closeButton.innerHTML = '<i class="fas fa-times"></i>';
            closeButton.addEventListener('click', () => {
                modalOverlay.remove();
            });
            
            modalHeader.appendChild(modalTitle);
            modalHeader.appendChild(closeButton);
            
            // Modal body
            const modalBody = document.createElement('div');
            modalBody.className = 'space-y-4';
            
            const amountInfo = document.createElement('div');
            amountInfo.className = 'flex justify-between';
            amountInfo.innerHTML = `
                <span class="text-gray-600">Valor:</span>
                <span class="font-bold">R$ ${selectedAmount.toFixed(2).replace('.', ',')}</span>
            `;
            
            const pixKeyInfo = document.createElement('div');
            pixKeyInfo.className = 'flex justify-between';
            pixKeyInfo.innerHTML = `
                <span class="text-gray-600">Chave PIX:</span>
                <span class="font-medium">${selectedPixKey}</span>
            `;
            
            const pixTypeInfo = document.createElement('div');
            pixTypeInfo.className = 'flex justify-between';
            pixTypeInfo.innerHTML = `
                <span class="text-gray-600">Tipo:</span>
                <span class="font-medium">${summaryType.textContent}</span>
            `;
            
            const divider = document.createElement('div');
            divider.className = 'border-t border-gray-200 my-3';
            
            const totalInfo = document.createElement('div');
            totalInfo.className = 'flex justify-between text-lg';
            totalInfo.innerHTML = `
                <span class="text-gray-800 font-medium">Total:</span>
                <span class="font-bold text-blue-600">R$ ${selectedAmount.toFixed(2).replace('.', ',')}</span>
            `;
            
            const warningInfo = document.createElement('div');
            warningInfo.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-700';
            warningInfo.innerHTML = `
                <p class="font-medium">Você será redirecionado para o gateway de pagamento.</p>
                <p class="mt-1">O saldo será creditado automaticamente após a confirmação.</p>
            `;
            
            modalBody.appendChild(amountInfo);
            modalBody.appendChild(pixKeyInfo);
            modalBody.appendChild(pixTypeInfo);
            modalBody.appendChild(divider);
            modalBody.appendChild(totalInfo);
            modalBody.appendChild(warningInfo);
            
            // Modal footer
            const modalFooter = document.createElement('div');
            modalFooter.className = 'flex justify-end space-x-3 mt-6';
            
            const cancelButton = document.createElement('button');
            cancelButton.className = 'px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition';
            cancelButton.textContent = 'Cancelar';
            cancelButton.addEventListener('click', () => {
                modalOverlay.remove();
            });
            
            const confirmButton = document.createElement('button');
            confirmButton.className = 'px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition';
            confirmButton.textContent = 'Confirmar e Pagar';
            confirmButton.addEventListener('click', () => {
                // In a real application, this would redirect to the payment gateway
                // For demonstration, we'll simulate a redirect
                simulatePaymentRedirect();
                modalOverlay.remove();
            });
            
            modalFooter.appendChild(cancelButton);
            modalFooter.appendChild(confirmButton);
            
            // Assemble modal
            modalContent.appendChild(modalHeader);
            modalContent.appendChild(modalBody);
            modalContent.appendChild(modalFooter);
            modalOverlay.appendChild(modalContent);
            
            // Add modal to body
            document.body.appendChild(modalOverlay);
        }
        
        // Simulate payment redirect
        function simulatePaymentRedirect() {
            // Create loading overlay
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            loadingOverlay.id = 'loadingOverlay';
            
            // Create loading content
            const loadingContent = document.createElement('div');
            loadingContent.className = 'bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-center';
            
            // Add spinner
            const spinner = document.createElement('div');
            spinner.className = 'animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mx-auto mb-4';
            
            // Add text
            const loadingText = document.createElement('p');
            loadingText.className = 'text-gray-700 mb-4';
            loadingText.textContent = 'Redirecionando para o gateway de pagamento...';
            
            loadingContent.appendChild(spinner);
            loadingContent.appendChild(loadingText);
            loadingOverlay.appendChild(loadingContent);
            
            // Add to body
            document.body.appendChild(loadingOverlay);
            
            // Simulate delay before redirect
            setTimeout(() => {
                // In a real application, this would be the actual redirect
                // For demo, we'll show a success message
                loadingOverlay.remove();
                showPaymentSuccess();
            }, 3000);
        }
        
        // Show payment success message
        function showPaymentSuccess() {
            // Create success overlay
            const successOverlay = document.createElement('div');
            successOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            successOverlay.id = 'successOverlay';
            
            // Create success content
            const successContent = document.createElement('div');
            successContent.className = 'bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-center';
            
            // Add success icon
            const successIcon = document.createElement('div');
            successIcon.className = 'bg-green-100 rounded-full p-3 inline-flex items-center justify-center mb-4';
            successIcon.innerHTML = '<i class="fas fa-check-circle text-green-600 text-3xl"></i>';
            
            // Add title
            const successTitle = document.createElement('h3');
            successTitle.className = 'text-xl font-bold text-gray-800 mb-2';
            successTitle.textContent = 'Pagamento Iniciado!';
            
            // Add text
            const successText = document.createElement('p');
            successText.className = 'text-gray-600 mb-6';
            successText.textContent = `Seu pagamento de R$ ${selectedAmount.toFixed(2).replace('.', ',')} foi iniciado com sucesso. O saldo será creditado assim que o pagamento for confirmado.`;
            
            // Add close button
            const closeButton = document.createElement('button');
            closeButton.className = 'px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition';
            closeButton.textContent = 'Entendi';
            closeButton.addEventListener('click', () => {
                successOverlay.remove();
            });
            
            successContent.appendChild(successIcon);
            successContent.appendChild(successTitle);
            successContent.appendChild(successText);
            successContent.appendChild(closeButton);
            successOverlay.appendChild(successContent);
            
            // Add to body
            document.body.appendChild(successOverlay);
        }
    </script>

@endsection
