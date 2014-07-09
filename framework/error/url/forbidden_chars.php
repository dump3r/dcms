<div class="wrapper">
    <h1>Verbotene Zeichen im URL String</h1>
    <div class="inner">
        
        <p>
            Es wurden verbotene Zeichen in der URL gefunden!
            Bitte versuche nicht die URL-Zeile zu manipulieren.
            <a href="<?php echo $base_url; ?>">Zur&uuml;ck zur Startseite</a>
        </p>
        
        <?php if(DCMS_ENVIRONMENT == 'development'): ?>
            <div class="log-debug">
                <?php \dcms\Log::display(); ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>