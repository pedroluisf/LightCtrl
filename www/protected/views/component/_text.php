<?php
$attributeTranslated = Dictionary::translateAttributeDescription($attribute);

echo '<h2>'.$attributeTranslated.($subAttribute !== null ? ' - '.($subAttribute+1) : '').'</h2>';
?>

<input type="text" id="dialog_value" value="<?php echo $value ;?>">
