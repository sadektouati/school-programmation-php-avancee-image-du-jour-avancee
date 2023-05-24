<?php
class ContactModele extends AccesBd
{
    /**
     * Obtenir la liste detaillée de tout les contacts avec numeros de telephone
     */
    public function tout()
    {
        $contacts = $this->lireTout("select * from contact join telephone on (ctc_id=tel_ctc_id_ce) where ctc_uti_id_ce = :ctc_uti_id_ce order by ctc_id, case tel_type when 'Cellulaire' then 1 when 'Domicile' then 2 when 'Bureau' then 3 when 'Autre' then 4 else 5 end",
            [
            'ctc_uti_id_ce'  => $_SESSION['utilisateur']->uti_id
            ]);
        $cntctID=-1; $personne=[];
        foreach ($contacts as $key => $contact) {
            if($cntctID != $contact->ctc_id){
                $cntctID = $contact->ctc_id;
                $personne[$cntctID]['ctc_prenom']=$contact->ctc_prenom;
                $personne[$cntctID]['ctc_nom']=$contact->ctc_nom;
                $j=-1;
            }
            $j++;
            $personne[$cntctID]['tels'][$j]['tel_numero'] = $contact->tel_numero;
            $personne[$cntctID]['tels'][$j]['tel_type'] = $contact->tel_type;
            $personne[$cntctID]['tels'][$j]['tel_poste'] = $contact->tel_poste;
        }

        return $personne;

    }



    /**
     * Ajouter un contact
     * @param array $contact Tableau contenant le détail d'un contact.
     */
    public function ajouter($prenom, $nom, $tel_numeros, $tel_postes, $tel_types)
    {
        $res = $this->creer("INSERT INTO contact(ctc_prenom, ctc_nom, ctc_uti_id_ce) VALUES (:ctc_prenom, :ctc_nom, :ctc_uti_id_ce)"
                        , [
                            'ctc_prenom' => $prenom, 
                            'ctc_nom' => $nom, 
                            'ctc_uti_id_ce'  => $_SESSION['utilisateur']->uti_id
                        ]);

        if(is_numeric($res)) {
            foreach ($tel_numeros as $key => $tel_numero) {
               $resTel = $this->creer("INSERT INTO telephone(tel_numero, tel_type, tel_poste, tel_ctc_id_ce) VALUES (:tel_numero, :tel_type, :tel_poste, :tel_ctc_id_ce)"
                        , [
                            'tel_numero' => $tel_numero, 
                            'tel_type' => $tel_types[$key], 
                            'tel_poste' => $tel_postes[$key],
                            'tel_ctc_id_ce' => $res
                        ]);
            }
        }
        return $res;
    }


    /**
     * mettre a jour un contact
     * @param int $ctc_id identifiant de contact
     * @param string $prenom le prenom de contact
     * @param string $nom le nom de contact
     * @param array $tel_numeros tableau de numeros de telephone
     * @param array $tel_postes tableau de poste de chaque numero dans le tableau de tel
     * @param array $tel_types tableau de type de chaque numero dans le tableau de tel
     * @return int
     */
    
    public function update($ctc_id, $prenom, $nom, $tel_numeros, $tel_postes, $tel_types)
    {

        $this->delete($ctc_id);
                        
        return $this->ajouter($prenom, $nom, $tel_numeros, $tel_postes, $tel_types);

    }

    /**
     * supprimer un contact
     * @param int $ctc_id identifiant de contact
     */
    public function delete ($ctc_id){

        $suprimeTels = $this->supprimer("delete from telephone where tel_ctc_id_ce = (select ctc_id from contact where ctc_id=:ctc_id and ctc_uti_id_ce=:ctc_uti_id_ce)"
                        , [
                            'ctc_id'  => $ctc_id,
                            'ctc_uti_id_ce'  => $_SESSION['utilisateur']->uti_id
                        ]);
        
        $suprimeContact = $this->supprimer("delete from contact where ctc_id=:ctc_id and ctc_uti_id_ce=:ctc_uti_id_ce"
                        , [
                            'ctc_id'  => $ctc_id,
                            'ctc_uti_id_ce'  => $_SESSION['utilisateur']->uti_id
                        ]);

    }

}