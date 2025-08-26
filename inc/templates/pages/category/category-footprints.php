<?php
/*
    Template name: 足迹地图
    Template Post Type: page
*/
    $footprint_sw = get_option('site_footprint_switcher');
    $default_theme = isset($_GET['theme']) ? $_GET['theme'] : theme_mode(true);
    $default_zoom = isset($_GET['zoom']) ? $_GET['zoom'] : '';
    $default_center = isset($_GET['center']) ? $_GET['center'] : '';
    $default_datasrc = isset($_GET['coords']) ? $_GET['coords'] : '';
    $default_key = isset($_GET['key']) ? $_GET['key'] : get_option('site_footprint_apikey');
    $default_map = isset($_GET['map']) ? $_GET['map'] : get_option('site_footprint_map');
    $default_amap = $default_map === 'amap';
    $default_data = isset($_GET['coords']) ? $_GET['coords'] : ''; //coords.json
    $default_path = dirname('//' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]);
    $default_coords_data = get_option('site_footprint_data');
    $default_panorama_data = get_option('site_footprint_panorama_data');
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
<?php
    if ($default_amap) {
?>
        html, body, #container,
        .content-all, .win-top {
            height: 100%;
            width: 100%;
        }
        @keyframes profileScale {
        	0%{
        		transform: scale(.8);
        		opacity: .8;
        	}
        	100%{
        		transform: scale(1.3);
        		opacity: 0;
        	}
        }
        .amap-cluster {
            max-width: 80px;
            max-height: 80px;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            font-size: 12px;
            opacity: .85;
        }
        .amap-cluster::before {
            content: "";
        	width: 100%;
        	height: 100%;
        	border-radius: inherit;
        	background: currentColor;
        	opacity: .8;
    		transform: scale(0.8);
        	position: absolute;
        	top: 0;
        	left: 0;
        	z-index: -1;
        	animation: profileScale 1.8s ease infinite normal;
        }
        .showName {
            font-size: 14px;
            height: 100%;
            /*background: black;*/
            /*color: white;*/
            /*height: auto;*/
            line-height: 16px;
        }
        .showCount {
            font-size: xx-large;
            line-height: 34px;
        }
        .showCount,.showName {
            color: white;
            display: block;
            text-overflow: ellipsis;
            /*white-space: nowrap;*/
            overflow: hidden;
            /*padding: 3px;*/
            /* width: 80%; */
            /*padding: 0 5px 5px;*/
        }
        .showCount img,.showName img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }
        .win-top:after,
        .amap-info-close {
            display: none;
        }
        .amap-info-content {
            /*max-width: 200px;*/
            padding: 15px 25px; 
            border-radius: 50px; 
        }
        .bottom-center .amap-info-sharp {
            bottom: 1px;
        }
<?php
    } else {
?>
        html,
        body {
            overflow: hidden;
            height: 100%;
        }
        #mapContainer {
            position: relative;
            height: 100%;
            width: 100%;
        }
        .clusterBubble {
            padding: 5px;
            /*min-width: 55px;*/
            /*min-height: 55px;*/
            border-radius: 50%;
            color: #fff;
            font-size: x-large;
            font-weight: 800;
            text-align: center;
            opacity: 0.88;
            background-image: linear-gradient(139deg, #4294FF 0%, #295BFF 100%);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.20);
            position: absolute;
            top: 0px;
            left: 0px;
            cursor: pointer;
            border: 10px solid rgb(255 255 255 / 55%);
            will-change: transform;
        }
        .infoWindow > div:first-child {
            padding: 10px 15px!important;
            border-radius: 50px !important;
            min-width: auto!important;
        }
        .infoWindow > div:first-child iframe {
            border-radius: inherit;
        }
        .infoWindow > div:last-child {
            display: none;
            width: 20px !important;
            height: 20px !important;
            margin: 5px;
        }
        #mapContainer > div:last-child,
        #mapContainer a,
        #mapContainer img,
        #mapContainer .logo-text,
        #mapContainer .tmap-scale-control,
        #mapContainer .clusterBubble,
        #mapContainer .rotate-circle,
        #mapContainer .tmap-zoom-control {
            user-select: none;
            -webkit-user-select: none;
            -webkit-user-drag: none;
            /*pointer-events: none;*/
        }
