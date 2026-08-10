<?php

// Last Modified : 2026/08/10 18:35:05

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
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';


if (!class_exists('netatmoApi')) {
    require_once __DIR__ . '/netatmoApi.class.php';
}


class EcoNetatmo extends eqLogic
{

    private static $_client = null;

    public static function enable_cron($_enable)
    {
        $cron_EcoNetatmo = cron::byClassAndFunction('EcoNetatmo', 'update');
        $schedule = '*/10 * * * *';
        if ($_enable == '1') {
            log::add('EcoNetatmo', 'debug', __('Activation du cron de EcoNetatmo', __FILE__));
            if (!is_object($cron_EcoNetatmo)) {
                $cron_EcoNetatmo = new cron();
                $cron_EcoNetatmo->setClass('EcoNetatmo');
                $cron_EcoNetatmo->setFunction('update');
                $cron_EcoNetatmo->setEnable(1);
                $cron_EcoNetatmo->setDeamon(0);
                $cron_EcoNetatmo->setSchedule($schedule);
                $cron_EcoNetatmo->setTimeout(1);
            } else {
                $cron_EcoNetatmo->setEnable(1);
            }
            $cron_EcoNetatmo->save();
        } else {
            log::add('EcoNetatmo', 'debug', __('Désactivation du cron de EcoNetatmo', __FILE__));
            if (is_object($cron_EcoNetatmo)) {
                $cron_EcoNetatmo->remove();
            }
        }
    }

    public static function FormatArrayForLog($value)
    {
        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;
        $encoded = json_encode($value, $options);

        if ($encoded === false) {
            return json_encode((string) $value, $options);
        }

        return $encoded;
    }

    public static function getClient()
    {
        if (self::$_client == null) {
            self::$_client = new netatmoApi(
                array(
                    'client_id' => config::byKey('client_id', 'EcoNetatmo'),
                    'client_secret' => config::byKey('client_secret', 'EcoNetatmo'),
                    'access_token' => config::byKey('access_token', 'EcoNetatmo'),
                    'refresh_token' => config::byKey('refresh_token', 'EcoNetatmo'),
                    'object_cb' => 'EcoNetatmo',
                    'func_cb' => 'saveTokens'
                )
            );
        }
        return self::$_client;
    }
    static function saveTokens($p_token)
    {
        foreach ($p_token as $key => $value) {
            log::add('EcoNetatmo', 'debug', 'saveTokens ' . $key . ' -> ' . $value);
            config::save($key, $value, 'EcoNetatmo');
        }
    }

    public static function refresh_token()
    {
        // avec Netatmo, une fois que le token est expiré, on ne peut plus faire de refresh
        // (ce qui est normalement bien géré avec getAccessTokenFromRefreshToken)
        // aussi, on fait un refresh du token toutes les heures pour être sur qu'il est toujours valide
        log::add('EcoNetatmo', 'info', 'Refresh token');
        // dans netatmoApi.class.php, remplacer le private par public devant la fonction getAccessTokenFromRefreshToken
        self::getClient()->getAccessTokenFromRefreshToken();
    }


