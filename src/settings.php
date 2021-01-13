<?php
require_once('requests.php');
if (isset($_POST['submit'])) {
    if (!isset($_POST['ar_cdn_api_key']))
        return;
    ArRequests::setApiKey($_POST['ar_cdn_api_key']);
}
?>
<h1><?php _e('Settings', AR_TEXT_DOMAIN); ?></h1>
<form method="post">
<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row"><label for="ar_cdn_api_key"><?php _e('Api key', AR_TEXT_DOMAIN); ?></label></th>
            <td><input name="ar_cdn_api_key" type="text" id="ar_cdn_api_key" value="<?php echo ArRequests::getAPIKey(); ?>" class="regular-text"></td>
        </tr>
    </tbody>
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Submit', AR_TEXT_DOMAIN); ?>"></p>
</form>