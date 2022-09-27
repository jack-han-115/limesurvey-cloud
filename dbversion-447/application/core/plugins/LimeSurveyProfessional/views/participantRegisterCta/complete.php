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
    'utm_term' => isset($utm_term) ? $utm_term : 'complete'
);
$trackingQueryString = http_build_query($trackingParams);
$signupUrl = 'https://www.limesurvey.org/?' . $trackingQueryString;

?>
<div class="col-lg-5 col-md-6 col-sm-8 col-xs-12 col-centered">
    <div class="well text-center">
        <h3>
            <?php eT('Thank you for taking this survey powered by LimeSurvey.'); ?>
        </h3>
        <p>
            <?php eT('Turn your own questions into answers and start building your own survey today.'); ?>
        </p>
        <div>
            <a href="<?= $signupUrl ?>" target="_blank" class="btn btn-primary">
                <?php eT('Get started now'); ?>
            </a>
        </div>
    </div>
</div>
