<?php

// Last Modified : 2026/08/22 18:46:57

/*
 * Copyright (C) 2026 Bernard Dandrea
 * SPDX-License-Identifier: GPL-3.0-or-later
 * https://www.gnu.org/licenses/gpl-3.0.html
 */

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    ajax::init();


    // From button on Configuration page
    if (init('action') == 'createEquipmentsAndCommands') {

        EcoNetatmo::createEquipmentsAndCommands();

        ajax::success();
    }


    if (init('action') == 'counters_import') {

        $eqLogic = EcoNetatmo::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new \Exception(__('EcoNetatmo eqLogic non trouvé : ', __FILE__) . init('id'));
        }
        $consumption_type = $eqLogic->getConfiguration('consumption_type');
        $source_type = $eqLogic->getConfiguration('source_type');
        $EcoNetatmo  = $eqLogic->counters_import($consumption_type,$source_type);
        ajax::success($EcoNetatmo);
    }

    if (init('action') == 'enable_cron') {
        EcoNetatmo::enable_cron(init('enable'));
        ajax::success();
    }


    throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