    public static function createEquipmentsAndCommands()
    {
        log::add('EcoNetatmo', 'debug', __FUNCTION__);

        $devicelist = self::getClient()->api("homesdata", "GET");
        log::add('EcoNetatmo', 'debug', json_encode($devicelist));
        foreach ($devicelist['homes'] as $homes) {
            log::add('EcoNetatmo', 'debug', 'homes id: ' . $homes['id']);
            foreach ($homes['modules'] as $module) {
                log::add('EcoNetatmo', 'debug', 'Module id ' .  $module['id'] . ' ' . $module['name']);
                $module_id = $module['id'];
                $ignore = false;
                if (substr($module_id, -2, 1) == '#') {
                    if (!is_object(self::byLogicalId($module_id, 'EcoNetatmo'))) {
                        $index = substr($module_id, strpos($module_id, '#') + 1);
                        switch (true) {
                            case ($index >= '0' and $index <= '5'):
                                $consumption_type = 'electrical';
                                $source_type = 'other';
                                break;
                            case ($index == '6'):
                                $consumption_type = 'fluid';
                                $source_type = 'gas';
                                $ignore = true;
                                break;
                            case ($index == '7'):
                                $consumption_type = 'fluid';
                                $source_type = 'hot_water';
                                $ignore = true;
                                break;
                            case ($index == '8'):
                                $consumption_type = 'fluid';
                                $source_type = 'cold_water';
                                $ignore = true;
                                break;
                        }
                        if ($ignore == false) {
                            $eqLogic = new EcoNetatmo();
                            log::add('EcoNetatmo', 'info', __('Création eqLogic', __FILE__) . ' ' . $module_id . ' ' . __('type de consommation', __FILE__) . ' ' . $consumption_type . ' ' . __('type de source', __FILE__) . ' ' . $source_type);
                            if (!isset($module['module_name']) || $module['module_name'] == '') {
                                $module['module_name'] = $module['_id'];
                            }
                            $eqLogic->setName($module['name']);
                            $eqLogic->setIsEnable(1);
                            $eqLogic->setCategory('energy', 1);
                            $eqLogic->setIsVisible(1);
                            $eqLogic->setEqType_name('EcoNetatmo');
                            $eqLogic->setLogicalId($module_id);
                            $eqLogic->setConfiguration('consumption_type', $consumption_type);
                            $eqLogic->setConfiguration('source_type', $source_type);
                            $eqLogic->setConfiguration('source_type', $source_type);
                            $eqLogic->setConfiguration('icon', $source_type);

                            $eqLogic->save();

                            $eqLogic->Counters_Import($consumption_type, $source_type);
                        }
                    } else {
                        log::add('EcoNetatmo', 'info', __('eqLogic déjà créé', __FILE__) . ' ' .  $module_id);
                    }
                }
            }
        }
    }

    public function Counters_Import($_consumption_type, $_source_type)
    {

        log::add('EcoNetatmo', 'debug', __FUNCTION__ . ' ' . $this->getName() . ': ' . '  ' . __('type de consommation', __FILE__) . ' ' . $_consumption_type . ' ' . __('type de source', __FILE__) . ' ' . $_source_type);

        switch ($_consumption_type) {
            case ('electrical'):
                $this->create_counter('Cumulated energy no contract', 'sum_energy_elec', $consumption_type);
                $this->create_counter('Cumulated energy with contract', 'sum_energy_elec$0', $consumption_type, '0');
                $this->create_counter('Cumulated energy peak period with contract', 'sum_energy_elec$1', $consumption_type, '0');
                $this->create_counter('Cumulated energy off peak period with contract', 'sum_energy_elec$2', $consumption_type, '0');
                $this->create_counter('Sum energy price no contract', 'sum_energy_price', $consumption_type, '0');
                $this->create_counter('Sum energy price with contract', 'sum_energy_price$0', $consumption_type, '0');
                $this->create_counter('Sum energy price peak period with contract', 'sum_energy_price$1', $consumption_type, '0');
                $this->create_counter('Sum energy price off peak period with contract', 'sum_energy_price$2', $consumption_type, '0');

                break;
            case ('fluid'): // pas géré par Netatmo, mais on crée quand même la commande pour l'historisation
                $this->create_counter($_source_type, 'sum_energy_elec', $consumption_type);
                break;
        }
    }

