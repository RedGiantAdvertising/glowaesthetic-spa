<?php
  $title = "Modifier un utilisateur";
  include_once("inc/header.php");

  if(!isset($_GET['id'])) {
    header("Location: clients.php");
  }
  $editingClient = $_GET['id'];
  $editingClientData = getAnythingWithIdAndTable($editingClient, "clients", "AND is_deleted = ?", "i", [0]);
  if(!$editingClientData) {
    header("Location: clients.php");
  }
  $allowRoleChange = true;
  $allowManagerChange = true;
  $userTypes = getSpecificKeysFormAssociativeArray(getAnythingFromtable("userTypes"), ['id', "pretty_name"]);
?>
<div class="container">
  <form class="" action="api.php" method="post" onComplete="defaultApiRes" before="showLoading">
   <div class="card">
     <div class="card-header">
       <h5 class="mb-0">Modifier client</h5>
     </div>
     <div class="card-body">
       <input type="hidden" name="client_id" value="<?= $editingClientData['id']; ?>" />

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="clientFirstname">Prenom:</label>
         <div class="col-md-10">
           <input type="text" name="clientFirstname" class="form-control" id="clientFirstname" placeholder="Entrer un prénom" value="<?= $editingClientData['firstname']; ?>">
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="clientLastname">Nom:</label>
         <div class="col-md-10">
           <input type="text" name="clientLastname" class="form-control" id="clientLastname" placeholder="Entrer un nom" value="<?= $editingClientData['lastname']; ?>">
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="clientPhone">Nom:</label>
         <div class="col-md-10">
           <input type="text" name="clientPhone" class="form-control" id="clientPhone" placeholder="Entrer un nom" value="<?= $editingClientData['numberphone']; ?>">
         </div>
       </div>

     </div>
     <div class="card-footer">
       <button class="btn btn-primary" name="updateClient">Mettre à jour <i class="fa fa-edit"></i></button>
     </div>
   </div>
 </form>
</div>
<?php include_once("inc/footer.php"); ?>
