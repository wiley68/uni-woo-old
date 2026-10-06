<?php
/** add admin menu */
function uni_admin_actions() {
    add_options_page('УНИ Кредит - Настройки на модула', 'УНИ Кредит настойки', 'manage_options', "uni-options", "uni_admin_options");
}