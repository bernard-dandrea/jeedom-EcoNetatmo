<?php
if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('EcoNetatmo');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
  <!-- Page d'accueil du plugin -->
  <div class="col-xs-12 eqLogicThumbnailDisplay">
    <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
    <!-- Boutons de gestion du plugin -->
    <div class="eqLogicThumbnailContainer">
      <div class="cursor eqLogicAction logoSecondary npd_btn_sync">
        <i class="fas fa-sync-alt" style="color:rgb(169, 52, 206)"></i>
        <br>
        <span>{{Synchronisation}}</span>
      </div>
      <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
        <i class="fas fa-wrench"></i>
        <br>
        <span>{{Configuration}}</span>
      </div>
      <form action="https://www.paypal.com/donate" method="post" target="_top">
        <input type="hidden" name="business" value="FW6UHZEVE8984" />
        <input type="hidden" name="no_recurring" value="1" />
        <input type="hidden" name="item_name" value="{{Encourager le développeur}}" />
        <input type="hidden" name="currency_code" value="EUR" />
        <input type="image" src="plugins/EcoNetatmo/plugin_info/images/beer.png" style="width:60px" border="0" name="submit" title="{{Encourager le développeur}}" alt="{{Encourager le développeur}}" />
      </form>
    </div>
    <legend><i class="fas fa-table"></i> {{Mes Ecocompteurs}}</legend>
    <?php
    if (count($eqLogics) == 0) {
      echo '<br><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun écocompteur Netatmo trouvé, cliquer sur "Synchroniser" pour commencer}}</div>';
    } else {
      // Champ de recherche
      echo '<div class="input-group" style="margin:5px;">';
      echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic">';
      echo '<div class="input-group-btn">';
      echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
      echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
      echo '</div>';
      echo '</div>';
      // Liste des équipements du plugin
      echo '<div class="eqLogicThumbnailContainer">';
      foreach ($eqLogics as $eqLogic) {

        $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
        echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';

        $file = 'plugins/EcoNetatmo/plugin_info/images/' . $eqLogic->getConfiguration('icon') . '.png';
        if (file_exists(__DIR__ . '/../../../../' . $file)) {
          echo '<img src="' . $file . '" height="105" width="95">';
        } else {
          echo '<img src="' . $plugin->getPathImgIcon() . '">';
        }
        echo '<br>';
        echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
        echo '<span class="hiddenAsCard displayTableRight hidden">';
        echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Equipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Equipement non visible}}"></i>';
        echo '</span>';
        echo '</div>';
      }
      echo '</div>';

      echo '<br><br><br><br><br><br>Credits<br><br>';
      echo '<a href="https://www.flaticon.com/free-icons/electric-meter" title="electric meter icons">Electric meter icons created by Freepik - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/free-icons/socket" title="socket icons">Socket icons created by Freepik - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/free-icons/lamp" title="lamp icons">Lamp icons created by Yogi Aprelliyanto - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/free-icons/heater" title="heater icons">Heater icons created by Linector - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/cuisiniere-electrique" title="cuisinière électrique icônes">Cuisinière électrique icônes créées par Freepik - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/chaudiere" title="chaudière icônes">Chaudière icônes créées par Smashicons - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/air" title="air icônes">Air icônes créées par Smashicons - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/ev" title="ev icônes">Ev icônes créées par GOWI - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/free-icons/fridge" title="fridge icons">Fridge icons created by Vichanon Chaimsuk - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/four" title="four icônes">Four icônes créées par ToZ Icon - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/lave-vaisselle" title="lave-vaisselle icônes">Lave-vaisselle icônes créées par Freepik - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/machine-a-laver" title="machine à laver icônes">Machine à laver icônes créées par Freepik - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/cintre" title="cintre icônes">Cintre icônes créées par Freepik - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/multimedia" title="multimédia icônes">Multimédia icônes créées par srip - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/gaz" title="gaz icônes">Gaz icônes créées par surang - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/robinet" title="robinet icônes">Robinet icônes créées par Freepik - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/fr/icones-gratuites/eau-chaude" title="eau chaude icônes">Eau chaude icônes créées par Freepik - Flaticon</a>';
      echo '  -  <a href="https://www.flaticon.com/free-icons/beer" title="beer icons">Beer icons created by Freepik - Flaticon</a>';
    }

    ?>
  </div> <!-- /.eqLogicThumbnailDisplay -->


  <!-- Page de présentation de l'équipement -->
  <div class="col-xs-12 eqLogic" style="display: none;">
    <!-- barre de gestion de l'équipement -->
    <div class="input-group pull-right" style="display:inline-flex;">
      <span class="input-group-btn">
        <!-- Les balises <a></a> sont volontairement fermées à la ligne suivante pour éviter les espaces entre les boutons. Ne pas modifier -->
        <a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
        </a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
        </a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
        </a>
      </span>
    </div>
    <!-- Onglets -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
      <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
      <li role="presentation"><a href="#commandtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>
    </ul>
    <div class="tab-content">
      <!-- Onglet de configuration de l'équipement -->
      <div role="tabpanel" class="tab-pane active" id="eqlogictab">
        <!-- Partie gauche de l'onglet "Equipements" -->
        <!-- Paramètres généraux et spécifiques de l'équipement -->
        <form class="form-horizontal">
          <fieldset>

            <div class="col-lg-8">
              <legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Nom}}</label>
                <div class="col-sm-6">
                  <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display:none;">
                  <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom}}">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Objet parent}}</label>
                <div class="col-sm-6">
                  <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                    <option value="">{{Aucun}}</option>
                    <?php
                    $options = '';
                    foreach ((jeeObject::buildTree(null, false)) as $object) {
                      $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
                    }
                    echo $options;
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Catégorie}}</label>
                <div class="col-sm-6">
                  <?php
                  foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                    echo '<label class="checkbox-inline">';
                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" >' . $value['name'];
                    echo '</label>';
                  }
                  ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Options}}</label>
                <div class="col-sm-6">
                  <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked>{{Activer}}</label>
                  <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked>{{Visible}}</label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label"></label>
                <div class="col-sm-4">
                  <a class="btn btn-default" id="bt_counters_import"><i class="fa fa-refresh"> {{Importer les compteurs}}</i></a>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Module ID}}</label>
                <div class="col-sm-6">
                  <span class="eqLogicAttr" data-l1key="logicalId"></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Consumption Type}}</label>
                <div class="col-sm-6">
                  <span class="eqLogicAttr" data-l1key="configuration" data-l2key="consumption_type"></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label">{{Source Type}}</label>
                <div class="col-sm-6">
                  <span class="eqLogicAttr" data-l1key="configuration" data-l2key="source_type"></span>
                </div>
              </div>

              <div class=" form-group">
                <label class="col-sm-4 control-label">{{Icône}}</label>
                <div class="col-sm-6">
                  <select id="sel_icon" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="icon">
                    <option value="other">{{Electricité}}</option>
                    <option value="sockets">{{Prise}}</option>
                    <option value="lights">{{Lumière}}</option>
                    <option value="heaters">{{Chauffage}}</option>
                    <option value="cooktop">{{Cuisine}}</option>
                    <option value="sanitary_hot_water">{{Chauffe eau}}</option>
                    <option value="air_conditioner">{{Climatisation}}</option>
                    <option value="electric_charger">{{Chargeur VE}}</option>
                    <option value="fridge">{{Réfrigérateur}}</option>
                    <option value="oven">{{Four}}</option>
                    <option value="dishwasher">{{Lave-vaisselle}}</option>
                    <option value="washing_machine">{{Lave-linge}}</option>
                    <option value="tumble_dryer">{{Sèche-linge}}</option>
                    <option value="multimedia">{{Multimédia}}</option>
                    <option value="gas">{{Gaz}}</option>
                    <option value="hot_water">{{Eau chaude}}</option>
                    <option value="cold_water">{{Eau froide}}</option>
                    <option value="Perso1">{{Perso1}}</option>
                    <option value="Perso2">{{Perso2}}</option>
                    <option value="Perso3">{{Perso3}}</option>
                    <option value="Perso4">{{Perso4}}</option>
                    <option value="Perso5">{{Perso5}}</option>
                    <option value="Perso6">{{Perso6}}</option>
                    <option value="Perso7">{{Perso7}}</option>
                    <option value="Perso8">{{Perso8}}</option>
                    <option value="Perso9">{{Perso9}}</option>
                  </select>
                </div>
              </div>
           </fieldset>
        </form>
      </div><!-- /.tabpanel #eqlogictab-->

      <!-- Onglet des commandes de l' équipement -->
      <div role=" tabpanel" class="tab-pane" id="commandtab">

        <br><br>
        <div class="table-responsive">
          <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
              <tr>
                <th class="hidden-xs" style="min-width:50px;width:70px;">ID</th>
                <th style="min-width:200px;width:350px;">{{Nom}}</th>
                <th>{{logicalID}}</th>
                <th>{{Type}}</th>
                <th style="min-width:260px;">{{Options}}</th>
                <th>{{Période}}</th>
                <th>{{Valeur}}
                </th>
                <th style="min-width:80px;width:200px;">{{Actions}}</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div><!-- /.tabpanel #commandtab-->

    </div><!-- /.tab-content -->
  </div><!-- /.eqLogic -->
</div><!-- /.row row-overflow -->

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, id_du_plugin) -->
<?php
include_file('desktop', 'EcoNetatmo', 'js', 'EcoNetatmo');
include_file('core', 'plugin.template', 'js');
?>