<?php
    }
?>
        #panorama .amap-info-content,
        #panorama > div:first-child {
            text-align: center;
            padding: 10px !important;
            border-radius: 15px !important;
            background-color: transparent!important;
            background: linear-gradient(180deg, rgb(200 215 255 / 58%) 0%, rgb(255 255 255) 100%);
            background: -webkit-linear-gradient(270deg, rgb(200 215 255 / 58%) 0%, rgb(255 255 255) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgb(255 255 255);
            user-select: none;
            -webkit-user-select: none;
        }
        #panorama img,
        #panorama iframe {
            display: block;
            max-width: 600px!important;
            max-height: 300px!important;
            border-radius: inherit;
            -webkit-user-drag: none;
        }
        #panorama img {
            max-height: 200px!important;
        }
        #panorama h2, #panorama h3,
        #panorama p {
            color: var(--preset-3a);
            padding: 0 5px;
            max-width: 235px;
            text-align: left;
            white-space: pre-wrap;
            /* overflow: hidden; */
            /* text-overflow: ellipsis; */
        }
        #panorama h2, #panorama h3 {
            margin: 10px auto 2px;
        }
        #panorama p {
            margin: auto auto 18px;
            opacity: .75;
            font-size: 12px;
        }
    </style>
</head>
<body class="<?php echo $default_theme; ?>">
<div class="content-all">
    <div class="win-top bg">
        <?php 
            if (!$footprint_sw) {
                echo '<div class="empty_card"><i class="icomoon icom icon-' . current_slug() . '" data-t=" EMPTY "></i><h1> ' . $default_key ? current_slug(1) : 'API KEY' . ' </h1></div></div>';
            } else {
                switch ($default_map) {
                    case 'tmap':
                        $map_container = '<div id="mapContainer"></div>';
                        $map_src = 'https://map.qq.com/api/gljs?v=1.exp&key=' . $default_key;
                        break;
                    case 'amap':
                    default:
                        $map_container = '<div id="container" class="map"></div>';
                        $map_src = 'https://webapi.amap.com/maps?v=2.0&key=' . $default_key . '&plugin=AMap.IndexCluster';
                        break;
                }
                echo $map_container;
            }
        ?>
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
<!-- siteJs -->
<script src="<?php echo $map_src; ?>"></script>
<script type="text/javascript">
    function searchQueryData(query = 'data') {
        const queryString = location.search;
        const queryArray = new URLSearchParams(queryString) || queryString.split('&');
        if (queryArray.size <= 0) {
            return false;
        }
        let queryData = queryArray.get(query);
        if (queryData) {
            queryData = decodeURIComponent(queryData);
            try {
                return JSON.parse(queryData);
            } catch(e) {
                // console.warn(e);
                return false;
            }
        }
        // let queryRes;
        // queryArray.forEach((i)=> {
        //     try {
        //         if (!i) throw new Error(`'failed to parse queryString'`);
        //         queryRes += JSON.parse(decodeURIComponent(i));
        //     } catch (e) {
        //         console.warn(e, i);
        //         // queryRes += i;
        //     }
        // });
        // return queryRes;
    };
    
    function getDistrictCenter(markerData, position = '') {
        const defaultPosition = Object.values(markerData)[0].lnglat.split(',');
        if (!position) {
            return defaultPosition;
        }
        const central = markerData[position].lnglat;
        const center = central ? central.split(',') : defaultPosition;
        return center;
    }
    
    function loadLocalData(callback, fileName = '') {
        if (!fileName || fileName === '') {
            return;
        }
        return fetch(`<?php echo $default_path; ?>/${fileName}`, {
            method: 'GET',
        })
        .then(res=> {
            // console.log(res)
            if (!res.ok) throw new Error('loadLocalData failed.');
            try {
                return res.json();
            } catch(e) {
                return res.text();
            }
        })
        .then(data=> {
            if (callback) callback?.(data);
            console.log('MarkerData fullfilled.', data);
            return data;
        })
        .catch(error => {
            if (callback) callback?.(error);
            console.warn('Error fetching MarkerData:', error);
            return error;
        });
    }
    
    function cacheControler(content = '', cacheSet, callback) {
        const iframeContent = content.match(/<iframe\ssrc\s*=\s*['"]([^'"]+)['"]/i);
        const cacheFrame = content.match(/<iframe[^>]*data-cache-required\b[^>]*>/i);
        // console.log(cacheFrame)
        if (!iframeContent || !cacheFrame) {
            callback?.(content);
            return content;
        }
        const source = iframeContent[1];
        const hasCached = cacheSet.has(source);
        const hasIframe = iframeContent && iframeContent[0];
        if (hasCached) {
            const data = cacheSet.get(source);
            callback?.(data, hasIframe, hasCached);
            return data; //
        }
        if (hasIframe) {
            let result;
            const container = document.createElement('DIV');
            container.id = 'divContainer';
            // document.body.appendChild(container);
            // fetch page
            fetch(source, {
                method: 'GET',
                // credentials: 'include',
            })
            .then(res=> {
                if (!res.ok) throw new Error('loadURL failed.');
                return res.text();
            })
            .then(data=> {
                console.debug('data fullfilled.', data);
                result = data;
            })
            .catch(error => {
                console.warn('Error fetching data:', error);
                result = error;
            })
            .finally(() => {
                // container.contentDocument.documentElement.innerHTML = result;
                container.innerHTML = result;
                const containerString = container.outerHTML;
                // console.log(container, containerString)
                cacheSet.set(source, containerString);
                callback?.(containerString, hasIframe, hasCached);
                return containerString;
            });
        }
    }
    
    function setIframeWindow(container, content) {
        let iframeContainer = container.querySelector('#iframeContainer');
        // console.log(container, iframeContainer);
        if (!iframeContainer) {
            iframeContainer = document.createElement('IFRAME');
            iframeContainer.id = 'iframeContainer';
            iframeContainer.setAttribute('frameborder', 0);
            // document.body.appendChild(iframeContainer);
            container.innerHTML = '';
            container.appendChild(iframeContainer);
        }
        // if (defaultAmap) {
            // 使用微任务队列处理iframe内容，规避高德地图全局延迟渲染bug导致无法获取 iframeContainer
            queueMicrotask(()=> {
                iframeContainer.contentDocument.documentElement.innerHTML = content;
            });
        //     return;
        // }
        // iframeContainer.contentDocument.documentElement.innerHTML = content;
    }
    
    async function init() {
        let markerData;
        const minZoom = 2;
        const maxZoom = 20;
        const defaultZoom = <?php echo $default_zoom ? $default_zoom : 5; ?>;
        const defaultDistrict = "<?php echo $default_center; ?>";
        const defaultImage = '//imgs.2broear.com/2broear.png';
        const caches = new Map();
        const queryData = searchQueryData('panorama');
    <?php 
        echo $default_panorama_data;
        echo $default_coords_data;
        if ($default_amap) {
            $mapTheme = $default_theme === 'dark' ? 'grey' : 'white';
            echo 'const mapTheme = "' . $mapTheme . '";';
    ?>
            // overwrite local.json
            const customMarkerData = await loadLocalData(false, '<?php echo $default_datasrc; ?>');
            if (customMarkerData) markerData = customMarkerData;
            // overwrite URLSearchParams
            if (Object.prototype.toString.call(queryData)==='[object Object]') markerData = queryData;
            
            // 高德坐标转换器 // compatible function for amap-coords(lng,lat)
            function lng2lat(coordsString = '', splitArray = false) {
                if (Array.isArray(coordsString)) coordsString = coordsString[0];
                if (typeof coordsString !== 'string') {
                    console.warn('invalid coordsString', coordsString);
                    return;
                }
                const coords = coordsString.split(',');
                const lat = coords[1];
                const lng = coords[0];
                return splitArray ? [lat, lng] : `${lat},${lng}`;
            }
            
            // compatible both
            Object.values(markerData.district).forEach((item)=> item.lnglat = lng2lat(item.lnglat));
            markerData.points.forEach((item)=> item.lnglat = lng2lat(item.lnglat, true));
            // console.log(markerData.points, markerData.district);
            
            var center = getDistrictCenter(markerData.district, defaultDistrict);
            var map = new AMap.Map("container", {
                zoom: defaultZoom,
                center: center,  //设置地图中心点
                animateEnable: true,
                mapStyle: "amap://styles/" + mapTheme,
            });
            // map.setCenter(center);  //设置地图中心点
        
            // var layer_length = Object.keys(markerData.district).length;
            // var dynamic_size = 2 * layer_length;
            
            function getStyle(context) {
                var clusterData = context.clusterData[0]; // 聚合中包含数据
                var index = context.index; // 聚合的条件
                var count = context.count; // 聚合中点的总数
                var marker = context.marker; // 聚合绘制点 Marker 对象
                var color = [
                    'red',
        			'orangered',
                    'white',
                ];
                var indexs = ['city','district','context'];
                var i = indexs.indexOf(index['mainKey']);
                var size = Math.round(30 + Math.pow(count / markerData.points.length, 1 / 5) * 70);
                var mainKey = clusterData[index['mainKey']];
                var text;
                if(i < 2) {
                    text = '<b class="showCount">'+ count +'<span class="showName">' + mainKey + '</span></b>';
                } else {
                    text = `<span class="showName"><img src="${clusterData?.thumbnail || defaultImage}" alt="${mainKey}" /></span>`;
                    // size = 12 * text.length + 20;
                }
                // console.log(JSON.stringify(clusterData))
                var style = {
                    bgColor: color[i], //'rgba(' + color[i] + ',.8)',
                    borderColor: color[i], //'rgba(' + color[i] + ',1)'
                    context: `<b> ${mainKey} </b>`,
                    content: clusterData.content,
                    text: text,
                    size: size,
                    index: i,
                    marker: marker,
                    color: '#ffffff',
                    textAlign: 'center',
                    boxShadow: '0px 0px 5px rgba(0,0,0,0.8)'
                }
                return style;
            }
          
            function getPosition(context) {
                var key = context.index.mainKey;
                var dataItem = context.clusterData && context.clusterData[0];
                var districtName = dataItem[key];
                if(!markerData.district[districtName]) {
                    return null;
                }
                var center = markerData.district[districtName].lnglat.split(',');
                var centerLnglat = new AMap.LngLat(center[0], center[1]);
                return centerLnglat;
            };
            
            // 在 renderClusterMarker 外部定义 infoWindow 以关闭 infoWindow.close();
            var infoWindow = new AMap.InfoWindow({
                offset: new AMap.Pixel(0, -50),
                // anchor: 'bottom-center',
            });
            // markerData.points = markerData.points.forEach((item)=> item.lnglat[0] = lng2lat(item.lnglat[0]));
            new AMap.IndexCluster(map, markerData.points, {
                renderClusterMarker: function (context) {
                    var styleObj = getStyle(context);
                    var index = styleObj.index; // 聚合的条件
                    var marker = styleObj.marker; // 聚合点标记对象
                    // 自定义点标记样式
                    var div = document.createElement('div');
                    div.className = 'amap-cluster';
                    div.style.backgroundColor = styleObj.bgColor;
                    div.style.width = styleObj.size + 'px';
                    if(index <= 2) {
                        const maxZoom = index == 2;
                        div.style.height = styleObj.size + 'px';
                        // 点击事件
                        marker.on('click', function(e) {
                            var curZoom = map.getZoom();
                            // 递增放大倍数
                            if(curZoom < 20) curZoom += 2;
                            // reset id status
                            infoWindow.dom.id = '';
                            // (最大缩放倍数)
                            if (maxZoom) {
                                // console.log(infoWindow.dom)
                                if (styleObj.content) infoWindow.dom.id = 'panorama';
                                // infoWindow.setContent(content);
                                const content = styleObj.content || styleObj.context;
                                cacheControler(content, caches, (res, hasIframe, hasCached)=> {
                                    // 设置信息窗 字符串内容
                                    infoWindow.setContent(res);
                                    // 覆盖 iframe 信息窗口
                                    if (hasIframe) setIframeWindow(infoWindow.dom.querySelector('#divContainer'), res);
                                    // async-load container size bug
                                    infoWindow.setAnchor('bottom-center');
                                });
                                infoWindow.open(map, e.target.getPosition());
                                // center only
                                map.setCenter(e.lnglat);
                                // curZoom -= 1.9;  // 减小缩放倍数
                            } else {
                                // zoom & center by default
                                map.setZoomAndCenter(curZoom, e.lnglat);
                            }
                        }); // marker.on('touchend', clickEvent);
                        // (隐藏窗口 最大缩放)
                        if (maxZoom - 1) infoWindow.close(); // console.log('exit', infoWindow);
                    };
                    // 动态样式
                    div.style.border = `solid ${15 / (index + 2.5)}px rgb(255 255 255 / 55%)`; // + styleObj.borderColor;
                    div.style.borderRadius = styleObj.size + 'px';
                    div.innerHTML = styleObj.text;
                    div.style.color = styleObj.bgColor; //styleObj.color;
                    div.style.textAlign = styleObj.textAlign;
                    div.style.boxShadow = styleObj.boxShadow;
                    marker.setContent(div);
                    // 自定义聚合点标记显示位置
                    var position = getPosition(context);
                    if(position) marker.setPosition(position);
                    marker.setAnchor('center');
                },
                clusterIndexSet: {
                    city: {
                        minZoom: minZoom,
                        maxZoom: maxZoom / 4,
                    },
                    district: {
                        minZoom: maxZoom / 4,
                        maxZoom: maxZoom / 2,
                    },
                    context: {
                        minZoom: maxZoom / 2,
                        maxZoom: maxZoom,
                    },
                },
            });
<?php
        } else {
            // 注意EO缓存！
            $mapTheme = $default_theme === 'dark' ? 'style 6' : 'style 3';
            echo 'const mapTheme = "' . $mapTheme . '";';
?>
            // overwrite local.json
            const customMarkerData = await loadLocalData(false, '<?php echo $default_datasrc; ?>');
            if (customMarkerData) markerData = customMarkerData;
            // overwrite URLSearchParams
            if (Array.isArray(queryData)) markerData = queryData;
            // console.log(markerData);
            
            var map;
            var ClusterBubbleClick;
            var central = getDistrictCenter(markerData.district, defaultDistrict);
            var center = new TMap.LatLng(central[0], central[1]);
            
            // overwrite markerData(common data after getDistrictCenter)
            if (markerData?.points) markerData = markerData.points;
            
            map = new TMap.Map('mapContainer', {
                zoom: defaultZoom / 1.1,
                pitch: 1,
                center: center,
                minZoom: minZoom,
                maxZoom: maxZoom,
                draggable: true,
                scrollable: true,
                mapStyleId: mapTheme, //个性化样式
                doubleClickZoom: false, // 禁用双击地图放大
                // baseMap: { //设置底图样式
                //     type: 'vector', //设置底图为矢量底图
                //     features: [ //设置矢量底图要素类型
                //         'base',
                //         'point'
                //     ]
                // },
                //地图的默认鼠标指针样式
                draggableCursor: "crosshair",
 
                //拖动地图时的鼠标指针样式
                draggingCursor: "pointer",
            });
            
            // init markerData.positions LatLng
            markerData.forEach((item)=> {
                const latlng = item.position;
                if (Array.isArray(latlng)) {
                    item.position = new TMap.LatLng(latlng[0], latlng[1]);
                } else {
                    const coords = latlng.split(',');
                    item.position = new TMap.LatLng(coords[0], coords[1]);
                }
            });
            
            // 创建点聚合
            var markerCluster = new TMap.MarkerCluster({
                id: 'cluster',
                map: map,
                enableDefaultStyle: false, // 关闭默认样式
                minimumClusterSize: 2,
                geometries: markerData,
                zoomOnClick: true,
                gridSize: 60,
                averageCenter: false,
            });
            
            var clusterBubbleList = [];
            var markerGeometries = [];
            var marker = null;
            //初始化infoWindow marker 点击事件
            var infoWindow = new TMap.InfoWindow({
                map: map,
                position: new TMap.LatLng(39.984104, 116.307503),
                offset: { x: 0, y: -22 } //设置信息窗相对x: 0, y: -32偏移像素
            });
            infoWindow.close();//初始关闭信息窗关闭
            // infoWindow.dom.lastChild.remove();
            infoWindow.dom.className = 'infoWindow';
            
            // 配置默认标记样式/背景
            let markerStyles = {
                default: new TMap.MarkerStyle({
                    'width': 34,
                    'height': 42,
                    'anchor': {
                        x: 17,
                        y: 21,
                    },
                    'src': 'https://mapapi.qq.com/web/lbs/javascriptGL/demo/img/marker_blue.png',
                }),
            };
            const mapZoomEase = function(map = map, lat = central[0], lng = central[1], zoomStep = 2, pitchStep = 3) {
                let curZoom = map.getZoom() + zoomStep;
                let curCenter = new TMap.LatLng(lat, lng);
                // 直接缩放
                // map.setCenter(curCenter);  // exec before setZoom
                // map.setZoom(curZoom + 1);
                // 平滑缩放
                // map.panTo(curCenter, 500);
                // map.zoomTo(curZoom + 1, 500);
                // map.pitchTo(curZoom);
                let easeOptions = {
                    zoom: curZoom,
                    center: curCenter,
                    // pitch: curZoom * pitchStep
                };
                if (pitchStep) easeOptions['pitch'] = curZoom * pitchStep;
                map.easeTo(easeOptions);
            };
            // 监听标记事件
            const markerEventListener = function(marker = null) {
                //注册点标记事件
                if (!marker || marker._events?.click) {
                    return;
                }
                // console.log('register..', marker._events)
                // marker.addListener("click", function (evt) {
                marker.on("click", function (evt) {
                    // console.log(evt)
                    const geometry = evt.geometry;
                    // const customContent = markerData[geometry.index];
                    const customContent = geometry?.data ? geometry.data : geometry;
                    // console.log(customContent)
                    
                    // 平滑缩放
                    // map.panTo(geometry.position);
                    mapZoomEase(map, evt.latLng.lat, evt.latLng.lng, 0.1, 0);
                    
                    //设置 infoWindow
                    infoWindow.open(); //打开信息窗
                    
                    // clear before set
                    infoWindow.dom.id = '';
                    let context = customContent?.context;
                    if (customContent?.content || customContent?._content) {
                        infoWindow.dom.id = 'panorama';
                        context = customContent.content || customContent._content;
                    }
                    
                    // overwrite cacheControls
                    const content = context || geometry.position.toString();
                    cacheControler(content, caches, (res, hasIframe, hasCached)=> {
                        // 设置信息窗 字符串内容
                        infoWindow.setContent(res);
                        // 覆盖 iframe 信息窗口
                        if (hasIframe) setIframeWindow(infoWindow.dom.querySelector('#divContainer'), res);
                        // reset async-load container size
                        infoWindow.setPosition(geometry.position); //设置信息窗位置
                    });
                });
                marker.on("mousemove", (evt)=> {
                    const geometry = evt.geometry;
                    const target = evt.originalEvent.target;
                    const customContent = geometry?.data ? geometry.data : geometry;
                    target.style.cursor = 'pointer';
                    target.title = customContent.context || customContent.content || customContent._context || geometry.position.toString();
                });
                marker.on("hover", (evt)=> {
                    const target = evt.originalEvent.target;
                    target.style.cursor = 'auto';
                    target.title = '';
                });
            };
            
            // 监听聚合簇变化
            markerCluster.on('cluster_changed', function (e) {
                let getZoom = map.getZoom();
                // console.log(clusterBubbleList.length, getZoom)
                if (getZoom <= defaultZoom) {
                    map.pitchTo(0);  // reset pitch
                    infoWindow.close();
                }
                // 销毁旧聚合簇生成的覆盖物（大屏bug修复：小于最小缩放时停止销毁聚合）
                if (clusterBubbleList.length && getZoom >= defaultZoom) {
                    clusterBubbleList.forEach((item)=> item.destroy());
                    clusterBubbleList = [];
                }
                markerGeometries = [];
                
                // 根据新的聚合簇数组生成新的覆盖物和点标记图层
                var clusters = markerCluster.getClusters();
                clusters.forEach(function (item, index) {
                    // map.pitchTo(0);
                    if (item.geometries.length > 1) {
                        let clusterBubble = new ClusterBubble({
                            map,
                            position: item.center,
                            content: item.geometries.length,
                        });
                        
                        clusterBubble.on('click', () => {
                            // map.fitBounds(item.bounds);
                            mapZoomEase(map, item.center.lat, item.center.lng);
                        });
                        
                        clusterBubbleList.push(clusterBubble);
                        infoWindow.close();//缩放聚合时关闭信息窗
                    } else {
                        // console.log(index, item);
                        const customData = item.geometries[0];
                        markerGeometries.push({
                            // ...customData,
                            position: item.center,
                            data: customData,  // 储存当前自定义数据
                            // index: index,  // 定位 markerData 数据,当前cluster顺序混乱bug..
                        });
                        // infoWindow.open();//缩放聚合时关闭信息窗
                    };
                });
            
                // create marker
                if (marker) {
                    // 已创建过点标记图层，直接更新数据
                    // console.log(markerGeometries)
                    marker.setGeometries(markerGeometries);
                    markerGeometries.forEach((item, index)=> {
                        // const currentMarkerData = markerData[index]; //item.index
                        const currentMarkerData = item.data;
                        // console.log(currentMarkerData, marker);
                        if (currentMarkerData?.thumbnail) {
                            // 替换预设 content(fix tmap/ampa attr-conflict)
                            if (currentMarkerData?.content) {
                                currentMarkerData._content = currentMarkerData.content;
                                currentMarkerData.content = '';
                            }
                            // 重写自定义 thumbnail
                            markerStyles[currentMarkerData.id] = new TMap.MarkerStyle({
                                'width': 50,
                                'height': 50,
                                'anchor': {
                                    x: 25,
                                    y: 75,
                                },
                                'src': currentMarkerData.thumbnail,
                            });
                            const updateMarkerData = [{
                                ...currentMarkerData,
                                'id': currentMarkerData.id,
                                'styleId': currentMarkerData.id,
                                // 'index': index, //item.index
                            }];
                            // console.log('updateGeometries thumbnail', currentMarkerData, updateMarkerData, marker);
                            marker.updateGeometries(updateMarkerData);
                        }
                    });
                } else {
                    // 创建点标记图层
                    marker = new TMap.MultiMarker({
                        map,
                        styles: markerStyles,
                        geometries: markerGeometries
                    });
                    // 注册标记事件
                    markerEventListener(marker);
                };
            });
            
            
            // 以下代码为基于DOMOverlay实现聚合点气泡
            function ClusterBubble(options) {
                TMap.DOMOverlay.call(this, options);
            }
            
            ClusterBubble.prototype = new TMap.DOMOverlay();
            
            ClusterBubble.prototype.onInit = function (options) {
                this.content = options.content;
                this.position = options.position;
            };
            
            // 销毁时需要删除监听器
            ClusterBubble.prototype.onDestroy = function() {
                this.dom.removeEventListener('click', this.onClick);
                this.removeAllListeners();
            };
            
            ClusterBubble.prototype.onClick = function() {
                this.emit('click');
            };
            
            // 创建气泡DOM元素
            ClusterBubble.prototype.createDOM = function () {
                var dom = document.createElement('div');
                dom.classList.add('clusterBubble');
                dom.innerText = this.content;
                dom.style.cssText = [
                    'width: ' + (40 + parseInt(this.content) * 2) + 'px;',
                    'height: ' + (40 + parseInt(this.content) * 2) + 'px;',
                    'line-height: ' + (40 + parseInt(this.content) * 2) + 'px;',
                ].join(' ');
                
                // 监听点击事件，实现zoomOnClick
                this.onClick = this.onClick.bind(this);
                // pc端注册click事件，移动端注册touchend事件
                dom.addEventListener('click', this.onClick);
                dom.addEventListener('touchend', this.onClick);
                return dom;
            };
            
            ClusterBubble.prototype.updateDOM = function () {
                if (!this.map) {
                    return;
                }
                // 经纬度坐标转容器像素坐标
                let pixel = this.map.projectToContainer(this.position);
                
                // 使文本框中心点对齐经纬度坐标点
                let left = pixel.getX() - this.dom.clientWidth / 2 + 'px';
                let top = pixel.getY() - this.dom.clientHeight / 2 + 'px';
                this.dom.style.transform = `translate(${left}, ${top})`;
                
                this.emit('dom_updated');
            };
            
            window.ClusterBubble = ClusterBubble;
<?php
        }
?>
    };
    init();
</script>
<?php get_foot(); ?>
</body></html>