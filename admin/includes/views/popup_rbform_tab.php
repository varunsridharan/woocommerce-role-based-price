
<ul> 
    <?php
    foreach(WC_RBP()->get_allowed_roles() as $user_role_key => $val){
        $name = WC_RBP()->get_mod_name($user_role_key);
    ?>
        <li> <a href="#<?php echo $user_role_key; ?>"><?php echo $name; ?></a></li>
    <?php
    }
    ?>
</ul> 