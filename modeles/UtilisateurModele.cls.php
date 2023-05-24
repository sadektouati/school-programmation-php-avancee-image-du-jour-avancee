<?php
class UtilisateurModele extends AccesBd
{
    /**
     * Obtenir le détail d'un utilisateur
     * @param string $courriel Adresse courriel de l'utilisateur
     */
    public function un($courriel)
    {
        return $this->lireUn("SELECT * FROM utilisateur 
                                WHERE uti_courriel=:email"
                        , ['email'=>$courriel]);
    }

    /**
     * Ajouter un utilisateur
     * @param array $utilisateur Tableau contenant le détail d'un utilisateur.
     */
    public function ajouter($utilisateur)
    {
        $uti_confirmation = ''; //Ne pas requiere une confirmation d'adresse couriel, on enleve uniqid('Nestor', true);
        $res = $this->creer("INSERT INTO utilisateur(uti_nom, uti_courriel, uti_mdp, uti_date, uti_confirmation) VALUES (:uti_nom, :uti_courriel, :uti_mdp, NOW(), :uti_confirmation)"
                        , [
                            'uti_nom' => $_POST['uti_nom'], 
                            'uti_courriel' => $_POST['uti_courriel'], 
                            'uti_mdp' => password_hash($_POST['uti_mdp'], PASSWORD_DEFAULT),
                            'uti_confirmation'  => $uti_confirmation
                        ]);
        if(is_numeric($res)) {
            return ['courriel'=>$_POST['uti_courriel']];
        }
        return $res;
    }

}