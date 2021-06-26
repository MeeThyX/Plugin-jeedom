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

    
     // Fonction exécutée automatiquement toutes les minutes par Jeedom
  
      // demon
      public static function deamon_info()
          {
              $return = array();
              $return['log'] = 'dobiss';
              $return['launchable'] = 'ok';
              $return['state'] = '';

              if ($return['state'] != 'nok') {
                  // Vérif de la configuration
                  if (is_object($dobiss)) {
                      $ip = $dobiss->getConfiguration('IPAddress');
                      $Port = $dobiss->getConfiguration('Port');
                      $NumberId = $dobiss->getConfiguration('NumberId');
              
                      log::add('dobiss', 'debug', 'envois parametre dans variable');

                      if (empty($ip)) {
                          $return['launchable'] = 'nok';
                      }
                      if (empty($Port)) {
                          $return['launchable'] = 'nok';
                      }
                      if (empty($NumberId)) {
                          $return['launchable'] = 'nok';
                      }

                  }

                  $cron = cron::byClassAndFunction('dobiss', 'RefreshInformation');
                  if (is_object($cron) && $cron->running()) {
                      $return['state'] = 'ok';
                  } else {
                      $return['state'] = 'nok';
                  }

                  return $return;
 
              }
          }
          public static function deamon_start()
          {
              //log::remove('dobiss');
              self::deamon_stop();
              $deamon_info = self::deamon_info();
              if ($deamon_info['launchable'] != 'ok') {
                  return;
              }

              if ($deamon_info['state'] == 'ok') {
                  return;
              }

              $cron = cron::byClassAndFunction('dobiss', 'RefreshInformation');
              if (!is_object($cron)) {
                  $cron = new cron();
                  $cron->setClass('dobiss');
                  $cron->setFunction('RefreshInformation');
                  $cron->setEnable(1);
                  $cron->setDeamon(1);
                  $cron->setSchedule('* * * * *');
                  $cron->setTimeout('999999');
                  $cron->save();
              }
              $cron->start();
              $cron->run();
          }
          public static function deamon_stop()
          {
              $cron = cron::byClassAndFunction('dobiss', 'RefreshInformation');
              if (is_object($cron)) {
                  $cron->stop();
                  $cron->remove();
              }
          }

          public function RefreshInformation()
          {
              while (true) {
                  foreach (eqLogic::byType('dobiss') as $Equipement) {
                      if ($Equipement->getIsEnable()) {
                          // Recuperation des infos
                          log::add('dobiss', 'debug', 'Recuperation des infos si enable');

                      }
                      $Equipement->statut_receiveMod();
                  }

              }
              self::deamon_stop();
          }	
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
        $AddresseModule = $this->getConfiguration('ModuleAdresse');
        $SortieModule = $this->getConfiguration('ModuleOutput');

        $info = $this->getCmd(null, 'state'.$AddresseModule.$SortieModule);
		if (!is_object($info)) {
			$info = new dobissCmd();
			$info->setName(__('Statut', __FILE__));
		}
		$info->setLogicalId('state'.$AddresseModule.$SortieModule);
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('binary');
		$info->save();
    }

 // Fonction exécutée automatiquement après la mise à jour de l'équipement 
    public function postUpdate() {

    }

 // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement 
    public function preSave() {

    }

 // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
    public function postSave() {
      
        $AddresseModule = $this->getConfiguration('ModuleAdresse');
        $SortieModule = $this->getConfiguration('ModuleOutput');

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

  		
    }
  

 // Fonction exécutée automatiquement avant la suppression de l'équipement 
    public function preRemove() {
        
    }

 // Fonction exécutée automatiquement après la suppression de l'équipement 
    public function postRemove() {
        
    }
  

  // reception des statuts     reste encore a terminer pour fonctionnement correct
   //$AddresseModule = $this->getConfiguration('ModuleAdresse');
   public function statut_receiveMod() {
        sleep(2);
        $changed = false;
      	$host = config::byKey (strtolower('IPAddress'),'dobiss');
        $port = config::byKey (strtolower('Port'),'dobiss');
      	$NumberId = config::byKey (strtolower('NumberId'),'dobiss');

        try {
              $fp1 = fsockopen($host, $port, $errno, $errstr);
                    if (!$fp1) {
                        echo "ERROR: $errno - $errstr<br />\n";
                        log::add('dobiss', 'debug', 'Error connection TCP ');
                    } else {
                            //$hex = hex2bin("af01ff".$AddresseModule."0000000100ffffffffffffaf");
                            $hex1 = hex2bin("af01ff010000000100ffffffffffffaf");
                            $hex2 = hex2bin("af01ff020000000100ffffffffffffaf");
                            $hex3 = hex2bin("af01ff030000000100ffffffffffffaf");
                            $hex4 = hex2bin("af01ff040000000100ffffffffffffaf");
                            $hex5 = hex2bin("af01ff050000000100ffffffffffffaf");
                            $hex6 = hex2bin("af01ff060000000100ffffffffffffaf");
                            $hex7 = hex2bin("af01ff070000000100ffffffffffffaf");
                            $hex8 = hex2bin("af01ff080000000100ffffffffffffaf");
                            $hex9 = hex2bin("af01ff090000000100ffffffffffffaf");
                            $hex10 = hex2bin("af01ff0a0000000100ffffffffffffaf");
                            $hex11 = hex2bin("af01ff0b0000000100ffffffffffffaf");
                            $hex12 = hex2bin("af01ff0c0000000100ffffffffffffaf");
                            $hex13 = hex2bin("af01ff0d0000000100ffffffffffffaf");
                            $hex14 = hex2bin("af01ff0e0000000100ffffffffffffaf");
                            $hex15 = hex2bin("af01ff0f0000000100ffffffffffffaf");
                            $hex16 = hex2bin("af01ff100000000100ffffffffffffaf");
                            $hex17 = hex2bin("af01ff110000000100ffffffffffffaf");
                            $hex18 = hex2bin("af01ff120000000100ffffffffffffaf");
                            $hex19 = hex2bin("af01ff130000000100ffffffffffffaf");
                            $hex20 = hex2bin("af01ff140000000100ffffffffffffaf");
                            $rgb1 = hexdec($hex1);
                            $rgb2 = hexdec($hex2);
                            $rgb3 = hexdec($hex3);
                            $rgb4 = hexdec($hex4);
                            $rgb5 = hexdec($hex5);
                            $rgb6 = hexdec($hex6);
                            $rgb7 = hexdec($hex7);
                            $rgb8 = hexdec($hex8);
                            $rgb9 = hexdec($hex9);
                            $rgb10 = hexdec($hex10);
                            $rgb11 = hexdec($hex11);
                            $rgb12 = hexdec($hex12);
                            $rgb13 = hexdec($hex13);
                            $rgb14 = hexdec($hex14);
                            $rgb15 = hexdec($hex15);
                            $rgb16 = hexdec($hex16);
                            $rgb17 = hexdec($hex17);
                            $rgb18 = hexdec($hex18);
                            $rgb19 = hexdec($hex19);
                            $rgb20 = hexdec($hex20);
                            sleep(1);

                            	  // Module 1

                                  if ($this->getConfiguration("ModuleAdresse", "01") == "01"){

                                      fwrite($fp1, $hex1);
                                      $data_receive1 = bin2hex(fread($fp1, 1024));
                                      $data_module1=fopen("/var/www/html/plugins/dobiss/data/data_module1.txt","w+");
                                      if (!$data_module1){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_01 not found ');
                                      }
                                      fwrite($data_module1,$data_receive1);
                                      fclose($data_module1);

                                                  log::add('dobiss', 'debug', 'Data Module01 ' . $data_receive1);
											      sleep(1);                                                        
                                                          // actualisation sortie 1										
                                                        if ($this->getConfiguration("ModuleOutput", "0") == "0"){
                                                          if ($this->getIsEnable()) {
                                                            
                                                              $data_1 = $data_receive1[65];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 1: ' . $data_1);
                                                              $logical_id_1 = 'state010';
                                                              $cmd = $this->getCmd(null, $logical_id_1);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_1);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id_1);
                                                              }
                                                        }}
                                                        // actualisation sortie 2
                                                        if ($this->getConfiguration("ModuleOutput", "1") == "1"){
                                                          if ($this->getIsEnable()) {
                                                              $data_2 = $data_receive1[67];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 2: ' . $data_2);
                                                              $logical_id = 'state011';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_2);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 3
                                                        if ($this->getConfiguration("ModuleOutput", "2") == "2"){
                                                          if ($this->getIsEnable()) {
                                                              $data_3 = $data_receive1[69];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 3: ' . $data_3);
                                                              $logical_id = 'state012';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_3);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 4
                                                        if ($this->getConfiguration("ModuleOutput", "3") == "3"){
                                                          if ($this->getIsEnable()) {
                                                              $data_4 = $data_receive1[71];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 4: ' . $data_4);
                                                              $logical_id = 'state013';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_4);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 5
                                                        if ($this->getConfiguration("ModuleOutput", "4") == "4"){
                                                          if ($this->getIsEnable()) {
                                                              $data_5 = $data_receive1[73];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 5: ' . $data_5);
                                                              $logical_id = 'state014';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_5);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 6
                                                        if ($this->getConfiguration("ModuleOutput", "5") == "5"){
                                                          if ($this->getIsEnable()) {
                                                              $data_6 = $data_receive1[75];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 6: ' . $data_6);
                                                              $logical_id = 'state015';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_6);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 7
                                                        if ($this->getConfiguration("ModuleOutput", "6") == "6"){
                                                          if ($this->getIsEnable()) {
                                                              $data_7 = $data_receive1[77];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 7: ' . $data_7);
                                                              $logical_id = 'state016';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_7);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 8
                                                        if ($this->getConfiguration("ModuleOutput", "7") == "7"){
                                                          if ($this->getIsEnable()) {
                                                              $data_8 = $data_receive1[79];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 8: ' . $data_8);
                                                              $logical_id = 'state017';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_8);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 9
                                                        if ($this->getConfiguration("ModuleOutput", "8") == "8"){
                                                          if ($this->getIsEnable()) {
                                                              $data_9 = $data_receive1[81];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 9: ' . $data_9);
                                                              $logical_id = 'state018';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_9);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 10
                                                        if ($this->getConfiguration("ModuleOutput", "9") == "9"){
                                                          if ($this->getIsEnable()) {
                                                              $data_10 = $data_receive1[83];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 10: ' . $data_10);
                                                              $logical_id = 'state019';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_10);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 11
                                                        if ($this->getConfiguration("ModuleOutput", "0a") == "0a"){
                                                          if ($this->getIsEnable()) {
                                                              $data_11 = $data_receive1[85];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 11: ' . $data_11);
                                                              $logical_id = 'state010a';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_11);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                        // actualisation sortie 12
                                                        if ($this->getConfiguration("ModuleOutput", "0b") == "0b"){
                                                          if ($this->getIsEnable()) {
                                                              $data_12 = $data_receive1[87];
                                                              log::add('dobiss', 'debug', 'Valeur sortie 12: ' . $data_12);
                                                              $logical_id = 'state010b';
                                                              $cmd = $this->getCmd(null, $logical_id);
                                                              if (is_object($cmd)) {
                                                                  $cmd->setCollectDate(date('Y-m-d H:i:s'));
                                                                  $cmd->event($data_12);
                                                              } else {
                                                                  log::add('dobiss', 'debug', 'cmd not found ' . $logical_id);
                                                              }
                                                        }}
                                                                                         

                                  }
                                  if ($this->getConfiguration("ModuleAdresse", "02") == "02"){

                                      fwrite($fp1, $hex2);
                                      $data_receive2 = bin2hex(fread($fp1, 1024));
                                      $data_module2=fopen("/var/www/html/plugins/dobiss/data/data_module2.txt","w+");
                                      if (!$data_module2){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_02 not found ');
                                      }
                                      fwrite($data_module2,$data_receive2);
                                      fclose($data_module2);
                                      
                                      //$Module->statut_refreshMod2();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "03") == "03"){

                                      fwrite($fp1, $hex3);
                                      $data_receive3 = bin2hex(fread($fp1, 1024));
                                      $data_module3=fopen("/var/www/html/plugins/dobiss/data/data_module3.txt","w+");
                                      if (!$data_module3){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_03 not found ');
                                      }
                                      fwrite($data_module3,$data_receive3);
                                      fclose($data_module3);
                                    
                                      //$Module->statut_refreshMod3();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "04") == "04"){

                                      fwrite($fp1, $hex4);
                                      $data_receive4 = bin2hex(fread($fp1, 1024));
                                      $data_module4=fopen("/var/www/html/plugins/dobiss/data/data_module4.txt","w+");
                                      if (!$data_module4){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_04 not found ');
                                      }
                                      fwrite($data_module4,$data_receive4);
                                      fclose($data_module4);
                                    
                                      //$Module->statut_refreshMod4();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "05") == "05"){

                                      fwrite($fp1, $hex5);
                                      $data_receive5 = bin2hex(fread($fp1, 1024));
                                      $data_module5=fopen("/var/www/html/plugins/dobiss/data/data_module5.txt","w+");
                                      if (!$data_module5){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_05 not found ');
                                      }
                                      fwrite($data_module5,$data_receive5);
                                      fclose($data_module5);
                                    
                                      //$Module->statut_refreshMod5();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "06") == "06"){

                                      fwrite($fp1, $hex6);
                                      $data_receive6 = bin2hex(fread($fp1, 1024));
                                      $data_module6=fopen("/var/www/html/plugins/dobiss/data/data_module6.txt","w+");
                                      if (!$data_module6){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_06 not found ');
                                      }
                                      fwrite($data_module6,$data_receive6);
                                      fclose($data_module6);
                                    
                                      //$Module->statut_refreshMod6();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "07") == "07"){

                                      fwrite($fp1, $hex7);
                                      $data_receive7 = bin2hex(fread($fp1, 1024));
                                      $data_module7=fopen("/var/www/html/plugins/dobiss/data/data_module7.txt","w+");
                                      if (!$data_module7){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_07 not found ');
                                      }
                                      fwrite($data_module7,$data_receive7);
                                      fclose($data_module7);
                                    
                                      //$Module->statut_refreshMod7();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "08") == "08"){

                                      fwrite($fp1, $hex8);
                                      $data_receive8 = bin2hex(fread($fp1, 1024));
                                      $data_module8=fopen("/var/www/html/plugins/dobiss/data/data_module8.txt","w+");
                                      if (!$data_module8){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_08 not found ');
                                      }
                                      fwrite($data_module8,$data_receive8);
                                      fclose($data_module8);
                                    
                                      //$Module->statut_refreshMod8();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "09") == "09"){

                                      fwrite($fp1, $hex9);
                                      $data_receive9 = bin2hex(fread($fp1, 1024));
                                      $data_module9=fopen("/var/www/html/plugins/dobiss/data/data_module9.txt","w+");
                                      if (!$data_module9){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_09 not found ');
                                      }
                                      fwrite($data_module9,$data_receive9);
                                      fclose($data_module9);
                                    
                                      //$Module->statut_refreshMod9();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "0A") == "0A"){

                                      fwrite($fp1, $hex10);
                                      $data_receive10 = bin2hex(fread($fp1, 1024));
                                      $data_module10=fopen("/var/www/html/plugins/dobiss/data/data_module10.txt","w+");
                                      if (!$data_module10){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_10 not found ');
                                      }
                                      fwrite($data_module10,$data_receive10);
                                      fclose($data_module10);
                                    
                                      //$Module->statut_refreshMod10();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "0B") == "0B"){

                                      fwrite($fp1, $hex11);
                                      $data_receive11 = bin2hex(fread($fp1, 1024));
                                      $data_module11=fopen("/var/www/html/plugins/dobiss/data/data_module11.txt","w+");
                                      if (!$data_module11){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_11 not found ');
                                      }
                                      fwrite($data_module11,$data_receive11);
                                      fclose($data_module11);
                                    
                                      //$Module->statut_refreshMod11();
                                    
                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "0C") == "0C"){

                                      fwrite($fp1, $hex12);
                                      $data_receive12 = bin2hex(fread($fp1, 1024));
                                      $data_module12=fopen("/var/www/html/plugins/dobiss/data/data_module12.txt","w+");
                                      if (!$data_module12){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_12 not found ');
                                      }
                                      fwrite($data_module12,$data_receive12);
                                      fclose($data_module12);
                                    
                                      //$Module->statut_refreshMod12();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "0D") == "0D"){

                                      fwrite($fp1, $hex13);
                                      $data_receive13 = bin2hex(fread($fp1, 1024));
                                      $data_module13=fopen("/var/www/html/plugins/dobiss/data/data_module13.txt","w+");
                                      if (!$data_module13){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_13 not found ');
                                      }
                                      fwrite($data_module13,$data_receive13);
                                      fclose($data_module13);
                                    
                                      //$Module->statut_refreshMod13();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "0E") == "0E"){

                                      fwrite($fp1, $hex14);
                                      $data_receive14 = bin2hex(fread($fp1, 1024));
                                      $data_module14=fopen("/var/www/html/plugins/dobiss/data/data_module14.txt","w+");
                                      if (!$data_module14){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_14 not found ');
                                      }
                                      fwrite($data_module14,$data_receive14);
                                      fclose($data_module14);
                                    
                                      //$Module->statut_refreshMod14();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "0F") == "0F"){

                                      fwrite($fp1, $hex15);
                                      $data_receive15 = bin2hex(fread($fp1, 1024));
                                      $data_module15=fopen("/var/www/html/plugins/dobiss/data/data_module15.txt","w+");
                                      if (!$data_module15){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_15 not found ');
                                      }
                                      fwrite($data_module15,$data_receive15);
                                      fclose($data_module15);
                                    
                                      //$Module->statut_refreshMod15();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "10") == "10"){

                                      fwrite($fp1, $hex16);
                                      $data_receive16 = bin2hex(fread($fp1, 1024));
                                      $data_module16=fopen("/var/www/html/plugins/dobiss/data/data_module16.txt","w+");
                                      if (!$data_module16){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_16 not found ');
                                      }
                                      fwrite($data_module16,$data_receive16);
                                      fclose($data_module16);
                                    
                                      //$Module->statut_refreshMod16();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "11") == "11"){

                                      fwrite($fp1, $hex17);
                                      $data_receive17 = bin2hex(fread($fp1, 1024));
                                      $data_module17=fopen("/var/www/html/plugins/dobiss/data/data_module17.txt","w+");
                                      if (!$data_module17){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_17 not found ');
                                      }
                                      fwrite($data_module17,$data_receive17);
                                      fclose($data_module17);
                                    
                                      //$Module->statut_refreshMod17();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "12") == "12"){

                                      fwrite($fp1, $hex18);
                                      $data_receive18 = bin2hex(fread($fp1, 1024));
                                      $data_module18=fopen("/var/www/html/plugins/dobiss/data/data_module18.txt","w+");
                                      if (!$data_module18){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_18 not found ');
                                      }
                                      fwrite($data_module18,$data_receive18);
                                      fclose($data_module18);
                                    
                                      //$Module->statut_refreshMod18();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "13") == "13"){

                                      fwrite($fp1, $hex19);
                                      $data_receive19 = bin2hex(fread($fp1, 1024));
                                      $data_module19=fopen("/var/www/html/plugins/dobiss/data/data_module19.txt","w+");
                                      if (!$data_module19){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_19 not found ');
                                      }
                                      fwrite($data_module19,$data_receive19);
                                      fclose($data_module19);
                                    
                                      //$Module->statut_refreshMod19();

                                  }

                                  if ($this->getConfiguration("ModuleAdresse", "14") == "14"){

                                      fwrite($fp1, $hex20);
                                      $data_receive20 = bin2hex(fread($fp1, 1024));
                                      $data_module20=fopen("/var/www/html/plugins/dobiss/data/data_module20.txt","w+");
                                      if (!$data_module20){
                                        log::add('dobiss', 'debug', 'Requête TCP:Module_20 not found ');
                                      }
                                      fwrite($data_module20,$data_receive20);
                                      fclose($data_module20);
                                    
                                      //$Module->statut_refreshMod20();

                                  }
                          
              fclose($fp1);
        }
        } 
        catch (Exception $e) {
			 log::add('dobiss','debug'," except:".$e->getMessage());
        }

    }


        /*if ($data_receive[65] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[65] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[67] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[67] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[69] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[69] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[71] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[71] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[73] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[73] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[75] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[75] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[77] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[77] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[79] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[79] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive1[81] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive1[81] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive1[83] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive1[83] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }/*
        if ($data_receive[85] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[85] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[87] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[87] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }*/


   /*public function statut_receiveMod2() {
     	sleep(2);
        $changed = false;
      	$host = config::byKey (strtolower('IPAddress'),'dobiss');
        $port = config::byKey (strtolower('Port'),'dobiss');
      	$NumberId = config::byKey (strtolower('NumberId'),'dobiss');
        $AddresseModule = $this->getConfiguration('ModuleAdresse');
        $SortieModule = $this->getConfiguration('ModuleOutput');
     

        $fp2 = fsockopen($host, $port, $errno, $errstr);
        if (!$fp2) {
            echo "ERROR: $errno - $errstr<br />\n";
        } else {
          //$hex = hex2bin("af01ff".$AddresseModule."0000000100ffffffffffffaf");
          $hex2 = hex2bin("af01ff020000000100ffffffffffffaf");
          $rgb2 = hexdec($hex2);
		
        fwrite($fp2, $hex2);
		$data_receive2 = bin2hex(fread($fp2, 1024));
        print ($data_receive2);
        sleep(1);
        /*if ($data_receive[65] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[65] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[67] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[67] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[69] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[69] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[71] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[71] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[73] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[73] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive2[75] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive2[75] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }/*
        if ($data_receive[77] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[77] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[79] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[79] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[81] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[81] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[83] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[83] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }/*
        if ($data_receive[85] != 0) {
            $changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[85] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }
        if ($data_receive[87] != 0) {
			$changed = $this->checkAndUpdateCmd('state', 1) || $changed;
		}
        if ($data_receive[87] == 0) {
            $changed = $this->checkAndUpdateCmd('state', 0) || $changed;
        }

        fclose($fp);

        }
        return $data_receive;
        
    }*/
  
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
 
    public function statutOn_Module() {
      $AddresseModule = $this->getConfiguration('ModuleAdresse');
      $SortieModule = $this->getConfiguration('ModuleOutput');
            $data = "1";
            $logical_id = 'state'.$AddresseModule.$SortieModule;
            $cmd = $this->getCmd(null, $logical_id);
            if (is_object($cmd)) {
                $cmd->setCollectDate(date('Y-m-d H:i:s'));
                $cmd->event($data);
            } else {
                log::add('dobiss', 'debug', 'Cmd On Statut error ' . $logical_id);
            }
      
    }

    public function statutOff_Module() {
      $AddresseModule = $this->getConfiguration('ModuleAdresse');
      $SortieModule = $this->getConfiguration('ModuleOutput');
            $data = "0";
            $logical_id = 'state'.$AddresseModule.$SortieModule;
            $cmd = $this->getCmd(null, $logical_id);
            if (is_object($cmd)) {
                $cmd->setCollectDate(date('Y-m-d H:i:s'));
                $cmd->event($data);
            } else {
                log::add('dobiss', 'debug', 'Cmd Off Statut error ' . $logical_id);
            }
      
    }
 
    // Modification icone en fonction de l'appareil enregistré
    public function getPathImgIcon()  {
        $path = "";
        switch ($this->getConfiguration("device")) {
           case 'lumiere' :  $path = 'lumière_icon.png'; break;
           case 'prise' :  $path = 'prise_icon.png'; break;
           case 'ventilation' :  $path = 'ventilation_icon.png'; break;
           case 'volet' :  $path = 'volet_icon.png'; break;
           default:
                        $path = 'dobiss_icon.png';
        }
        return '/plugins/dobiss/images/' . $path;
        
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
			case 'on': 
				$marche = $eqlogic->allumer();
                //$marche = $eqlogic->statutOn_Module();
            	//$marche = $eqlogic->statut_receiveMod1();
            	//$marche = $eqlogic->statut_receiveMod2();

				break;
            case 'off': 
				$arret = $eqlogic->eteindre();
                //$arret = $eqlogic->statutOff_Module();
            	//$arret = $eqlogic->statut_receiveMod1();
            	//$arret = $eqlogic->statut_receiveMod2();

				break;
            	
		}
     }

    /*     * **********************Getteur Setteur*************************** */
}