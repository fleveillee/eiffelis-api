<html>
<head>
    <title>iRestMyCase: Model Generator</title>
</head>
<body>
<label for="selectDao">Select a DAO</label>
<form method="POST">
	<? if (isset($daos)): ?>
        <select name="dao" id="selectDao">
			<? foreach ($daos as $key => $config): ?>
                <option<?= (isset($selectedDao) && $selectedDao == $key ? ' selected="selected"' : ''); ?>><?= $key; ?></option>
			<? endforeach; ?>
        </select>
	<? endif; ?>

	<? if (isset($models)): ?>
        <h2>Select Models</h2>
        <ul style="list-style-type: none;">
			<? foreach ($models as $tableName => $modelName): ?>
                <li><label><input type="checkbox"
                                  name="models[<?= $tableName; ?>]"<?= isset($selectedModels) && in_array($modelName,
							$selectedModels) ? ' checked="checked"' : ''; ?>
                                  value="<?= $modelName; ?>"/><?= $modelName; ?></label></li>
			<? endforeach; ?>
        </ul>
	<? endif; ?>
    <input type="submit" value="Submit"/>
</form>
</body>
</html>
