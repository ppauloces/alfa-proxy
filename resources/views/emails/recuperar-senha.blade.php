<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            color: #333;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .email-body p {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .reset-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .reset-button:hover {
            transform: translateY(-2px);
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            color: #6c757d;
            font-size: 14px;
            margin: 5px 0;
        }
        .link-fallback {
            word-break: break-all;
            color: #667eea;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🔐 AlfaProxy</h1>
        </div>

        <div class="email-body">
            <h2>Recuperação de Senha</h2>

            <p>Olá,</p>

            <p>Recebemos uma solicitação para redefinir a senha da sua conta associada ao email <strong>{{ $email }}</strong>.</p>

            <div class="button-container">
                <a href="{{ url('/redefinir-senha/' . $token) }}" class="reset-button">
                    Redefinir Minha Senha
                </a>
            </div>

            <div class="info-box">
                <p><strong>⏰ Este link é válido por 60 minutos</strong></p>
                <p>Por questões de segurança, o link expira após 1 hora.</p>
            </div>

            <p>Se você não solicitou a redefinição de senha, ignore este email. Sua senha permanecerá inalterada.</p>

            <p class="link-fallback">
                <strong>Caso o botão não funcione, copie e cole este link no seu navegador:</strong><br>
                {{ url('/redefinir-senha/' . $token) }}
            </p>
        </div>

        <div class="email-footer">
            <p><strong>AlfaProxy</strong></p>
            <p>Este é um email automático, por favor não responda.</p>
            <p>&copy; {{ date('Y') }} AlfaProxy. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
