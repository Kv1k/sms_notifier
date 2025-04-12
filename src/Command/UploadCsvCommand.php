<?php

namespace App\Command;

use App\Service\DestinatairesManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

class UploadCsvCommand extends Command
{
    protected static $defaultName = 'app:upload-csv';
    
    private DestinatairesManager $destManager;

    /**
     * UploadCsvCommand constructor.
     *
     * @param DestinatairesManager $destManager
     */
    public function __construct(DestinatairesManager $destManager)
    {
        $this->destManager = $destManager;
        parent::__construct();
    }


    /**
     * Configure la commande
     */
    protected function configure(): void    
    {
        $this
            ->setDescription('Importe un fichier CSV et insère les données dans la base.')
            ->addArgument('file', InputArgument::OPTIONAL, 'Le chemin du fichier CSV à importer', 'var/uploads/data.csv');
    }

    /**
     * Exécute la commande
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');//Récupération du fichier csv 

        $handle = null; //Le pointeur csv

        $errorCount = 0; // Compteur d'erreurs
        $successCount = 0; //Compteur de succès

        // Vérifie si le fichier existe
        if (!file_exists($file)) {
            $output->writeln('<error>Le fichier n\'existe pas!</error>');
            return Command::FAILURE;
        }

        // Ouverture du fichier CSV
        if (($handle = fopen($file, 'r')) === false) {
            $output->writeln('<error>Impossible d\'ouvrir le fichier!</error>');
            return Command::FAILURE;
        }

        
        fgetcsv($handle);// Lit les en-têtes du fichier CSV et les consomme

        
        while (($data = fgetcsv($handle)) !== false) {
            $insee = $data[0];
            $telephone = $data[1];
            $nom = $data[2];
            $prenom = $data[3];

            // Validation du code INSEE : 5 chiffres
            if (!preg_match('/^\d{5}$/', $insee)) {
                $output->writeln("<error>INSEE invalide: $insee</error>");
                $errorCount++;
                continue; 
            }

            // Validation du téléphone : 10 chiffres, doit commencer par 0
            if (!preg_match('/^0\d{9}$/', $telephone)) {
                $output->writeln("<error>Téléphone invalide: $telephone</error>");
                $errorCount++;
                continue; 
            }

            // Insertion dans la base de données si les données sont valides
            $this->destManager->insertCsvIntoDb($insee, $telephone, $nom, $prenom);

            $successCount++;
        }

        fclose($handle);
       
        $output->writeln("
            <info>
            Nombre d'importations réussies : $successCount 
            Nombre d'importations avec erreur : $errorCount
            </info>"
        );
        
        return Command::SUCCESS;
    }
}
