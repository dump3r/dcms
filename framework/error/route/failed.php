<div class="wrapper">
    <h1>Das Routing konnte nicht beendet werden</h1>
    <div class="inner">
        
        <p>
            Die Routerklasse hat das Routing gestoppt.
            Entweder ist keine Controller- bzw. Viewklasse definiert
            oder die Namen der Methoden stimmen nicht &uuml;berein.
        </p>
        
        <?php if(DCMS_ENVIRONMENT == 'development'): ?>
            <div class="log-debug">
                <?php \dcms\Log::display(); ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>