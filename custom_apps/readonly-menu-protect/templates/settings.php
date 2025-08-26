<?php
declare(strict_types=1);
?>
<div class="section" id="readonly-menu-protect">
    <h2><?php p($l->t('Readonly Menu Protect')); ?></h2>
    <p>
        <?php p($l->t('This app hides all navigation items except "Files" for users in the "readonly_users" group.')); ?>
    </p>
    <p>
        <?php p($l->t('Ensure the group "readonly_users" exists and users are assigned to it.')); ?>
    </p>
    <p>
        <strong><?php p($l->t('Note:')); ?></strong>
        <?php p($l->t('Users will be automatically redirected to the Files app if they try to access other applications.')); ?>
    </p>
</div>
