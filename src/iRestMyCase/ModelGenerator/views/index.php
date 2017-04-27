<html>
<head>
    <title>iRestMyCase: Model Generator</title>
</head>
<body>
<label for="selectDao">Select a DAO</label>
<form method="POST">
	<? if (!empty($daos)): ?>
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

		<? if (isset($languages)): ?>
            <label for="selectLanguage">Language: </label>
            <select name="language" id="selectLanguage">
				<? foreach ($languages as $key => $language): ?>
                    <option value="<?= $key; ?>"<?= (isset($selectedLanguage) && $selectedLanguage == $key ? ' selected="selected"' : ''); ?>><?= $language; ?></option>
				<? endforeach; ?>
            </select>
		<? endif; ?>

	<? endif; ?>


    <input type="submit" value="Submit"/>
</form>
</body>
</html>
