<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class dobiss extends eqLogic {
  
    /*     * *************************Attributs****************************** */
    
  /*
   * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
   * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
	public static $_widgetPossibility = array();
   */
    
    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {
      }
     */

    /*
     * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
      public static function cron5() {
      }
     */

    /*
     * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
      public static function cron10() {
      }
     */
    
    /*
     * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
      public static function cron15() {
      }
     */
    
    /*
     * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
      public static function cron30() {
      }
     */
    
    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {
      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {
      }
     */



    /*     * *********************Méthodes d'instance************************* */
    
 // Fonction exécutée automatiquement avant la création de l'équipement 
    public function preInsert() {
        
    }

 // Fonction exécutée automatiquement après la création de l'équipement 
    public function postInsert() {
       
    }

 // Fonction exécutée automatiquement avant la mise à jour de l'équipement 
    public function preUpdate() {
        
    }

 // Fonction exécutée automatiquement après la mise à jour de l'équipement 
    public function postUpdate() {
        
    }

 // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement 
    public function preSave() {
						 
    }

 // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
    public function postSave() {
        $marche = $this->getCmd(null, 'on');
		if (!is_object($marche)) {
			$marche = new dobissCmd();
			$marche->setName(__('Allumer', __FILE__));
		}
		$marche->setEqLogic_id($this->getId());
		$marche->setLogicalId('on');
		$marche->setType('action');
		$marche->setSubType('other');
		$marche->save(); 
      
      	$arret = $this->getCmd(null, 'off');
		if (!is_object($arret)) {
			$arret = new dobissCmd();
			$arret->setName(__('Eteindre', __FILE__));
		}
		$arret->setEqLogic_id($this->getId());
		$arret->setLogicalId('off');
		$arret->setType('action');
		$arret->setSubType('other');
		$arret->save();
      
        $info = $this->getCmd(null, 'state');
		if (!is_object($info)) {
			$info = new dobissCmd();
			$info->setName(__('Statut', __FILE__));
		}
		$info->setLogicalId('state');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('binary');
		$info->save();	
  
    }
  

 // Fonction exécutée automatiquement avant la suppression de l'équipement 
    public function preRemove() {
        
    }

 // Fonction exécutée automatiquement après la suppression de l'équipement 
    public function postRemove() {
        
    }
  
  	public function allumer() {
		/* Allumer */
      	$host = config::byKey (strtolower('IPAddress'),'dobiss');
        $port = config::byKey (strtolower('Port'),'dobiss');
      	$NumberId = config::byKey (strtolower('NumberId'),'dobiss');
        $AddresseModule = $this->getConfiguration('ModuleAdresse');
        $SortieModule = $this->getConfiguration('ModuleOutput');


        $fp = fsockopen($host, $port, $errno, $errstr);
        if (!$fp) {
            echo "ERROR: $errno - $errstr<br />\n";
        } else {
          $hex = hex2bin($NumberId.$AddresseModule."0".$SortieModule."01");
          $rgb = hexdec($hex);

        fwrite($fp, $hex);

        fclose($fp);
        }
    }
  
  	public function eteindre() {
        /* Eteindre */
        $host = config::byKey (strtolower('IPAddress'),'dobiss');
        $port = config::byKey (strtolower('Port'),'dobiss');
      	$NumberId = config::byKey (strtolower('NumberId'),'dobiss');
        $AddresseModule = $this->getConfiguration('ModuleAdresse');
        $SortieModule = $this->getConfiguration('ModuleOutput');
      

        $fp = fsockopen($host, $port, $errno, $errstr);
        if (!$fp) {
            echo "ERROR: $errno - $errstr<br />\n";
        } else {
          $hex = hex2bin($NumberId.$AddresseModule."0".$SortieModule."00");
          $rgb = hexdec($hex);

        fwrite($fp, $hex);

        fclose($fp);
        }
    }
   // test pour gestion statut
   public function Number_receive() {
     $SortieModule = $this->getConfiguration('ModuleOutput');
        $TypeModule = $this->getConfiguration('ModuleType');
     
     	if ($TypeModule == "relais"){
          if ($SortieModule == 1){
            	$NumberReceive = 65;
            }
          if ($SortieModule == 2){
            	$NumberReceive = 67;
            }
          if ($SortieModule == 3){
            	$NumberReceive = 69;
            }
          if ($SortieModule == 4){
            	$NumberReceive = 71;
            }
          if ($SortieModule == 5){
            	$NumberReceive = 73;
            }
          if ($SortieModule == 6){
            	$NumberReceive = 75;
            }
          if ($SortieModule == 7){
            	$NumberReceive = 77;
            }
          if ($SortieModule == 8){
            	$NumberReceive = 79;
            }
          if ($SortieModule == 9){
            	$NumberReceive = 81;
            }
          if ($SortieModule == 10){
            	$NumberReceive = 83;
            }
          if ($SortieModule == 11){
            	$NumberReceive = 85;
            }
          if ($SortieModule == 12){
            	$NumberReceive = 87;
            }}
   }
  
  // reception des statuts     reste encore a terminer pour fonctionnement correct
   public function statut_receive() {
     	sleep(1);
        $changed = false;
      	$host = config::byKey (strtolower('IPAddress'),'dobiss');
        $port = config::byKey (strtolower('Port'),'dobiss');
      	$NumberId = config::byKey (strtolower('NumberId'),'dobiss');
        $AddresseModule = $this->getConfiguration('ModuleAdresse');
        $SortieModule = $this->getConfiguration('ModuleOutput');
     

        $fp = fsockopen($host, $port, $errno, $errstr);
        if (!$fp) {
            echo "ERROR: $errno - $errstr<br />\n";
        } else {
          $hex = hex2bin("af01ff".$AddresseModule."0000000100ffffffffffffaf");
          $rgb = hexdec($hex);
		
        fwrite($fp, $hex);
		$data_receive = bin2hex(fread($fp, 1024));
        
        if ($data_receive[$NumberReceive] !== 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;

		}else {$changed = $this->checkAndUpdateCmd('state', 0) || $changed;

        }
        fclose($fp);

        }
        return $data_receive;
    }
  	
    // Modification icone en fonction de l'appareil enregistré
    public function getPathImgIcon()  {
        $path = "";
		echo 'bonsoir';
        switch ($this->getConfiguration("device")) {
           case 'lumiere' :  $path = ' /../../../../images/lumière_icon.png'; break;
           case 'prise' :  $path = ' /../../../../images/prise_icon.png'; break;
           case 'ventilation' :  $path = ' /../../../../images/ventilation_icon.png'; break;
           case 'volet' :  $path = ' /../../../../images/volet_icon.png'; break;
           default:
                        $path = ' /../../../../images/dobiss_icon.png';
        }
        return $path;
    }
    /*
     * Non obligatoire : permet de modifier l'affichage du widget (également utilisable par les commandes)
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire : permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire : permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class dobissCmd extends cmd {
  
    /*     * *************************Attributs****************************** */
    
    /*
      public static $_widgetPossibility = array();
    */
    
    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

  // Exécution d'une commande  
     public function execute($_options = array()) {

        $eqlogic = $this->getEqLogic(); //récupère l'éqqlogic de la commande $this
		switch ($this->getLogicalId()) {	//vérifie le logicalid de la commande 			
			case 'on': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm . 
				$marche = $eqlogic->allumer(); 	//On lance la fonction randomVdm() pour récupérer une vdm et on la stocke dans la variable $info
            	$marche = $eqlogic->statut_receive();
                $marche = $eqlogic->Number_receive();
				//$eqlogic->checkAndUpdateCmd('statut', $marche); // on met à jour la commande avec le LogicalId "story"  de l'eqlogic 
				break;
            case 'off': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm . 
				$arret = $eqlogic->eteindre(); 	//On lance la fonction randomVdm() pour récupérer une vdm et on la stocke dans la variable $info
                $arret = $eqlogic->statut_receive();
                $arret = $eqlogic->Number_receive();
                //$eqlogic->checkAndUpdateCmd('statut', $marche);
				//$eqlogic->checkAndUpdateCmd('story', $info); // on met à jour la commande avec le LogicalId "story"  de l'eqlogic 
				break;
            	
		}
     }

    /*     * **********************Getteur Setteur*************************** */
}