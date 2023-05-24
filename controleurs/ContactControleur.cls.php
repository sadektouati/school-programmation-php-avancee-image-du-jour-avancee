<?php

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Choice;


class ContactControleur extends Controleur
{

    /**
     * les messages d'erreurs de validation
     */
    protected $messagesUI = [
        '_1000'  =>  "Prenom non autorisé",
        '_1001'  =>  "Nom non autorisé",
        '_1002'  =>  "Numéro téléphone non autorisé",
        '_1003'  =>  "Poste téléphone non autorisé",
        '_1004'  =>  "Type téléphone non autorisé",
    ];
    /**
     * les type de telephones
     */
    private $telTypes = ['Cellulaire','Domicile','Bureau','Autre'];

    function __construct($modele, $module, $action, $params)
    {
        if(!isset($_SESSION['utilisateur'])) {
            Utilitaire::nouvelleRoute('utilisateur/index');
        }
        
        parent::__construct($modele, $module, $action, $params);
    }

    /**
     * Méthode invoquée par défaut si aucune action n'est indiquée
     */
    public function index()
    {
        // Par défaut on affiche les tâches
        $this->gabarit->affecterActionParDefaut('tout');
        $this->tout();

    }

    /**
     * Methode pour recuperer tous les contact de l'usager connecté
     * @param void
     */
    public function tout() {

        $this->gabarit->affecter('nouveau', $this->params['nouveau']??'');
        $this->gabarit->affecter('modifier', $this->params['modifier']??'');

        $contacts = $this->modele->tout();
        $this->gabarit->affecter('contacts', $contacts);
        $this->gabarit->affecter('modifier_contact_id', array_key_last($this->params??[]));

        $contact = $contacts[array_key_last($this->params??[])]??[];
        $telsIdx = count($contact['tels']??[])>0 ?  count($contact['tels']??[])-1 : 1;
        $this->gabarit->affecter('contact', $contact);
        $this->gabarit->affecter('telsIdx', $telsIdx);


    }

    /**
     * validation de formulaire
     */
    private function donneesValide(){
        $erreurValidation = false;
        $ctcTelTypeErreurs = $ctcNumeroPosteErreurs = $ctcNumeroErreurs = $ctcPrenomErreurs = 0;

        $ctcPrenomErreurs = $this->validateur->validate($_POST['ctc_prenom'], [new NotBlank(), new Regex(['pattern'=>'/[A-Za-z\-., ]{1,100}/'])]);
        if(count($ctcPrenomErreurs)>0) $erreurValidation = '_1001';

        $ctcNomErreurs = $this->validateur->validate($_POST['ctc_nom'], [new NotBlank(), new Regex(['pattern'=>'/[A-Za-z\-., ]{1,100}/'])]);
        if(count($ctcNomErreurs)>0) $erreurValidation = '_1001';

        foreach($_POST['tel_numero'] as $k => $tel_numero) {
            $ctcNumeroErreurs = $this->validateur->validate($tel_numero, [new NotBlank(), new Regex(['pattern'=>'/[0-9\-+ ]{10,15}/'])]);
            if(count($ctcNumeroErreurs)>0) {
                $erreurValidation = '_1002';
                break;
            }

            $ctcNumeroPosteErreurs = $this->validateur->validate($_POST['tel_poste'][$k], [new NotBlank(), new Regex(['pattern'=>'/[0-9]{4}/'])]);
            if(count($ctcNumeroPosteErreurs)>0){
                $erreurValidation = '_1003';
                break;
            }

            $ctcTelTypeErreurs += in_array($_POST['tel_type'][$k], $this->telTypes) ? 1 : 0;
            if($ctcTelTypeErreurs>0) {
                $ctcTelTypeErreurs = '_1004';
                break;
            }

        }

        if($erreurValidation) {
            $this->gabarit->affecter('ajout', true);
            $this->gabarit->affecter('ctc_prenom', $_POST['ctc_prenom']);
            $this->gabarit->affecter('ctc_nom', $_POST['ctc_nom']);
            $this->gabarit->affecter('tel_numero', $_POST['tel_numero']);
            $this->gabarit->affecter('tel_poste', $_POST['tel_poste']);
            $this->gabarit->affecter('tel_type', $_POST['tel_type']);
            $this->gabarit->affecter('erreur', $this->messagesUI[$erreurValidation]);
            $this->gabarit->affecterActionParDefaut('tout');
            $this->tout();
        }else{
            return true;
        }
    }


    /**
     * pour rajouter un contact avec ses numeros de telephone
     */
    public function ajouter() {

        if($this->donneesValide()){
            $res = $this->modele->ajouter($_POST['ctc_prenom'], $_POST['ctc_nom'], $_POST['tel_numero'], $_POST['tel_poste'], $_POST['tel_type']);
            Utilitaire::nouvelleRoute('contact/tout');
        }
    }

    /**
     * mettre a jour un contact et tout ses numeros de telephone
     */
    public function modifier() {

        if($this->donneesValide()){
            $res = $this->modele->update(array_key_last($this->params), $_POST['ctc_prenom'], $_POST['ctc_nom'], $_POST['tel_numero'], $_POST['tel_poste'], $_POST['tel_type']);
            Utilitaire::nouvelleRoute('contact/tout');
        }
    }

    /**
     * pour supprimer un contact et tous se numeros de telephone
     */
    public function supprimer() {

            $res = $this->modele->delete(array_key_last($this->params));
            Utilitaire::nouvelleRoute('contact/tout');

    }


}
