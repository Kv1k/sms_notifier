CREATE TABLE IF NOT EXISTS destinataires (
    id SERIAL PRIMARY KEY,
    insee VARCHAR(5) NOT NULL,
    telephone VARCHAR(10) NOT NULL,
    nom VARCHAR(255),
    prenom VARCHAR(255) 
);
