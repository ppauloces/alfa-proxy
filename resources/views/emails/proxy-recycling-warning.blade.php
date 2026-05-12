<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sua proxy sera reciclada</title>
</head>
<body style="margin:0;padding:0;background-color:#0f1117;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0f1117;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;">
                    <tr>
                        <td style="background-color:#161b27;border-radius:12px;overflow:hidden;border:1px solid #1e2535;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#b54848;padding:24px 36px;">
                                        <p style="margin:0;font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.7);">Aviso de Reciclagem</p>
                                        <p style="margin:6px 0 0;font-size:20px;font-weight:700;color:#ffffff;line-height:1.3;">Sua proxy sera reciclada em 24h</p>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding:32px 36px;color:#c5cad6;font-size:15px;line-height:1.6;">
                                        <p style="margin:0 0 16px;">Ola, <strong style="color:#ffffff;">{{ $nomeUsuario }}</strong>.</p>

                                        <p style="margin:0 0 16px;">
                                            Identificamos que a proxy abaixo esta expirada e bloqueada ha alguns dias sem renovacao.
                                            Em <strong style="color:#ffffff;">24 horas</strong> ela sera reciclada automaticamente e devolvida ao
                                            estoque com novas credenciais.
                                        </p>

                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0f1117;border:1px solid #1e2535;border-radius:8px;margin:20px 0;">
                                            <tr>
                                                <td style="padding:16px 20px;color:#c5cad6;font-size:14px;font-family:'Courier New',monospace;">
                                                    <p style="margin:0 0 6px;"><strong style="color:#9aa3b8;">IP:</strong> {{ $stock->ip }}:{{ $stock->porta }}</p>
                                                    <p style="margin:0 0 6px;"><strong style="color:#9aa3b8;">Usuario:</strong> {{ $stock->usuario }}</p>
                                                    <p style="margin:0;"><strong style="color:#9aa3b8;">Reciclagem em:</strong> {{ $recicladaEm->format('d/m/Y H:i') }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                        <p style="margin:0 0 16px;">
                                            Se voce ainda deseja manter esta proxy, acesse o painel e renove agora.
                                            Apos a reciclagem nao sera possivel recuperar as credenciais antigas.
                                        </p>

                                        <p style="margin:24px 0 0;text-align:center;">
                                            <a href="{{ url('/socks5') }}" style="display:inline-block;background-color:#3369a5;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:600;font-size:14px;">Acessar painel</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding:20px 36px;background-color:#0f1117;border-top:1px solid #1e2535;color:#6b7385;font-size:12px;text-align:center;">
                                        AlfaProxy &middot; mensagem automatica, nao responda este e-mail.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
