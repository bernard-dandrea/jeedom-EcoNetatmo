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




/* Permet la réorganisation des commandes dans l'équipement */
$("#table_cmd").sortable({
    axis: "y",
    cursor: "move",
    items: ".cmd",
    placeholder: "ui-state-highlight",
    tolerance: "intersect",
    forcePlaceholderSize: true
})

$("#table_cmd").delegate(".listEquipementInfo", 'click', function () {
    var el = $(this)
    jeedom.cmd.getSelectModal({ cmd: { type: 'info' } }, function (result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']')
        calcul.atCaret('insert', result.human)
    })
})

$("#table_cmd").delegate(".listEquipementAction", 'click', function () {
    var el = $(this)
    var subtype = $(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').value()
    jeedom.cmd.getSelectModal({ cmd: { type: 'action', subType: subtype } }, function (result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.attr('data-input') + ']')
        calcul.atCaret('insert', result.human);
    })
})

/* Fonction permettant l'affichage des commandes dans l'équipement */
function addCmdToTable(_cmd) {

    if (!isset(_cmd)) {
        var _cmd = { configuration: {} }
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {}
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
    tr += '<td class="hidden-xs">'
    tr += '<span class="cmdAttr" data-l1key="id"></span>'
    tr += '</td>'
    tr += '<td>'
    tr += '<div class="input-group">'
    tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">'
    tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>'
    tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>'
    tr += '</div>'
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display:none;margin-top:5px;" title="{{Commande info liée}}">'
    tr += '<option value="">{{Aucune}}</option>'
    tr += '</select>'
    tr += '</td>'
    tr += '<td  class="hidden-xs">';
    tr += '<span class="cmdAttr" data-l1key="logicalId"></span>'
    tr += '</td>';
    tr += '<td>'
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>'
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>'
    tr += '</td>'
    tr += '<td>'
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> '
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> '
    tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label> '
    if (init(_cmd.type) == "info") {
        tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="isCollected" checked/>{{Activer}}</label> ';
    }

    tr += '<div style="margin-top:7px;">'
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
    tr += '</div>'
    tr += '</td>'


    if (init(_cmd.type) == "info") {
        tr += '<td>';
        tr += '<select id="sel_cron" class="cmdAttr form-control" data-l1key="configuration" data-l2key="scale"> '
        tr += '<option value="30min">{{30 minutes}}</option> '
        tr += '<option value="1hour">{{Une heure}}</option> '
        tr += '<option value="3hours">{{3 heures}}</option> '
        tr += '<option value="1day">{{Un jour}}</option> '
        tr += '</select> '
        tr += '</td>';
    }
    else {
        tr += '<td>';
        tr += '</td>';
    }

    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>';
    tr += '</td>';
    tr += '<td>'
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> '
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> Tester</a>'
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i></td>'
    tr += '</tr>'
    $('#table_cmd tbody').append(tr)
    var tr = $('#table_cmd tbody tr').last()
    jeedom.eqLogic.buildSelectCmd({
        id: $('.eqLogicAttr[data-l1key=id]').value(),
        filter: { type: 'info' },
        error: function (error) {
            $('#div_alert').showAlert({ message: error.message, level: 'danger' })
        },
        success: function (result) {
            tr.find('.cmdAttr[data-l1key=value]').append(result)
            tr.setValues(_cmd, '.cmdAttr')
            jeedom.cmd.changeType(tr, init(_cmd.subType))
        }
    })
}

function printEqLogic(_eqLogic) {
    $EcoNetatmotype = _eqLogic.configuration.type;
}

$('.npd_btn_sync').on('click', function (e) {
    e.preventDefault()
    $('#div_alert').showAlert({ message: '{{Synchronisation en cours}}', level: 'warning' })
    $.ajax({
        type: "POST",
        url: "plugins/EcoNetatmo/core/ajax/EcoNetatmo.ajax.php", // url du fichier php
        // LA FONCTION createEquipmentsAndCommands DOIT ETRE DEFINIE DANS LE FICHIER CI-DESSUS
        data: {
            action: "createEquipmentsAndCommands",
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error)
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({ message: data.result, level: 'danger' })
                return
            }
            $('#div_alert').showAlert({ message: '{{Synchronisation réussie}}', level: 'success' })
            setTimeout(function () {
                location.reload()
            }, 2000)
        }
    })
})

$("#table_cmd").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });

$('#bt_counters_import').on('click', function () {

    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/EcoNetatmo/core/ajax/EcoNetatmo.ajax.php", // url du fichier php
        // LA FONCTION counters_import DOIT ETRE DEFINIE DANS LE FICHIER CI-DESSUS
        data: {
            action: "counters_import",
            id: $('.eqLogicAttr[data-l1key=id]').value(),
            consumption_type: $('.eqLogicAttr[data-l1key="configuration"][data-l2key="consumption_type"]').value(),
            source_type: $('.eqLogicAttr[data-l1key="configuration"][data-l2key="source_type"]').value(),
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, $('#div_DetectBin'));
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({ message: data.result, level: 'danger' });
                return;
            }
            window.location.reload();
        }
    });
});

/* POUR TESTS */
$('.npd_btn_token').on('click', function (e) {
    $('#md_modal').dialog({ title: "{{Générer token}}" });
    var ip = location.host;
    window.open('https://api.netatmo.net/oauth2/authorize?client_id=64b0fe3a8de72c874006ccfd&scope=read_magellan&redirect_uri='+ encodeURIComponent('http://localhost/index.php?p=EcoNetatmo&m=EcoNetatmo'));
});



