<div class="wrapper">
    <h1>Leere URL Zeichenkette &uuml;bergeben</h1>
    <div class="inner">
        
        <p>
            Es wurde eine leere URL Zeichenkette &uuml;bergeben.
            Bitte gehe zur&uuml;ck und versuche es erneut.
            <a href="<?php echo $base_url; ?>">Zur&uuml;ck zur Startseite</a>
        </p>
        
        <?php if(DCMS_ENVIRONMENT == 'development'): ?>
            <div class="log-debug">
                <?php \dcms\Log::display(); ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>