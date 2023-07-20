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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>


<form class="form-horizontal">
  <fieldset>

    <div class="row">

      <div class="form-group">
        <label class="col-sm-4 control-label" for="client_id"> {{Client ID}}</label>
        <div class="col-sm-4">
          <input type="text" class="configKey form-control" data-l1key="client_id" id="client_id" placeholder="" autocomplete="off">
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4  control-label" for="client_secret">{{Client secret}}</label>
        <div class="col-sm-4">
          <input type="password" class="configKey form-control" data-l1key="client_secret" id="client_secret" placeholder="" autocomplete="off">
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label" for="access_token">{{Access token}}</label>
        <div class="col-sm-4">
          <input type="text" class="configKey form-control" data-l1key="access_token" id="access_token" placeholder="" autocomplete="off">
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label" for="refresh_token">{{Refresh token}}</label>
        <div class="col-sm-4">
          <input type="text" class="configKey form-control" data-l1key="refresh_token" id="refresh_token" placeholder="" autocomplete="off">
        </div>
      </div>



    </div>
  </fieldset>
</form>
