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
        
        .exhibition {
            width: 88%;
            height: 88%;
            /*max-width: 1102px;*/
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /*border-radius: calc(var(--radius)*2);*/
        }
        iframe#exhibition {
            max-height: 80%;
            position: inherit;
            top: 55%;
            left: inherit;
            transform: inherit;
            border-radius: calc(var(--radius) * 2);
        }
        .controls {
            max-width: 80%;
            position: fixed;
            /*top: 50%;*/
            bottom: 0;
            left: 50%;
            transform: translate(-50%, 0);
        }
        body.dark .controls ul {
            color: var(--preset-c);
            border-color: var(--preset-3a);
            background-image: radial-gradient(var(--preset-2b) 2px, rgb(10 20 28 / 66%) 2px);
            /*background-image: radial-gradient(var(--preset-2b) 2px, var(--preset-2bs) 2px);*/
            box-shadow: var(--preset-5a) 1px 2.2px 1px -1.8px inset, var(--preset-2bs) -1px -2.2px 1px -1.8px inset;
        }
        .controls ul {
            padding: 15px;
            margin: 0 auto;
            white-space: nowrap;
            box-sizing: border-box;
            border-radius: var(--radius);
            color: var(--preset-6);
            border: 2px solid var(--preset-f);
            backdrop-filter: saturate(150%) blur(5px);
            -webkit-backdrop-filter: saturate(200%) blur(5px);
            background-image: radial-gradient(rgb(255 255 255 / 66%) 2px, rgb(255 255 255) 2px);
            background-size: 4px 4px;
            box-shadow: rgb(0 0 0 / 5%) 0px 20px 20px;
            overflow: auto;
        }
        .controls ul li:last-child {
            margin-right: auto;
        }
        body.dark .controls ul li.active img {
            background: var(--preset-2a);
        }
        .controls ul li.active img {
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
            min-width: 66px;
            min-height: 66px;
            max-width: 80px;
            display: inline-block;
            border-radius: inherit;
            margin-right: 15px;
            vertical-align: top;
            /*cursor: pointer;*/
        }
        .controls ul li img,
        .controls ul li b {
            /*pointer-events: none;*/
            white-space: normal;
        }
        .controls ul li img {
            padding: 5px;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            min-width: 80px;
            max-width: 100px;
            border-radius: inherit;
            cursor: pointer;
            filter: opacity(0.5);
            -webkit-user-drag: none;
        }
        .controls ul li b {
            font-size: 12px;
            display: block;
            /*margin: 5px auto;*/
            /*margin-bottom: 5px;*/
        }
        
        @media screen and (max-width: 960px) {
            .exhibition,
            iframe#exhibition {
                width: 100%;
                height: 100%;
                max-height: 100%;
                border-radius: 0;
            }
            .controls ul {
                max-width: 100%;
                margin: 10% auto;
            }
        }
    </style>
</head>
<body class="<?php theme_mode(true); ?>">
    <div class="content-all">
        <div class="win-top blur">
            <div class="exhibition">
                <iframe id="exhibition" frameborder="no" data-src="https://node.2broear.com/indexs.html"></iframe>
                <div class="controls">
                    <ul class="containers lively-click-098 magnetic" data-magnet-step="0.1" data-magnet-scale="1.05">
                    </ul>
                </div>
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
        const exhibition = document.getElementById('exhibition');
        const containers = document.querySelector('.containers');
        
        // default goodsData
        let goodsData = [{
            title: "Tesla Model 3",
            img: "https://imgs.2broear.com/2026/04/teslat_model3s.jpg",
            url: "//node.2broear.com/?texture&entry=tesla_model_3&model=/assets/3d/draco/tesla_2018_model_3-edit_compressed.glb",
        }];
        // rewrite goodsData
        <?php echo get_option('site_goods_panorama_data'); ?>
        
        // load goodsData
        const fragment = document.createDocumentFragment();
        goodsData.forEach((item, index)=> {
            let li = document.createElement('LI');
            li.className = 'lively-click-108';
            if (index === 0) {
                li.classList.add(activate); // current model statu
                exhibition.src = item.url;  // default display
            }
            li.innerHTML = `<img class="magnetics" data-magnet-scale="" src="${item.img}" alt="${item.title}" data-url="${item.url}" /><b>${item.title}</b>`;
            fragment.appendChild(li);
        });
        containers.appendChild(fragment);
        
        // setup events
        const list = containers.querySelectorAll('li');
        bindEventClick(containers, '', (t)=> {
            if (t.tagName !== 'IMG') return;
            const switchExhibition = t.dataset.url;
            // console.log(exhibition.src , switchExhibition)
            if (exhibition.src === switchExhibition) return;
            // switch exhibition
            exhibition.src = switchExhibition;
            // add stats
            list.forEach((item)=>item.classList.remove(activate));
            t.parentNode.classList.add(activate);
        });
    </script>
</body></html>