    private function create_counter($_name, $_type, $_consumption_type, $_collected = '1')
    // crée la commande type info
    {
        log::add('EcoNetatmo', 'info', __FUNCTION__ . ' ' . $this->getName() . '  name = ' . $_name . '  type = ' . $_type);
        if (is_object(cmd::byEqLogicIdAndLogicalId($this->id, $_type))) {
            log::add('EcoNetatmo', 'info', __FUNCTION__ . ' ' . $this->getName() . ' ' . __('commande déjà créée . type = ', __FILE__) . $_type);
        } else {
            $cmd = new EcoNetatmoCmd();
            $cmd->setName($_name);
            $cmd->setEqLogic_id($this->getId());
            $cmd->setLogicalId($_type);
            $cmd->setIsVisible(1);
            $cmd->setIsHistorized(1);
            $cmd->setConfiguration('scale', '30min');
            $cmd->setConfiguration('isPrincipale', '0');
            $cmd->setConfiguration('isCollected', $_collected);
            $cmd->setConfiguration('historizeMode', 'none');
            $cmd->setConfiguration('historyPurge', '-1 month');
            $cmd->setConfiguration('repeatEventManagement', 'always');
            $cmd->setTemplate('dashboard', 'core::line');
            $cmd->setTemplate('mobile', 'core::line');
            $cmd->setType('info');
            $cmd->setSubType('numeric');
            $cmd->setDisplay('generic_type', 'GENERIC_INFO');
            $cmd->setDisplay('graphType', 'column');
            $cmd->save();
        }
    }


    function refresh_counters()
    {
        log::add('EcoNetatmo', 'info', __FUNCTION__ . ' ' . $this->getLogicalId() . ' ' . $this->getName());
        $module_id = $this->getLogicalID();
        foreach ($this->getCmd() as $cmd) {
            if ($cmd->getConfiguration('isCollected') == 1 && $cmd->getType() == 'info') {

                $scale = $cmd->getConfiguration('scale', '30min');
                switch ($scale) {
                    case '30min':
                        $step_time = 30 * 60;
                        break;
                    case '1hour':
                        $step_time = 60 * 60;
                        break;
                    case '3hours':
                        $step_time = 3 * 60 * 60;
                        break;
                    case '1day':
                        $step_time = 24 * 60 * 60;
                        break;
                }

                $beg_time = $cmd->getConfiguration('last_update', '');
                if ($beg_time == '') {
                    $beg_time = strtotime('today -7 days');
                }

                log::add('EcoNetatmo', 'info', $this->getLogicalId() . ' ' . $this->getName() . ' : ' . __('lecture de la mesure depuis', __FILE__) .  ' ' . date('Y-m-d H:i:s', $beg_time) . ' (' . $beg_time . ')');
                $measurelist = self::getClient()->api(
                    "getmeasure",
                    "GET",
                    array(
                        "device_id" => substr($module_id, 0, strpos($module_id, '#')),
                        "module_id" => $module_id,
                        "type" => $cmd->getLogicalID(),
                        "scale" => $scale,
                        "date_begin" => $beg_time,
                    )
                );

                if (isset($measurelist['error'])) {
                    log::add('EcoNetatmo', 'error', $this->getLogicalId() . ' ' . $this->getName() . ' : ' . __('erreur Netatmo', __FILE__) . ' ' . $measurelist['error']['code'] . ' ' . $measurelist['error']['message']);
                } elseif (empty($measurelist)) {
                    log::add('EcoNetatmo', 'info', $this->getLogicalId() . ' ' . $this->getName() . ' : ' . __('pas de modification depuis', __FILE__) . ' ' . date('Y-m-d H:i:s', $beg_time) . ' (' . $beg_time . ')');
                } else {
                    log::add('EcoNetatmo', 'debug', $this->getLogicalId() . ' ' . $this->getName() . ' : ' . __('liste des mesures depuis', __FILE__) . ' ' . date('Y-m-d H:i:s', $beg_time) . ' (' . $beg_time . ')' . ' : ' . self::FormatArrayForLog($measurelist));
                    $last_update = $beg_time;
                    foreach ($measurelist as $measures) {
                        log::add('EcoNetatmo', 'debug', $this->getLogicalId() . ' ' . $this->getName() . ' : ' . __('mesures', __FILE__) . ' : ' . self::FormatArrayForLog($measures));
                        if (isset($measures['value']) && isset($measures['beg_time'])) {
                            $value = $measures['value'];
                            $beg_time = $measures['beg_time'];
                            log::add('EcoNetatmo', 'debug', $this->getLogicalId() . ' ' . $this->getName() . ' : ' . __('début', __FILE__) . ' ' . date('Y-m-d H:i:s', $beg_time)  . ' (' . $beg_time . ')' . ' ' . __('intervalle', __FILE__) . ' ' . $step_time . ' ' . __('values', __FILE__) . ' ' . self::FormatArrayForLog($measures['value']));
                            $x = 0;
                            foreach ($measures['value'] as $value) {
                                log::add('EcoNetatmo', 'info', $this->getLogicalId() . ' ' . $this->getName() . ' : ' .  $x . ' ' . __('début', __FILE__) . ' ' . date('Y-m-d H:i:s', $beg_time)  . ' (' . $beg_time . ')' . ' ' . __('valeur', __FILE__) . ' ' . $value[0]);
                                if ($value[0] != 0) {
                                    $eqLogic = $_cmd->getEqlogic();
                                    $eqLogic->checkAndUpdateCmd($_cmd, $value[0], date('Y-m-d H:i:s', $beg_time));
                                }
                                $last_update = $beg_time;
                                $beg_time += $step_time;
                                $x += 1;
                            }
                        }
                    }
                    $last_update += $step_time / 2;
                    $cmd->setConfiguration('last_update', $last_update);
                    log::add('EcoNetatmo', 'debug', $this->getLogicalId() . ' ' . $this->getName() . ' : ' . __('dernière mise à jour', __FILE__) . ' '  . date('Y-m-d H:i:s', $last_update)  . ' (' . $last_update . ')');

                    $cmd->save();
                }
            }
        }
    }

