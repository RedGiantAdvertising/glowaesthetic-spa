<?php
  $title = "Ajouter un client";
  include_once("inc/header.php");
  $userTypes = getSpecificKeysFormAssociativeArray(getAnythingFromtable("userTypes"), ['id', "pretty_name"]);
?>
<div class="container">
  <form class="" action="api.php" method="post" onComplete="defaultApiAndTryBackRes" before="showLoading" id="clientForm">
   <div class="card">
     <div class="card-header">
       <h5 class="mb-0">Ajouter un client</h5>
     </div>
     <div class="card-body">

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="clientFirstname">Prénom:</label>
         <div class="col-md-10">
           <input type="text" name="clientFirstname" class="form-control" id="clientFirstname" placeholder="Entrer un prénom" required>
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="clientLastname">Nom:</label>
         <div class="col-md-10">
           <input type="text" name="clientLastname" class="form-control" id="clientLastname" placeholder="Entrer un nom" required>
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="clientPhone">Numéro de téléphone</label>
         <div class="col-md-10">
           <input type="text" name="clientPhone" class="form-control" id="clientPhone" placeholder="Entrer numéro de téléphone" required>
         </div>
       </div>

     </div>
     <div class="card-footer">
       <button class="btn btn-primary" type="button" name="addClient">AddClient <i class="fa fa-plus-circle"></i></button>
     </div>
   </div>
 </form>
</div>
<?php include_once("inc/footer.php"); ?>
