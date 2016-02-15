<?php

if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'salat');
$eqLogics = eqLogic::byType('salat');

?>

<div class="row row-overflow">
  <div class="col-lg-2 col-md-3 col-sm-4">
    <div class="bs-sidebar">
      <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
        <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un équipement}}</a>
        <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
        <?php
        foreach ($eqLogics as $eqLogic) {
          echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
        }
        ?>
      </ul>
    </div>
  </div>

  <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
    <legend>{{Mes salat}}
    </legend>
    <div class="eqLogicThumbnailContainer">
      <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
        <center>
          <i class="fa fa-plus-circle" style="font-size : 7em;color:#00979c;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>Ajouter</center></span>
      </div>
      <?php
      foreach ($eqLogics as $eqLogic) {
        $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
        echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff ; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
        echo "<center>";
        echo '<img src="plugins/salat/doc/images/salat_icon.png" height="105" width="95" />';
        echo "</center>";
        echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
        echo '</div>';
      }
      ?>
    </div>
  </div>


  <div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
    <div class="row">
      <div class="col-sm-6">
        <form class="form-horizontal">
          <fieldset>
            <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i>  {{Général}}
              <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i>
            </legend>
            <div class="form-group">
              <label class="col-md-2 control-label">{{Lieu salat}}</label>
              <div class="col-md-3">
                <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement salat}}"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" >{{Objet parent}}</label>
              <div class="col-md-3">
                <select class="form-control eqLogicAttr" data-l1key="object_id">
                  <option value="">{{Aucun}}</option>
                  <?php
                  foreach (object::all() as $object) {
                    echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label">{{Catégorie}}</label>
              <div class="col-md-8">
                <?php
                foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                  echo '<label class="checkbox-inline">';
                  echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                  echo '</label>';
                }
                ?>

              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" ></label>
              <div class="col-sm-9">
                <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Activer}}" data-l1key="isEnable" checked/>
                <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Visible}}" data-l1key="isVisible" checked/>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">{{Commentaire}}</label>
              <div class="col-md-8">
                <textarea class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="commentaire" ></textarea>
              </div>
            </div>

          </fieldset>

        </form>
      </div>

      <div id="infoNode" class="col-sm-6">
        <form class="form-horizontal">
          <fieldset>
            <legend>{{Configuration}}</legend>

            <div class="form-group">
              <label class="col-md-2 control-label">{{Géolocalisation}}</label>
              <div class="col-md-3">
                <select class="form-control eqLogicAttr configuration" id="geoloc" data-l1key="configuration" data-l2key="geoloc">
                  <option value="none">{{Aucun}}</option>
                  <?php
                  foreach (eqLogic::byType('geoloc') as $geoloc) {
                    foreach (geolocCmd::byEqLogicId($geoloc->getId()) as $geoinfo) {
                        if ($geoinfo->getConfiguration('mode') == 'fixe' || $geoinfo->getConfiguration('mode') == 'dynamic') {
                            echo '<option value="' . $geoinfo->getId() . '">' . $geoinfo->getName() . '</option>';
                        }
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-2 control-label">{{Angle Fajr}}</label>
              <div class="col-md-3">
                <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="fajr" placeholder="ex : 12" title="Angle pour le calcul de Fajr (12 pour l'UOIF)"/>
              </div>

              <label class="col-md-2 control-label">{{Angle Isha}}</label>
              <div class="col-md-3">
                <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="isha" placeholder="ex : 12" title="Angle pour le calcul d'Isha (12 pour l'UOIF)"/>
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-2 control-label">{{Méthode de Calul}}</label>
              <div class="col-md-3">
                <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="method" placeholder="ex : 2" title="Méthode de calcul tel qu'utilisé par itools"/>
              </div>

              <label class="col-md-2 control-label">{{Madzab}}</label>
              <div class="col-md-3">
                <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="madzab" placeholder="ex : 2" title="Madzab utilisé par itools"/>
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-2 control-label">{{Heure d'été}}</label>
              <div class="col-md-3">
                <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="dst" placeholder="ex : 0" title="Décalage heure d'été, normalement laisser 0"/>
              </div>

              <label class="col-md-2 control-label">{{Ajustement UOIF}}</label>
              <div class="col-md-3">
                <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="uoif" placeholder="ex : 1" title="Indiquer 1 pour appliquer les ajustements de l'UOIF"/>
              </div>
            </div>

            <div class="form-group">
                       <label class="col-md-2 control-label">{{Commande Annonce}}</label>
                        <div class="col-md-3">
                          <div class="input-group">
                              <input type="text"  class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="alert" />
                              <span class="input-group-btn">
                                  <a class="btn btn-default cursor" title="Rechercher une commande" id="bt_selectMailCmd"><i class="fa fa-list-alt"></i></a>
                              </span>
                          </div>
                      </div>

                      <label class="col-md-2 control-label">{{Commande Action}}</label>
                       <div class="col-md-3">
                         <div class="input-group">
                             <input type="text"  class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="command" />
                             <span class="input-group-btn">
                                 <a class="btn btn-default cursor" title="Rechercher une commande" id="bt_selectActCmd"><i class="fa fa-list-alt"></i></a>
                             </span>
                         </div>
                     </div>
                  </div>

          </fieldset>
        </form>
      </div>
    </div>

    <legend>{{Informations}}</legend>

    <form class="form-horizontal">
              <fieldset>
                  <div class="form-actions">
                      <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> Supprimer</a>
                      <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> Sauvegarder</a>
                  </div>
              </fieldset>
          </form>
  <br>

    <table id="table_cmd" class="table table-bordered table-condensed">
      <thead>
        <tr>
          <th style="width: 50px;">#</th>
          <th style="width: 300px;">{{Nom}}</th>
          <th style="width: 250px;">{{Valeur}}</th>
          <th style="width: 150px;">{{Paramètres}}</th>
          <th style="width: 50px;"></th>
        </tr>
      </thead>
      <tbody>

      </tbody>
    </table>

    <form class="form-horizontal">
      <fieldset>
        <div class="form-actions">
          <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
          <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
        </div>
      </fieldset>
    </form>

  </div>
</div>

<?php include_file('desktop', 'salat', 'js', 'salat'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
