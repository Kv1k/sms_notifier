<?php

namespace App\Service;

use PDO;

/******************************************************************************************************************
***
*** service de gestion de la connexion à la base de données sms_notifier et gérant les requêtes associées à cette base
***
/******************************************************************************************************************/

class DestinatairesManager
{
    private string $host;
    private int $port;
    private string $userDb;
    private string $userPass;
    private string $bddName;
    private ?PDO $pdo = null;

    /**
     * DestinatairesManager constructor.
     *
     * @param string $host
     * @param int $port
     * @param string $userDb
     * @param string $userPass
     * @param string $bddName
     */
    public function __construct(string $host, int $port, string $userDb, string $userPass, string $bddName)
    {
        $this->host = $host;
        $this->port = $port;
        $this->userDb = $userDb;
        $this->userPass = $userPass;
        $this->bddName = $bddName;
    }

    /**
     * Récupère la connexion PDO
     *
     * @return void
     */
    public function getPdo(): void
    {
        if ($this->pdo === null) {
            $connect = 'pgsql:host=' . $this->host . ';port=' . $this->port . ';dbname=' .  $this->bddName . ';user=' . $this->userDb . ';password=' . $this->userPass;
            $this->pdo = new PDO($connect);
            $this->pdo->query("SET NAMES 'UTF8'");
        }
    }

    
    /**
     * Insère les données dans la table 'destinataires'
     *
     * @param string $insee
     * @param string $telephone
     * @param string $nom
     * @param string $prenom
     * @return void
     */
    public function insertCsvIntoDb(string $insee, string $telephone, string $nom, string $prenom): void
    {
        $this->getPdo();

        try {
            $req = "INSERT INTO public.destinataires (insee, telephone, nom, prenom) VALUES (:insee, :telephone, :nom, :prenom)";

            $resultat = $this->pdo->prepare($req);

            $resultat->execute(array(
                'insee'=>$insee,
                'telephone'=>$telephone,
                'nom'=>$nom,
                'prenom'=>$prenom
            ));

            //echo "Données insérées avec succès: $insee, $telephone, $nom, $prenom\n";

        } catch (\PDOException $e) {
            echo "Erreur d'insertion: " . $e->getMessage() . "\n";
        }
    }

    public function getDestinatairesByInsee(string $insee): array
    {
        $this->getPdo();

        try {
            $req = $this->pdo->prepare("SELECT * FROM public.destinataires WHERE insee = :insee");
            $req->execute(['insee' => $insee]);
            return $req->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo "Erreur de récupération : " . $e->getMessage() . "\n";
            return [];
        }
    }
}