    public function preInsert()
    {
        if ($this->getConfiguration('type', '') == "") {
            $this->setConfiguration('type', 'EcoNetatmo');
        }
    }

    public function postInsert()
    {
        $this->postUpdate();
    }

    public function postUpdate()
    {
        if ($this->getConfiguration('type', '') == 'EcoNetatmo') {
            $cmd = $this->getCmd(null, 'Refresh');
            if (!is_object($cmd)) {
                $cmd = new EcoNetatmoCmd();
                $cmd->setName('Refresh');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setLogicalId('Refresh');
                $cmd->setIsVisible(1);
                $cmd->setOrder('-1');
                $cmd->setDisplay('generic_type', 'GENERIC_INFO');
                $cmd->save();
            }
        }
    }


    public static function cron10()
    {
        $cron_EcoNetatmo = cron::byClassAndFunction('EcoNetatmo', 'update');
        if (!is_object($cron_EcoNetatmo)) {
            log::add('EcoNetatmo', 'info', __('Lancement de cron', __FILE__));
            EcoNetatmo::update();
        }
    }

    public static function update()
    {
        // rafraichit le token toutes les heures
        $autorefresh = '0 * * * *';
        $c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
        if ($c->isDue()) {
            EcoNetatmo::refresh_token();
        }

        foreach (eqLogic::byTypeAndSearchConfiguration('EcoNetatmo', '"type":"EcoNetatmo"') as $eqLogic) {
            if ($eqLogic->getIsEnable()) {
                log::add('EcoNetatmo', 'info', 'cron Refresh Info  : ' . $eqLogic->getName());
                $eqLogic->refresh_counters();
            }
        }
    }
}

class EcoNetatmoCmd extends cmd
{

    public function execute($_options = null)
    {
        $eqLogic = $this->getEqLogic();
        if (!is_object($eqLogic) || $eqLogic->getIsEnable() != 1) {
            throw new \Exception(__('Equipement desactivé impossible d\éxecuter la commande : ' . $this->getHumanName(), __FILE__));
        }

        // Commande refresh
        if ($eqLogic->getLogicalId() == 'Refresh') {
            return $eqLogic->refresh_counters();
        }
    }


    public function dontRemoveCmd()
    {
        $eqLogic = $this->getEqLogic();
        if (is_object($eqLogic)) {
            if ($this->getLogicalId() == 'Refresh') {
                return true;
            } else {
                return false;
            }
        }
    }
}
