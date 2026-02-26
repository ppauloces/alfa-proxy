<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização AlfaProxy</title>
</head>
<body style="margin:0;padding:0;background-color:#0f1117;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0f1117;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;">

                    {{-- Card principal --}}
                    <tr>
                        <td style="background-color:#161b27;border-radius:12px;overflow:hidden;border:1px solid #1e2535;">

                            {{-- Faixa superior azul --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#3369a5;padding:24px 36px;">
                                        <p style="margin:0;font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.7);">Comunicado Oficial</p>
                                        <p style="margin:6px 0 0;font-size:20px;font-weight:700;color:#ffffff;line-height:1.3;">Atualização de Infraestrutura</p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Corpo --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding:36px;">

                                        <p style="margin:0 0 20px;font-size:15px;color:#c8d0e0;line-height:1.7;">
                                            Olá, <strong style="color:#ffffff;">{{ $nomeUsuario }}</strong>
                                        </p>

                                        <p style="margin:0 0 20px;font-size:15px;color:#c8d0e0;line-height:1.7;">
                                            Nas últimas semanas identificamos instabilidades e bloqueios de alguns IPs relacionados ao uso das proxies em determinadas plataformas.
                                        </p>

                                        <p style="margin:0 0 28px;font-size:15px;color:#c8d0e0;line-height:1.7;">
                                            Realizamos uma série de melhorias estruturais, incluindo ajustes na infraestrutura, rede e políticas de provisionamento, com foco em aumentar a
                                            <strong style="color:#ffffff;">estabilidade</strong>,
                                            <strong style="color:#ffffff;">qualidade dos IPs</strong> e
                                            <strong style="color:#ffffff;">segurança operacional</strong>.
                                        </p>

                                        {{-- Box substituição --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
                                            <tr>
                                                <td style="background-color:#1a2236;border:1px solid #2a3a5c;border-left:3px solid #3369a5;border-radius:8px;padding:20px 22px;">
                                                    <p style="margin:0 0 6px;font-size:12px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:#3369a5;">Substituição de Proxy Ativa</p>
                                                    <p style="margin:0;font-size:14px;color:#c8d0e0;line-height:1.6;">
                                                        Quem possui proxy ativa já pode, <strong style="color:#ffffff;">a partir de hoje</strong>, solicitar a substituição diretamente com o nosso suporte.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Box compras --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                                            <tr>
                                                <td style="background-color:#1a2a1a;border:1px solid #2a4a2a;border-left:3px solid #4caf50;border-radius:8px;padding:20px 22px;">
                                                    <p style="margin:0 0 6px;font-size:12px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:#4caf50;">Novas Compras Liberadas</p>
                                                    <p style="margin:0;font-size:14px;color:#c8d0e0;line-height:1.6;">
                                                        As compras de novas proxies estarão novamente disponíveis no sistema <strong style="color:#ffffff;">amanhã (27/06) a partir das 10h</strong>.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Divisor --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                                            <tr>
                                                <td style="border-top:1px solid #1e2535;font-size:0;line-height:0;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        <p style="margin:0 0 16px;font-size:15px;color:#c8d0e0;line-height:1.7;">
                                            Seguimos comprometidos com a evolução contínua do serviço e agradecemos a confiança de todos.
                                        </p>

                                        <p style="margin:0;font-size:15px;color:#c8d0e0;line-height:1.7;">
                                            Atenciosamente,<br>
                                            <strong style="color:#ffffff;">Equipe AlfaProxy</strong>
                                        </p>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" style="padding:28px 0 0;">
                            <p style="margin:0 0 4px;font-size:12px;color:#4a5568;">Este é um email automático, por favor não responda.</p>
                            <p style="margin:0;font-size:12px;color:#4a5568;">&copy; {{ date('Y') }} AlfaProxy. Todos os direitos reservados.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
