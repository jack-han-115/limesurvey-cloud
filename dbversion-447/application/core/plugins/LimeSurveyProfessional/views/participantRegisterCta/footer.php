<?php

/** @var String $utm_source */
/** @var String $utm_medium */
/** @var String $surveyId */
/** @var String $utm_term */

// https://ga-dev-tools.web.app/campaign-url-builder/
// https://www.cronyxdigital.com/blog/track-campaigns-in-google-analytics-utm-parameters
$trackingParams = array(
    'utm_source' => isset($utm_source) ? $utm_source : '',
    'utm_medium' => isset($utm_medium) ? $utm_medium : 'survey',
    'utm_campaign' => isset($utm_campaign) ? $utm_campaign : '',
    'utm_term' => isset($utm_term) ? $utm_term : 'footer'
);
$trackingQueryString = http_build_query($trackingParams);
$signupUrl = 'https://www.limesurvey.org/?' . $trackingQueryString;

?>
<div class="col-md-12">
    <div class="text-right">
        <a href="<?= $signupUrl ?>" target="_blank" class="btn btn-default">
            <?php eT('Made in LimeSurvey'); ?>
            <span class="fa fa-external-link" aria-hidden="true"></span>
        </a>
    </div>
</div>
