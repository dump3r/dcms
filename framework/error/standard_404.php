<div class="wrapper">
    <h1>Server Error 404</h1>
    <div class="inner">
        
        <p>
            Es wurde ein 404 Fehler durch dCMS ausgel&ouml;st.
            Möglicherweise existiert der Contoller oder das View nicht.
            <br />
            <em>Zudem hast du keine eigene Fehlerroute erstellt!</em>
            <a href="<?php echo $base_url; ?>">Zur&uuml;ck zur Startseite</a>
        </p>
        
        <?php if(DCMS_ENVIRONMENT == 'development'): ?>
            <div class="log-debug">
                <?php \dcms\Log::display(); ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>