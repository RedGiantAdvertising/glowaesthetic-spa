<?php
  $title = "Ajouter un utilisateur";
  include_once("inc/header.php");
  $userTypes = getSpecificKeysFormAssociativeArray(getAnythingFromtable("userTypes"), ['id', "pretty_name"]);
?>
<div class="container">
  <form class="" action="api.php" method="post" onComplete="defaultApiAndTryBackRes" before="showLoading">
   <div class="card">
     <div class="card-header">
       <h5 class="mb-0">Ajouter un utilisateur</h5>
     </div>
     <div class="card-body">
       <div class="form-group row">
         <label class="col-form-label col-md-2" for="username"><?= getTranslation("username"); ?>: <span class="text-danger">*</span></label>
         <div class="col-md-10">
           <input type="text" name="username" class="form-control" id="<?= getTranslation("username"); ?>" placeholder="Entrer un <?= strtolower(getTranslation("username")); ?>" required>
           <div class="invalid-feedback usernameInvalid" style="display: none;"></div>
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="email"><?= getTranslation("email"); ?>: <span class="text-danger">*</span></label>
         <div class="col-md-10">
           <input type="email" name="email" class="form-control" id="email" placeholder="Entrer une <?= strtolower(getTranslation("email")); ?>" required>
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="password"><?= getTranslation("password"); ?>: <span class="text-danger">*</span></label>
         <div class="col-md-10">
            <div class="input-group">
              <input type="password" name="password" class="form-control" id="password" placeholder="Entrer un <?= strtolower(getTranslation("password")); ?>" required>
              <span class="input-group-btn" title="Generate password"><button type="button" class="btn btn-default generatePassword" style="border: 1px solid #ccc; border-radius: 0;"><?= getTranslation("generatePassword"); ?></button></span>
              <span class="input-group-btn" title="Show password"><button type="button" class="btn btn-default showPassword" style="border: 1px solid #ccc; border-top-left-radius: 0; border-bottom-left-radius: 0;"><i class="fa fa-eye"></i></button></span>
            </div>
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="type"><?= getTranslation("userRole"); ?>: <span class="text-danger">*</span></label>
         <div class="col-md-10">
           <select name="type" class="form-control" id="type" placeholder="Select <?= getTranslation("userRole"); ?>" required>
             <option value="">Sélectionnez un <?= strtolower(getTranslation("userRole")); ?></option>
             <?php
                foreach($userTypes as $userType) {
                  $userTypeId = $userType['id'];
                  $userTypeName = $userType['pretty_name'];
                  echo "<option value=".$userTypeId.">$userTypeName</option>";
                }
             ?>
           </select>
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="firstname">Prénom:</label>
         <div class="col-md-10">
           <input type="text" name="firstname" class="form-control" id="firstname" placeholder="Entrer un prénom">
         </div>
       </div>

       <div class="form-group row mt-2">
         <label class="col-form-label col-md-2" for="lastname">Nom:</label>
         <div class="col-md-10">
           <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Entrer un nom">
         </div>
       </div>

     </div>
     <div class="card-footer">
       <button class="btn btn-primary" type="button" name="addUser"><?= getTranslation("addUser"); ?> <i class="fa fa-plus-circle"></i></button>
     </div>
   </div>
 </form>
</div>
<?php include_once("inc/footer.php"); ?>
