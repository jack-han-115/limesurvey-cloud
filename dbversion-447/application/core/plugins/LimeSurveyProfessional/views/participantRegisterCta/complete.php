<?php if (App()->getConfig('branding') === '1') : ?>
    <div class="col-lg-5 col-md-6 col-sm-8 col-xs-12 col-centered">
        <div class="well text-center">
            <h3>
                <?php eT('Thank you for taking this survey powered by LimeSurvey.'); ?>
            </h3>
            <p>
                <?php eT('Turn your own questions into answers and start building your own survey today.'); ?>
            </p>
            <div>
                <a href="https://www.limesurvey.org/" target="_blank" class="btn btn-primary">
                    <?php eT('Get started now'); ?>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
