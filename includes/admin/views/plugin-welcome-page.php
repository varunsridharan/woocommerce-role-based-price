<div class="wrap about-wrap">
    <h1><?php _e("Welcome To", WC_RBP_TXT);
        echo ' ' . __("WC Role Based Price", WC_RBP_TXT) . ' ' . WC_RBP_V; ?></h1>
    <p class="about-text">
        <?php echo WC_RBP_NAME;
        _e(" now with improved speed and stability"); ?>
    </p>
    <div class="wp-badge"><?php echo WC_RBP_V; ?></div>
</div>


<style>
    .wp-badge {
        background-image    : url("https://plugins.svn.wordpress.org/woocommerce-role-based-price/assets/icon-256x256.jpg") !important;
        background-position : center top !important;
        background-size     : 100% auto !important;
        padding-top         : 140px !important;
        padding-bottom      : 3px !important;
        height              : auto !important;
    }
</style>