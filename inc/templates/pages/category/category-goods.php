<?php
/*
 * Template name: 好物展柜
   Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <style>
        .win-top:after {
            background: transparent!important;
        }
        .content-all, .win-top {
            height: 100%;
        }
        html, body,
        iframe {
            width: 100%;
            height: 100%;
        }
        
        iframe#exhibition {
            width: 98%;
            height: 96%;
            max-width: 1102px;
            max-height: 66%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: var(--radius);
        }
        .controls {
            position: fixed;
            /*top: 50%;*/
            bottom: 0;
            left: 50%;
            transform: translate(-50%, 0);
            border-radius: var(--radius);
        }
        body.dark .controls ul {
            color: var(--preset-c);
            background-image: radial-gradient(var(--preset-2b) 2px, var(--preset-2bs) 2px);
            box-shadow: var(--preset-5a) 1px 2.2px 1px -1.8px inset, var(--preset-2bs) -1px -2.2px 1px -1.8px inset;
        }
        .controls ul {
            padding: 15px;
            margin: 15% auto;
            white-space: nowrap;
            box-sizing: border-box;
            border-radius: inherit;
            color: var(--preset-6);
            backdrop-filter: saturate(150%) blur(5px);
            -webkit-backdrop-filter: saturate(200%) blur(5px);
            background-image: radial-gradient(rgb(255 255 255 / 55%) 2px, rgb(255 255 255) 2px);
            background-size: 4px 4px;
            box-shadow: rgb(0 0 0 / 5%) 0px 20px 20px;
            overflow: auto;
        }
        .controls ul li:last-child {
            margin-right: auto;
        }
        body.dark .controls ul li.active {
            background: var(--preset-2a);
        }
        .controls ul li.active {
            padding: 5px;
            background: var(--preset-f);
            /*border: 1px solid var(--preset-3a);*/
            /*box-shadow: var(--preset-5a) 1px 2.2px 1px -1.8px inset, var(--preset-2bs) -1px -2.2px 1px -1.8px inset;*/
        }
        .controls ul li.active img,
        .controls ul li:hover img {
            filter: opacity(1);
        }
        /*.controls ul li:hover b,*/
        .controls ul li.active b {
            color: var(--theme-color);
        }
        .controls ul li {
            display: inline-block;
            border-radius: inherit;
            margin-right: 15px;
            /*cursor: pointer;*/
        }
        .controls ul li img,
        .controls ul li b {
            /*pointer-events: none;*/
        }
        .controls ul li img {
            width: 100%;
            height: 100%;
            min-width: 66px;
            min-height: 66px;
            max-width: 80px;
            border-radius: inherit;
            cursor: pointer;
            filter: opacity(0.5);
            -webkit-user-drag: none;
        }
        .controls ul li b {
            font-size: 12px;
            display: block;
            margin: 5px auto;
        }
        
        @media screen and (max-width: 960px) {
            iframe#exhibition {
                width: 100%;
                height: 100%;
                max-height: 100%;
                border-radius: 0;
            }
            .controls ul {
                max-width: 95%;
            }
        }
    </style>
</head>
<body class="<?php theme_mode(true); ?>">
    <div class="content-all">
        <div class="win-top blur">
            <iframe id="exhibition" frameborder="no" data-src="https://node.2broear.com/"></iframe>
            <div class="controls">
                <ul class="lively-click-098 magnetic" data-magnet-step="0.1" data-magnet-scale="1.05">
                    <li class="lively-click-108">
                        <img class="magnetics" data-magnet-scale="" src="https://imgs.2broear.com/2026/04/macbook_pro.jpg" alt="" data-search="?texture&entry=macbook_pro&model=/assets/3d/draco/apple_macbook_pro_16_inch_2021-x100-edit-central_compresseds.glb" />
                        <b>MacBook Pro</b>
                    </li>
                    <li class="lively-click-108">
                        <img class="magnetics" data-magnet-scale="" src="https://imgs.2broear.com/2026/04/teslat_model3s.jpg" alt="tesla_2018_model_3_compresseds" data-search="?texture&entry=tesla_model_3&model=/assets/3d/draco/tesla_2018_model_3-edit_compressed.glb" />
                        <b>Tesla Model 3</b>
                    </li>
                    <li class="lively-click-108">
                        <img class="magnetics" data-magnet-scale="" src="https://imgs.2broear.com/2026/04/tesla_cybertrucks.jpg" alt="" data-search="?texture&entry=tesla_cybertruck&model=/assets/3d/draco/tesla_cybertruck-x200_compresseds.glb" />
                        <b>CyberTruck</b>
                    </li>
                    <li class="lively-click-108">
                        <img class="magnetics" data-magnet-scale="" src="https://imgs.2broear.com/2026/04/mbti_enfp.jpg" alt="" data-search="?texture&entry=mbti_enfp&model=/assets/3d/draco/mbti_enfp-textured-x200_compresseds.glb" />
                        <b>MBTI ENFP</b>
                    </li>
                </ul>
            </div>
            <header>
                <nav id="tipson" class="ajaxloadon">
                    <?php get_header(); ?>
                </nav>
            </header>
        </div>
        <footer>
            <?php //get_footer(); ?>
        </footer>
    </div>
    <?php get_foot(); ?>
    <script type="text/javascript">
        const activate = 'active';
        const controls = document.querySelector('.controls');
        const exhibition = document.getElementById('exhibition');
        const list = controls.querySelectorAll('ul li');
        list[0].classList.add(activate);
        exhibition.src = exhibition.dataset.src + list[0].querySelector('img').dataset.search; //controls.querySelector('li img')
        // const iframeData = exhibition.contentWindow;
        const pathname = exhibition.src.substr(0, exhibition.src.indexOf('?'));
        // setup events
        bindEventClick(controls, '', (t)=> {
            if (t.tagName !== 'IMG') return;
            // add stats
            list.forEach((item)=>item.classList.remove(activate));
            t.parentNode.classList.add(activate);
            // switch exhibition
            const switchExhibition = pathname + t.dataset.search;
            if (exhibition.src !== switchExhibition) exhibition.src = switchExhibition; //iframeData.location.pathname
        });
    </script>
</body></html>