<html>
<head>

</head>
<body>
     <label>Select a DAO</label>
     <form method="POST">
          <select name="dao">
               <? foreach($daos as $key => $config): ?>
               <option<?=(isset($selectedDao) && $selectedDao==$key?' selected="selected"':'');?>><?=$key;?></option>
               <? endforeach; ?>
          </select>

          <? if(isset($models)): ?>
               <h2>Select Models</h2>
               <ul style="list-style-type: none;">
               <? foreach($models as $tableName => $modelName): ?>
                    <li><label><input type="checkbox" name="models[<?=$tableName;?>]"<?=isset($selectedModels) && in_array($modelName, $selectedModels)? ' checked="checked"': ''; ?> value="<?=$modelName;?>"/><?=$modelName;?></label></li>
               <? endforeach; ?>
               </ul>
          <? endif; ?>
          <input type="submit" value="Submit"/>
     </form>
</body>
</html>