<?php
$slug     = $wc_rbp_plugin_data['addon_slug'];
$cat_slug = implode(' wc-rbp-addon-', array_keys($wc_rbp_plugin_data['Category']));

$wrapperClass = 'plugin-card plugin-card-' . $slug . ' wc-rbp-addon-all  wc-rbp-addon-' . $cat_slug;

if( $wc_rbp_plugin_data['is_active'] ) {
    $wrapperClass .= ' wc-rbp-addon-active';
} else {
    $wrapperClass .= ' wc-rbp-addon-inactive';
}

?>
<div class="<?php echo $wrapperClass; ?>" id="<?php echo $slug; ?>">
    <?php wc_rbp_get_ajax_overlay(); ?>
    <div class="plugin-card-top">
        <div class="name column-name">
            <h3>
                <?php echo $wc_rbp_plugin_data['Name']; ?>
                [
                <small><?php _e('V', WC_RBP_TXT); ?><?php echo $wc_rbp_plugin_data['Version']; ?></small>
                ]
                <?php $this->get_addon_icon($wc_rbp_plugin_data); ?>
            </h3>
        </div>
        <div class="desc column-description">
            <p><?php echo $wc_rbp_plugin_data['Description']; ?></p>
            <p class="authors">

                <cite>
                    <?php _e('By', WC_RBP_TXT); ?>
                    <a href="<?php echo $wc_rbp_plugin_data['AuthorURI']; ?>"> <?php echo $wc_rbp_plugin_data['Author']; ?></a>
                </cite>
            </p>
        </div>
    </div>
    <div class="plugin-card-top wc-rbp-addons-required-plugins">
        <?php if( ! empty($required_plugins) ): ?>
            <div>
                <h3><?php _e('Required Plugins :', WC_RBP_TXT); ?></h3>
                <ul>
                    <?php
                    $echo = '';
                    foreach( $required_plugins as $plugin ) {
                        $plugin_status = $this->check_plugin_status($plugin['Slug']);
                        $status_val    = __('InActive', WC_RBP_TXT);
                        $class         = 'deactivated';
                        if( $plugin_status === 'notexist' ) {
                            $status_val = __('Plugin Does Not Exist', WC_RBP_TXT);
                            $class      = 'notexist';
                        } else if( $plugin_status === TRUE ) {
                            $status_val = __('Active', WC_RBP_TXT);
                            $class      = 'active';
                        }
                        if( ! isset($plugin['Version']) ) {
                            $plugin['version'] = '';
                        }
                        echo '<li class="' . $class . '">';

                        echo '<span class="wc_rbp_required_addon_plugin_name"> <a href="' . $plugin['URL'] . '" > ' . $plugin['Name'] . ' [' . $plugin['Version'] . '] </a> </span> : ';
                        echo '<span class="wc_rbp_required_addon_plugin_status ' . $class . '">' . $status_val . '</span>';
                        echo '</li>';
                        unset($plugin_status);
                    }
                    ?>
                </ul>
                <?php /*<p> <span><?php _e('Above Mentioned Plugin name with version are Tested Up to',WC_RBP_TXT);?></span> </p> */ ?>

            </div>
        <?php endif; ?>

        <?php if( ! empty($wc_rbp_plugin_data['screenshots']) ) : ?>
            <div class="addon-screenshots">
                <h3><?php _e("Screenshots", WC_RBP_TXT); ?></h3>
                <ul>
                    <?php
                    $i   = 1;
                    $url = $wc_rbp_plugin_data['addon_url'];
                    foreach( $wc_rbp_plugin_data['screenshots'] as $screen ) {
                        echo '<li><a class="thickbox" href="' . $url . basename($screen) . '?TB_iframe=true">' . $i . '</a></li>';
                        $i++;
                    }
                    ?>
                </ul>
            </div>

        <?php endif; ?>

        <small><strong><?php _e('Addon Slug : ', WC_RBP_TXT); ?></strong><?php echo $wc_rbp_plugin_slug; ?></small>
    </div>
    <div class="plugin-card-bottom">
        <div class="column-updated" data-pluginslug="<?php echo $slug; ?>">
            <?php echo $this->get_addon_action_button($wc_rbp_plugin_slug, $required_plugins); ?>
        </div>
        <div class="column-downloaded"><strong><?php _e('Last Updated:', WC_RBP_TXT); ?></strong>
            <span title="<?php echo $wc_rbp_plugin_data['last_update']; ?>"><?php echo $wc_rbp_plugin_data['last_update']; ?></span>
        </div>
        <div class="column-downloaded wc_rbp_ajax_response"></div>
    </div>
</div>