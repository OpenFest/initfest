<?php

register_nav_menus(
    array( 'main-menu' => __( 'Main Menu', 'initfest' ),
           'footer-openfest' => __('OpenFest', 'initfest'),
           'footer-openfest' => __('OpenFest', 'initfest'),
           'footer-schedule' => __('Schedule', 'initfest'),
           'footer-others' => __('Others', 'initfest'),
           'footer-followus' => __('Follow us in:', 'initfest') )
);

add_shortcode('sh-latest-posts', 'sh_latest_posts');

function sh_latest_posts($atts){
    ob_start();
    ?>

<div class="separator"></div>

        <section class="content">
            <h3>Новини | <small><a href="#">виж всички новини</a></small></h3>
            <div class="grid">
                <div class="col3">
                    <h4>Разходи за OpenFest 2013</h4>
                    <p class="info">От HACKMAN | Публикувано на: 13.11.2013</p>
                    <p>4913.05 – Зали, озвучаване и техника за презентиране<br />
                    2265.07 – Тениски за посетители, екип и доброволци<br />
                    673.18 – Разходи за вода, чай, кафе, вафли, разколнители, канцеларски материали<br />
                    2442.00 – Транспорт, хотел и вечеря за лекторите<br />
                    397.00 – Закуска и обяд за екипа в двата дни ...</p>
                    <a href="#" class="button">виж цялата новина</a>
                </div>
                <div class="col3">
                    <h4>2013 Network Stats</h4>
                    <p class="info">От HACKMAN | Публикувано на: 13.11.2013</p>
                    <p>Удостоверили и сме им раздали 1841 уникални потребителски IP версия 4
адреси и 1356 IPv6 адреси!<br />Максимума едновременно работещи потребители върху всичките 4 AP-та е 326
устройства и е направен на 2.11.2013 в 15:33
– Зала Варна 2.4GHz 11ng = 44
– Лоби 2.4GHz 11ng = 85 ...</p>
                    <a href="#" class="button">виж цялата новина</a>
                </div>
                <div class="col3">
                    <h4>Статистика за Бирата</h4>
                    <p class="info">От HACKMAN | Публикувано на: 13.11.2013</p>
                    <p>418х Старопрамен наливно 0.500мл<br />
97х Каменица 0.500мл<br />
62х Бекс<br />
48х Старопрамен бутилка 0.500мл<br />
34х Стела Артоа<br />
29х Каменица тъмно<br />
29х Ла Трап<br />
23х Корона<br />
23х Гинес<br />
23х ХопГоблин<br />
13х Трупър ...</p>
                    <a href="#" class="button">виж цялата новина</a>
                </div>
            </div>
        </section>

        <div class="separator"></div>
<?php
    $result = ob_get_contents();
    ob_end_clean();
    
    return $result;
    
}