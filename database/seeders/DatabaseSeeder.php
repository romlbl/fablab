<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 0. Types de jobs et types de travail
        DB::table('type_jobs')->updateOrInsert(
            ['nom' => 'Machine'],
            ['created_at' => $now, 'updated_at' => $now]
        );
        DB::table('type_jobs')->updateOrInsert(
            ['nom' => 'Collaborateur'],
            ['created_at' => $now, 'updated_at' => $now]
        );

        $typeMachineId = DB::table('type_jobs')->where('nom', 'Machine')->value('id');
        $typeCollaborateurId = DB::table('type_jobs')->where('nom', 'Collaborateur')->value('id');

        DB::table('type_travails')->updateOrInsert(
            ['nom' => 'CAO', 'id_type_jobs' => $typeCollaborateurId],
            ['created_at' => $now, 'updated_at' => $now]
        );
        DB::table('type_travails')->updateOrInsert(
            ['nom' => 'MO', 'id_type_jobs' => $typeCollaborateurId],
            ['created_at' => $now, 'updated_at' => $now]
        );

        // 1. Unités (g, m, m², ml)
        $uniteG = DB::table('unites')->insertGetId([
            'unite' => 'g',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $uniteM = DB::table('unites')->insertGetId([
            'unite' => 'm',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $uniteM2 = DB::table('unites')->insertGetId([
            'unite' => 'm²',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $uniteMl = DB::table('unites')->insertGetId([
            'unite' => 'ml',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Catégories (Impression 3D FDM, Impression 3D SLA, Découpe laser, Scanner)
        $catFdm = DB::table('categories')->insertGetId([
            'nom' => 'Impression 3D FDM',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $catSla = DB::table('categories')->insertGetId([
            'nom' => 'Impression 3D SLA',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $catLaser = DB::table('categories')->insertGetId([
            'nom' => 'Découpe laser',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $catScanner = DB::table('categories')->insertGetId([
            'nom' => 'Scanner',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Catégories de projets (projet ouvert, externe, interne)
        $catProjOuvert = DB::table('categories_projets')->insertGetId([
            'nom' => 'projet ouvert',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $catProjExterne = DB::table('categories_projets')->insertGetId([
            'nom' => 'externe',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $catProjInterne = DB::table('categories_projets')->insertGetId([
            'nom' => 'interne',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. Fournisseurs
        $fournisseurFilament = DB::table('fournisseurs')->insertGetId([
            'nom' => 'PolyMaker Supply',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $fournisseurBois = DB::table('fournisseurs')->insertGetId([
            'nom' => 'Bois & Laser FR',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $fournisseurResine = DB::table('fournisseurs')->insertGetId([
            'nom' => 'RésineLab',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5. Types de clients (interne, externe)
        $typeClientInterne = DB::table('types_clients')->insertGetId([
            'nom' => 'interne',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $typeClientExterne = DB::table('types_clients')->insertGetId([
            'nom' => 'externe',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 6. Collaborateurs (contrat etudiant, intervenant, responsable)
        $collabEtudiant = DB::table('collaborateurs')->insertGetId([
            'nom' => 'Camille Laurent',
            'type' => 'contrat etudiant',
            'mdp_hash' => Hash::make('Etudiant123!'),
            'date_creation' => $now->copy()->subDays(40),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $collabIntervenant = DB::table('collaborateurs')->insertGetId([
            'nom' => 'Lucas Bernard',
            'type' => 'intervenant',
            'mdp_hash' => Hash::make('Intervenant123!'),
            'date_creation' => $now->copy()->subDays(60),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $collabResponsable = DB::table('collaborateurs')->insertGetId([
            'nom' => 'Claire Moreau',
            'type' => 'responsable',
            'mdp_hash' => Hash::make('Responsable123!'),
            'date_creation' => $now->copy()->subDays(90),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $collabArchive = DB::table('collaborateurs')->insertGetId([
            'nom' => 'Inès Moreau',
            'type' => 'contrat etudiant',
            'mdp_hash' => Hash::make('Archive123!'),
            'date_creation' => $now->copy()->subDays(120),
            'date_archive' => $now->copy()->subDays(15),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 7. Clients (interne / externe)
        $clientNord = DB::table('clients')->insertGetId([
            'nom' => 'Atelier du Nord SAS',
            'id_type_client' => $typeClientExterne,
            'code_acces_hash' => Hash::make('CLIENT-EXT-01'),
            'date_creation' => $now->copy()->subDays(25),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $clientDepartement = DB::table('clients')->insertGetId([
            'nom' => 'Département Génie Mécanique',
            'id_type_client' => $typeClientInterne,
            'code_acces_hash' => Hash::make('CLIENT-INT-02'),
            'date_creation' => $now->copy()->subDays(14),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $clientAssociation = DB::table('clients')->insertGetId([
            'nom' => 'Club Robotique Étudiant',
            'id_type_client' => $typeClientInterne,
            'code_acces_hash' => Hash::make('CLIENT-INT-03'),
            'date_creation' => $now->copy()->subDays(8),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 8. Machines (associées strictement à leur catégorie correspondante)
        $machineBambu = DB::table('machines')->insertGetId([
            'nom' => 'BambuLab X1 Carbon',
            'description' => 'Imprimante 3D FDM multi-matériaux haute vitesse avec chambre fermée.',
            'id_categorie' => $catFdm, // -> Impression 3D FDM
            'chemin_image' => 'machines/bambulab-x1c.jpg',
            'date_creation' => $now->copy()->subDays(50),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $machineMakerbot = DB::table('machines')->insertGetId([
            'nom' => 'MakerBot Replicator+',
            'description' => null, // Sans description
            'id_categorie' => $catFdm, // -> Impression 3D FDM
            'chemin_image' => 'machines/makerbot-replicator.jpg',
            'date_creation' => $now->copy()->subDays(45),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $machineFormlabs = DB::table('machines')->insertGetId([
            'nom' => 'Formlabs Form 3+',
            'description' => 'Imprimante 3D SLA résine haute précision pour pièces détaillées.',
            'id_categorie' => $catSla, // -> Impression 3D SLA
            'chemin_image' => null, // Sans image
            'date_creation' => $now->copy()->subDays(40),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $machineTrotec = DB::table('machines')->insertGetId([
            'nom' => 'Trotec Speedy 300',
            'description' => 'Graveuse et découpeuse laser CO2 pour bois, acrylique et cuir.',
            'id_categorie' => $catLaser, // -> Découpe laser
            'chemin_image' => null, // Sans image
            'date_creation' => $now->copy()->subDays(70),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $machineScanner = DB::table('machines')->insertGetId([
            'nom' => 'Scanner 3D Artec Eva',
            'description' => null, // Sans description
            'id_categorie' => $catScanner, // -> Scanner
            'chemin_image' => null, // Sans image
            'date_creation' => $now->copy()->subDays(30),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 9. Consommables (Filament noir, Plaque bois, Résine, Ruban LED — variations avec/sans image, description, prix)
        $consoFilamentNoir = DB::table('consommables')->insertGetId([
            'nom' => 'Filament noir PLA+',
            'description' => 'Bobine de filament PLA+ noir mat, diamètre 1.75 mm.',
            'chemin_image' => 'consommables/filament-noir.jpg',
            'id_categorie' => $catFdm,
            'id_unite' => $uniteG,
            'fournisseur' => 'PolyMaker Supply',
            'prix_unite' => 0.03,
            'format_brute_vente' => 'Bobine 1000 g',
            'date_creation' => $now->copy()->subDays(20),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $consoPlaqueBois = DB::table('consommables')->insertGetId([
            'nom' => 'Plaque bois contreplaqué bouleau 3mm',
            'description' => 'Panneau de bouleau calibré pour découpe et gravure laser.',
            'chemin_image' => null, // Sans image
            'id_categorie' => $catLaser,
            'id_unite' => $uniteM2,
            'fournisseur' => 'Bois & Laser FR',
            'prix_unite' => 18.50,
            'format_brute_vente' => 'Planche 600 x 300 mm',
            'date_creation' => $now->copy()->subDays(18),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $consoResineGrise = DB::table('consommables')->insertGetId([
            'nom' => 'Résine photopolymère grise standard',
            'description' => null, // Sans description
            'chemin_image' => 'consommables/resine-grise.png',
            'id_categorie' => $catSla,
            'id_unite' => $uniteMl,
            'fournisseur' => 'RésineLab',
            'prix_unite' => 0.06,
            'format_brute_vente' => 'Flacon 500 ml',
            'date_creation' => $now->copy()->subDays(12),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $consoRubanLed = DB::table('consommables')->insertGetId([
            'nom' => 'Ruban LED 12V',
            'description' => null, // Sans description
            'chemin_image' => null, // Sans image
            'id_categorie' => $catFdm,
            'id_unite' => $uniteM,
            'fournisseur' => null, // Sans fournisseur
            'prix_unite' => null, // Sans prix unitaire
            'format_brute_vente' => 'Rouleau 5 m',
            'date_creation' => $now->copy()->subDays(6),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 10. Projets (catégories : projet ouvert, externe, interne — variations image/description/archivé)
        $projetExterne = DB::table('projets')->insertGetId([
            'nom' => 'Boîtier capteur industriel',
            'description' => 'Prototype imprimé en PLA+ avec trappe vissée pour client externe.',
            'chemin_image' => 'projets/boitier-capteur.jpg',
            'id_client' => $clientNord,
            'id_categorie_projet' => $catProjExterne,
            'date_creation' => $now->copy()->subDays(10),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $projetInterne = DB::table('projets')->insertGetId([
            'nom' => 'Maquette banc d’essai',
            'description' => null, // Sans description
            'chemin_image' => null, // Sans image
            'id_client' => $clientDepartement,
            'id_categorie_projet' => $catProjInterne,
            'date_creation' => $now->copy()->subDays(5),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $projetOuvert = DB::table('projets')->insertGetId([
            'nom' => 'Châssis robot open-source',
            'description' => 'Base modulaire partagée au sein du FabLab.',
            'chemin_image' => 'projets/chassis-robot.jpg',
            'id_client' => $clientAssociation,
            'id_categorie_projet' => $catProjOuvert,
            'date_creation' => $now->copy()->subDays(12),
            'date_archive' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $projetArchive = DB::table('projets')->insertGetId([
            'nom' => 'Support rétro-ingénierie V1',
            'description' => 'Première version archivée après validation du scan.',
            'chemin_image' => null, // Sans image
            'id_client' => $clientDepartement,
            'id_categorie_projet' => $catProjInterne,
            'date_creation' => $now->copy()->subDays(30),
            'date_archive' => $now->copy()->subDays(3), // Projet archivé
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 11. Jobs (cohérence machine <-> consommable)
        $jobImpressionFdm = DB::table('jobs')->insertGetId([
            'id_type' => $typeMachineId,
            'status' => 'termine',
            'id_operateur' => $collabEtudiant,
            'id_client' => $clientNord,
            'id_projet' => $projetExterne,
            'description' => 'Impression du boîtier en filament noir sur BambuLab.',
            'debut' => $now->copy()->subDays(2)->setTime(10, 0),
            'fin' => $now->copy()->subDays(2)->setTime(13, 30),
            'id_machine' => $machineBambu, // Machine FDM
            'id_type_travail' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $jobDecoupeLaser = DB::table('jobs')->insertGetId([
            'id_type' => $typeMachineId,
            'status' => 'en cours',
            'id_operateur' => $collabIntervenant,
            'id_client' => $clientDepartement,
            'id_projet' => $projetInterne,
            'description' => null, // Sans description
            'debut' => $now->copy()->subHours(1),
            'fin' => $now->copy()->addHours(1),
            'id_machine' => $machineTrotec, // Machine Découpe laser
            'id_type_travail' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $jobImpressionSla = DB::table('jobs')->insertGetId([
            'id_type' => $typeMachineId,
            'status' => 'en attente',
            'id_operateur' => $collabResponsable,
            'id_client' => $clientAssociation,
            'id_projet' => $projetOuvert,
            'description' => 'Tirage résine haute précision pour engrenages.',
            'debut' => $now->copy()->addDay()->setTime(9, 0),
            'fin' => $now->copy()->addDay()->setTime(12, 0),
            'id_machine' => $machineFormlabs, // Machine SLA
            'id_type_travail' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $jobScanner = DB::table('jobs')->insertGetId([
            'id_type' => $typeMachineId,
            'status' => 'planifie',
            'id_operateur' => $collabResponsable,
            'id_client' => null, // Sans client
            'id_projet' => null, // Sans projet
            'description' => null, // Sans description
            'debut' => $now->copy()->addDays(2)->setTime(14, 0),
            'fin' => $now->copy()->addDays(2)->setTime(15, 30),
            'id_machine' => $machineScanner, // Machine Scanner
            'id_type_travail' => null, // Aucun type de travail pour ce job machine
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 12. Jobs_consommables (cout_tot = quantite * cout)
        DB::table('jobs_consommables')->insert([
            [
                'id_job' => $jobImpressionFdm,
                'id_consommable' => $consoFilamentNoir,
                'quantite' => 145.00, // 145 g
                'cout' => 0.03,
                'cout_tot' => 4.35,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_job' => $jobDecoupeLaser,
                'id_consommable' => $consoPlaqueBois,
                'quantite' => 0.50, // 0.50 m²
                'cout' => 18.50,
                'cout_tot' => 9.25,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_job' => $jobImpressionSla,
                'id_consommable' => $consoResineGrise,
                'quantite' => 80.00, // 80 ml
                'cout' => 0.06,
                'cout_tot' => 4.80,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

    }
}