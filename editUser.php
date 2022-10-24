<?php
  $title = "Modifier un utilisateur";
  include_once("inc/header.php");

  if(!isset($_GET['id'])) {
    header("Location: users.php");
  }
  $editingUser = $_GET['id'];
  $editingUserData = getAnythingWithIdAndTable($editingUser, "users", "AND is_deleted = ?", "i", [0]);
  if(!$editingUserData) {
    header("Location: users.php");
  }
  $allowRoleChange = true;
  $allowManagerChange = true;
  $userTypes = getSpecificKeysFormAssociativeArray(getAnythingFromtable("userTypes"), ['id', "pretty_name"]);
?>
<div class="container">
  <form class="" action="api.php" method="post" onComplete="defaultApiRes" before="showLoading">
   <div class="card">
     <div class="card-header">
       <h5 class="mb-0">Modifier utilisateurs</h5>
     </div>
     <div class="card-body">
       <input type="hidden" name="id" value="<?= $editingUserData['id']; ?>" />
       <div class="form-group row">
         <label class="col-form-label col-md-2" for="username"><?= getTranslation("username"); ?>: <span class="text-danger">*</span></label>
         <div class="col-md-10">
           <input type="text" name="username" class="form-control" id="username" placeholder="Entrer un <?= strtolower(getTranslation("username")); ?>" value="<?= $editingUserData['username']; ?>" required disabled>
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="email"><?= getTranslation("email"); ?>: <span class="text-danger">*</span></label>
         <div class="col-md-10">
           <input type="email" name="email" class="form-control" id="email" placeholder="Entrer un <?= strtolower(getTranslation("email")); ?>" value="<?= $editingUserData['email']; ?>" required>
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="password"><?= getTranslation("password"); ?>:</label>
         <div class="col-md-10">
           <div class="input-group">
             <input type="password" name="password" class="form-control" id="password" placeholder="Entrer un <?= strtolower(getTranslation("password")); ?>">
             <span class="input-group-btn" title="Generate password"><button type="button" class="btn btn-default generatePassword" style="border: 1px solid #ccc; border-radius: 0;"><?= getTranslation("generatePassword"); ?></button></span>
             <span class="input-group-btn" title="Show password"><button type="button" class="btn btn-default showPassword" style="border: 1px solid #ccc; border-top-left-radius: 0; border-bottom-left-radius: 0;"><i class="fa fa-eye"></i></button></span>
           </div>
           <small class="text-muted">Laissez vide le mot de passe si vous ne souhaitez pas le modifier.</small>
         </div>
       </div>

       <?php if($allowRoleChange) { ?>
         <div class="form-group row mt-2">
           <label class="col-form-label col-md-2" for="type"><?= getTranslation("userRole"); ?>: <span class="text-danger">*</span></label>
           <div class="col-md-10">
             <select name="type" class="form-control" id="type" placeholder="Sélectionnez un <?= strtolower(getTranslation("userRole")); ?>" required>
               <option value="">Sélectionnez un <?= strtolower(getTranslation("userRole")); ?></option>
               <?php
                  foreach($userTypes as $userType) {
                    $userTypeId = $userType['id'];
                    $userTypeName = $userType['pretty_name'];
                    if($userTypeId == $editingUserData['type']) {
                      echo "<option value=".$userTypeId." selected>$userTypeName</option>";
                    }
                    else {
                      echo "<option value=".$userTypeId.">$userTypeName</option>";
                    }
                  }
               ?>
             </select>
           </div>
         </div>
       <?php } ?>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="firstname">Prenom:</label>
         <div class="col-md-10">
           <input type="text" name="firstname" class="form-control" id="firstname" placeholder="Entrer un prénom" value="<?= $editingUserData['firstname']; ?>">
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="lastname">Nom:</label>
         <div class="col-md-10">
           <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Entrer un nom" value="<?= $editingUserData['lastname']; ?>">
         </div>
       </div>

     </div>
     <div class="card-footer">
       <button class="btn btn-primary" name="updateUser">Mettre à jour <i class="fa fa-edit"></i></button>
     </div>
   </div>
 </form>
</div>
<?php include_once("inc/footer.php"); ?>
