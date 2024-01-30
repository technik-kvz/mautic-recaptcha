<?php

namespace MauticPlugin\MauticRecaptchaBundle\Helper;

class RecaptchaHelper {

public static function getPhp($event, $eventListener, $settings): string {

$formName = 'plugin_recaptcha';
$hashedFormName = md5($formName);
$fieldName = 'recaptcha';
$html = '';

$jsElement = '';
if($settings[1] == 'v2') {
    $jsElement = 'https://www.google.com/recaptcha/api.js';
} else {
    $jsElement = 'https://www.google.com/recaptcha/api.js?onload=onLoad{$hashedFormName}&render={$settings[0]}';
}

$html .= <<<HTML
<script src="${jsElement}"></script>
<script type="text/javascript">
var formName = "{$formName}";
var fieldName = "{$fieldName}";
document.addEventListener('DOMContentLoaded', function() {
(function() {
    var forms = document.getElementsByTagName("form");
    for (var form of forms) {
        if (form.hasAttribute("data-mautic-form")) {
            formName = form.getAttribute("data-mautic-form");
        }
    }
    document.getElementById("mauticform_{$formName}_{$fieldName}").id = "mauticform_" + formName + "_" + fieldName;
    document.getElementById("mauticform_input_{$formName}_{$fieldName}").id = "mauticform_input_" + formName + "_" + fieldName;
})();
}, false);
function verifyCallback_{$hashedFormName}( response ) {
    var el = document.getElementById("mauticform_input_" + formName + "_" + fieldName);
    if (response.length == 0) {
        el.value = "";
    } else {
        el.value = response;
    }
    el.setAttribute("name", "mauticform[recaptcha]");
}
function onLoad{$hashedFormName}() { 
    grecaptcha.execute('{$settings[0]}', {action: 'form'}).then(function(token) {
        verifyCallback_{$hashedFormName}(token);
     }); 
}
</script>
HTML;

$html .= <<<HTML
<div id="mauticform_{$formName}_{$fieldName}" class="mauticform-row mauticform-div-wrapper">
HTML;

if($settings[1] == 'v2') {
$html .= <<<HTML
<div class="g-recaptcha" data-sitekey="{$settings[0]}" data-callback="verifyCallback_{$hashedFormName}"></div>
HTML;
}

$html .= <<<HTML
<input id="mauticform_input_{$formName}_{$fieldName}" value="" class="mauticform-input" type="hidden">
HTML;

$html .= <<<HTML
<span class="mauticform-errormsg" style="display: none;">reCaptcha &cross;</span>
HTML;

$html .= "</div>";

return $html;

}

};
