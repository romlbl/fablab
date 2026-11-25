<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Client1ProjetsJobsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Récupération des types de jobs créés par DatabaseSeeder.
        $typeMachineId = DB::table('type_jobs')->where('nom', 'Machine')->value('id');
        $typeCollaborateurId = DB::table('type_jobs')->where('nom', 'Collaborateur')->value('id');
        $typeCaoId = DB::table('type_travails')
            ->where('nom', 'CAO')
            ->where('id_type_jobs', $typeCollaborateurId)
            ->value('id');

        if (! $typeMachineId || ! $typeCollaborateurId || ! $typeCaoId) {
            $this->command?->error('Les types de jobs ou de travail sont introuvables. Lancez DatabaseSeeder.');
            return;
        }

        $client1Id = DB::table('clients')->where('nom', 'Atelier du Nord SAS')->value('id');

        if (! $client1Id) {
            $this->command?->warn('Client « Atelier du Nord SAS » introuvable.');
            return;
        }

        // Récupération des IDs déjà présents en base
        $catOuvertId = DB::table('categories_projets')->where('nom', 'projet ouvert')->value('id')
            ?? DB::table('categories_projets')->value('id');
        $catExterneId = DB::table('categories_projets')->where('nom', 'externe')->value('id')
            ?? $catOuvertId;
        $catInterneId = DB::table('categories_projets')->where('nom', 'interne')->value('id')
            ?? $catOuvertId;

        $operateur1Id = DB::table('collaborateurs')->value('id');
        $operateur2Id = DB::table('collaborateurs')->skip(1)->value('id') ?? $operateur1Id;

        $machineFdmId = DB::table('machines')->where('nom', 'like', '%BambuLab%')->value('id')
            ?? DB::table('machines')->value('id');
        $machineLaserId = DB::table('machines')->where('nom', 'like', '%Trotec%')->value('id')
            ?? $machineFdmId;

        $consoFilamentId = DB::table('consommables')->where('nom', 'like', '%Filament%')->value('id')
            ?? DB::table('consommables')->value('id');
        $consoBoisId = DB::table('consommables')->where('nom', 'like', '%bois%')->value('id')
            ?? $consoFilamentId;

        // 1. Projet A du client 1
        $projetA = DB::table('projets')->insertGetId([
            'nom' => 'Bras articulé prototype V2',
            'description' => 'Modélisation CAO et impression de plusieurs pièces pour le client #1.',
            'chemin_image' => 'projets/bras-articule-v2.jpg',
            'id_client' => $client1Id,
            'id_categorie_projet' => $catExterneId,
            'date_creation' => $now->copy()->subDays(9),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Projet B du client 1
        $projetB = DB::table('projets')->insertGetId([
            'nom' => 'Panneau de contrôle gravé',
            'description' => null,
            'chemin_image' => null,
            'id_client' => $client1Id,
            'id_categorie_projet' => $catOuvertId,
            'date_creation' => $now->copy()->subDays(6),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Projet C du client 1
        $projetC = DB::table('projets')->insertGetId([
            'nom' => 'Mise à jour outillage atelier',
            'description' => 'Ajustement des gabarits et préparation de l’assemblage.',
            'chemin_image' => 'projets/outillage-atelier.jpg',
            'id_client' => $client1Id,
            'id_categorie_projet' => $catInterneId,
            'date_creation' => $now->copy()->subDays(3),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Jobs du Projet A
        DB::table('jobs')->insert([
            'id_type' => $typeCollaborateurId,
            'status' => 'termine',
            'id_operateur' => $operateur1Id,
            'id_client' => $client1Id,
            'id_projet' => $projetA,
            'description' => 'Conception et mise au plan des articulations.',
            'debut' => $now->copy()->subDays(5)->setTime(9, 0),
            'fin' => $now->copy()->subDays(5)->setTime(11, 30),
            'id_machine' => null,
            'id_type_travail' => $typeCaoId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $jobA2 = DB::table('jobs')->insertGetId([
            'id_type' => $typeMachineId,
            'status' => 'termine',
            'id_operateur' => $operateur2Id,
            'id_client' => $client1Id,
            'id_projet' => $projetA,
            'description' => 'Impression des pièces structurelles.',
            'debut' => $now->copy()->subDays(4)->setTime(14, 0),
            'fin' => $now->copy()->subDays(4)->setTime(17, 15),
            'id_machine' => $machineFdmId,
            'id_type_travail' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Jobs du Projet B
        DB::table('jobs')->insert([
            'id_type' => $typeCollaborateurId,
            'status' => 'termine',
            'id_operateur' => $operateur1Id,
            'id_client' => $client1Id,
            'id_projet' => $projetB,
            'description' => 'Préparation vectorielle du panneau.',
            'debut' => $now->copy()->subDays(3)->setTime(10, 0),
            'fin' => $now->copy()->subDays(3)->setTime(11, 0),
            'id_machine' => null,
            'id_type_travail' => $typeCaoId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $jobB2 = DB::table('jobs')->insertGetId([
            'id_type' => $typeMachineId,
            'status' => 'en_cours',
            'id_operateur' => $operateur2Id,
            'id_client' => $client1Id,
            'id_projet' => $projetB,
            'description' => 'Découpe et marquage laser sur plaque.',
            'debut' => $now->copy()->subHours(2),
            'fin' => $now->copy()->addHours(1),
            'id_machine' => $machineLaserId,
            'id_type_travail' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Jobs du Projet C
        DB::table('jobs')->insert([
            'id_type' => $typeCollaborateurId,
            'status' => 'planifie',
            'id_operateur' => $operateur1Id,
            'id_client' => $client1Id,
            'id_projet' => $projetC,
            'description' => 'Vérification des cotes sur modèle 3D.',
            'debut' => $now->copy()->addDay()->setTime(9, 30),
            'fin' => $now->copy()->addDay()->setTime(11, 0),
            'id_machine' => null,
            'id_type_travail' => $typeCaoId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('jobs')->insert([
            'id_type' => $typeMachineId,
            'status' => 'planifie',
            'id_operateur' => $operateur2Id,
            'id_client' => $client1Id,
            'id_projet' => $projetC,
            'description' => null,
            'debut' => $now->copy()->addDays(2)->setTime(13, 0),
            'fin' => $now->copy()->addDays(2)->setTime(15, 0),
            'id_machine' => $machineFdmId,
            'id_type_travail' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Consommables liés aux jobs machine
        if ($consoFilamentId) {
            DB::table('jobs_consommables')->insert([
                'id_job' => $jobA2,
                'id_consommable' => $consoFilamentId,
                'quantite' => 210.00,
                'cout' => 0.03,
                'cout_tot' => 6.30,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if ($consoBoisId) {
            DB::table('jobs_consommables')->insert([
                'id_job' => $jobB2,
                'id_consommable' => $consoBoisId,
                'quantite' => 0.25,
                'cout' => 18.50,
                'cout_tot' => 4.63,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}