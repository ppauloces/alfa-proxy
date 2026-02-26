<?php

namespace App\Console\Commands;

use App\Mail\ComunicadoMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarComunicado extends Command
{
    protected $signature = 'email:comunicado
                            {--dry-run : Simula o envio sem disparar emails}
                            {--delay=500 : Delay em milissegundos entre cada envio}';

    protected $description = 'Envia o comunicado de atualização para todos os usuários';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $delay = (int) $this->option('delay');

        $usuarios = User::select('id', 'name', 'email', 'status')
            ->where('status', 1)
            ->orderBy('id')
            ->get();

        $total = $usuarios->count();

        if ($total === 0) {
            $this->warn('Nenhum usuário ativo encontrado.');
            return self::SUCCESS;
        }

        $this->info("Total de usuários ativos: {$total}");

        if ($dryRun) {
            $this->warn('--- MODO DRY-RUN: nenhum email será enviado ---');
            foreach ($usuarios as $user) {
                $this->line("  [dry-run] {$user->email} ({$user->name})");
            }
            return self::SUCCESS;
        }

        if (! $this->confirm("Confirmar envio para {$total} usuário(s)?")) {
            $this->info('Cancelado.');
            return self::SUCCESS;
        }

        $enviados = 0;
        $erros = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($usuarios as $user) {
            try {
                Mail::to($user->email)->send(new ComunicadoMail($user->name));
                $enviados++;
            } catch (\Throwable $e) {
                $erros++;
                $this->newLine();
                $this->error("Erro ao enviar para {$user->email}: {$e->getMessage()}");
            }

            $bar->advance();

            if ($delay > 0) {
                usleep($delay * 1000);
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Enviados: {$enviados} | Erros: {$erros}");

        return self::SUCCESS;
    }
}
