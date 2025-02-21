<?php
function currency_check($option){
    if(in_array($option, ['🇺🇸 USD > 🇺🇿 UZS', '🇪🇺 EUR > 🇺🇿 UZS', '🇷🇺 RUB > 🇺🇿 UZS'])){
        $option= explode(' ', $option)[1];
        return $option;
    }
}
?>