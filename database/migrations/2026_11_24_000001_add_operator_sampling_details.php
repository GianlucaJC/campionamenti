<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sampling_operators', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $names = [
            'ALBERTO PROSPERI',
            'ALESSANDRA BELISARI',
            'ALESSANDRO ABATE',
            'ALESSANDRO DI CLEMENTE',
            'ALESSANDRO MIRABILIO',
            'ANDREA CICIONI',
            'ANDREA NARDINOCCHI',
            'ANDREA PAOLI',
            'ANDREA PICHILLI',
            'ANDREA PLEVANI',
            'ANDREA RUSSI',
            'ANGELO D_ASCENZO',
            'ANTONIETTA SANTONE',
            'BENEDETTA SAVINI',
            'CARLA MARSILI',
            'CLAUDIO PATTARA',
            'DANILO MARZIANI',
            'DAVIDE FELICIONE',
            'DAVIDE ROMANO',
            'DORELIA CERASI',
            'EMANUEL DI CARLO',
            'EMANUELA DI SANTE',
            'EMANUELE ABATE',
            'EMANUELE DI SANTE',
            'FABRIZIO BROCCO',
            'FERNANDO D EMIDIO',
            'FRANCESCA MARTELLA',
            'FRANCESCO ALOISI',
            'GABRIELE LOCOVERDE',
            'GABRIELLA GEMINIANI',
            'GIANCARLO BICERNA',
            'GIANLUCA BROCCO',
            'GIANNI VANNUCCI',
            'GIMMI IACONI',
            'GIORGIA TADDEI',
            'GIORGIO FORMICONE',
            'GIORGIO MARZIANI',
            'GIOVANNI COSENZA',
            'GIULIO PAVONE',
            'GIUSEPPE DE PETRIS',
            'IRINA MAKARENKO',
            'KEIVAN FERRETTI',
            'LOREDANA VANNUCCI',
            'LUCA FERRETTI',
            'LUCA PAVONE',
            'LUCIANA CASTAGNA',
            'LUIGI NARDINOCCHI',
            'LUIGI RUGGIERI',
            'MARCELLO DI GIUSEPPE',
            'MARCO CASTRONE',
            'MARCO MARINI',
            'MARIA CUSANO',
            'MARIA D',
            "MARIA D'ALONZO",
            'MARIA DALONZO',
            'MARIACRISTINA ASTOLFI',
            'MARIANNA DIGIFICO',
            'MARINA SBROLLA',
            'MARINO RICCI',
            'MARIO RAPAGNETTA',
            'MARZIA ADORANTE',
            'MASSIMO PELLONE',
            'MATTEO CASTRONE',
            'MATTEO GIORGINI',
            'MATTEO MALATESTA',
            'MATTEO MUZII',
            'MATTEO NALLIRA',
            'MAZZOCCHETTI FEDERICO',
            'MORGANTE BONAVITA',
            'NADIA DI DOMENICANTONIO',
            'PAMELA ASSOGNA',
            'PAOLO FERRETTI',
            'RAFFAELE DI STEFANO',
            'ROBERTO PASQUINI',
            'ROBERTO RICCI',
            'SASHA MARINI',
            'SAVERIO REPETTO',
            'SEBASTIANO ORRÙ',
            'SERENA SCAPELLATO',
            'SIMONE DI DOMENICO',
            'SIMONE FALONE',
            'SORBO LEOPOLDO',
            'STEFANO AZZOLINA',
            'STEFANO PICCIONI',
            'TANIA MARTELLA',
            'VALENTINA CORNELI',
            'VALERIA DI CANZIO',
            'VERRIGNI VALERIO',
            'VINCENZO FERRETTI',
            'WILLIAM FAVA',
        ];

        $now = now();
        DB::table('sampling_operators')->insert(array_map(
            static fn (string $name): array => [
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $names
        ));

        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->foreignId('sampled_operator_id')
                ->nullable()
                ->after('operator_name')
                ->constrained('sampling_operators')
                ->nullOnDelete();
            $table->string('operator_uniform')->nullable()->after('sampled_operator_id');
        });
    }

    public function down(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('sampled_operator_id');
            $table->dropColumn('operator_uniform');
        });

        Schema::dropIfExists('sampling_operators');
    }
};
