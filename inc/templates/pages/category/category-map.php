<?php
/*
    Template name: 足迹地图
    Template Post Type: page
*/
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php get_head(); ?>
    <style>
        .content-all, .win-top {
            height: 100%;
        }
        <?php
            $footprint_sw = get_option('site_footprint_switcher');
            $footprint_key = get_option('site_footprint_apikey');
            $footprint_data = get_option('site_footprint_data');
            $footprint_datas = get_option('site_footprint_panorama_data');
            $footprint_map= get_option('site_footprint_map');
            $default_amap = $footprint_map === 'amap';
            if ($default_amap) {
        ?>
                html, body, #container,
                .content-all, .win-top {
                    height: 100%;
                    width: 100%;
                }
                .amap-cluster {
                    display: flex;
                    justify-content: center;
                    flex-direction: column;
                    align-items: center;
                    font-size: 12px;
                    opacity: .75;
                }
                .showName {
                    font-size: 14px;
                }
                .showCount {
                    font-size: xx-large;
                }
                .showCount,.showName {
                    display: block;
                    text-overflow: ellipsis;
                    white-space: nowrap;
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
                /*#panorama .amap-info-content {*/
                /*    max-width: 100%;*/
                /*    padding: 10px;*/
                /*    border-radius: 15px;*/
                /*}*/
        <?php
            } else {
        ?>
                html,
                body {
                  margin: 0;
                  padding: 0;
                  overflow: hidden;
                  height: 100%;
                }
                #mapContainer {
                  position: relative;
                  height: 100%;
                  width: 100%;
                }
                .clusterBubble {
                  border-radius: 50%;
                  color: #fff;
                  font-size: x-large;
                  font-weight: 500;
                  text-align: center;
                  opacity: 0.88;
                  background-image: linear-gradient(139deg, #4294FF 0%, #295BFF 100%);
                  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.20);
                  position: absolute;
                  top: 0px;
                  left: 0px;
                  user-select: none;
                  -webkit-user-select: none;
                  cursor: pointer;
                  border: 8px solid rgb(255 255 255 / 55%);
                }
                .infoWindow > div:first-child {
                    padding: 10px 15px!important;
                    border-radius: 50px !important;
                }
                .infoWindow > div:first-child iframe {
                    border-radius: inherit;
                }
                .infoWindow > div:last-child {
                    display: none;
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
            background: linear-gradient(180deg, rgb(200 215 255 / 68%) 0%, rgb(255 255 255) 100%);
            background: -webkit-linear-gradient(270deg, rgb(200 215 255 / 68%) 0%, rgb(255 255 255) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgb(255 255 255);
        }
        #panorama img,
        #panorama iframe {
            display: block;
            max-width: 500px;
            max-height: 200px;
            border-radius: inherit;
            -webkit-user-drag: none;
            user-select: none;
            -webkit-user-select: none;
        }
        #panorama h2 {
            margin: 10px auto 5px;
        }
        #panorama p {
            margin-top: auto;
            opacity: .75;
            font-size: 12px;
        }
    </style>
</head>
<body class="<?php echo $theme_mode = theme_mode(true); ?>">
<div class="content-all">
    <div class="win-top bg">
        <?php 
            if (!$footprint_sw) echo '<div class="empty_card"><i class="icomoon icom icon-' . current_slug() . '" data-t=" EMPTY "></i><h1> ' . $footprint_key ? current_slug(1) : 'API KEY' . ' </h1></div></div>';
            switch ($footprint_map) {
                case 'tmap':
                    $map_container = '<div id="mapContainer"></div>';
                    $map_src = 'https://map.qq.com/api/gljs?v=1.exp&key=' . $footprint_key;
                    break;
                case 'amap':
                default:
                    $map_container = '<div id="container" class="map"></div>';
                    $map_src = 'https://webapi.amap.com/maps?v=2.0&key=' . $footprint_key . '&plugin=AMap.IndexCluster';
                    break;
            }
            echo $map_container;
        ?>
        <header>
            <nav id="tipson" class="ajaxloadon">
                <?php get_header(); ?>
            </nav>
        </header>
    </div>
    <!--<div class="content-all-windows">-->
    <!--</div>-->
    <footer>
        <?php //get_footer(); ?>
    </footer>
</div>
<!-- siteJs -->
<script src="<?php echo $map_src; ?>"></script>
<script type="text/javascript">
    const defaultZoom = 5;
    <?php 
        if (!$footprint_sw) exit;  // exit following php code
        if ($default_amap) {
            $mapTheme = $theme_mode === 'dark' ? 'grey' : 'white';
            echo 'const mapTheme = "' . $mapTheme . '";';
            // footprint_panorama_data
            echo $footprint_datas;
            // footprint_data(points, district)
            echo $footprint_data;
    ?>
            // compatible function for amap-coords(lng,lat)
            // 高德坐标转换器
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
            Object.values(district).forEach((item)=> item.lnglat = lng2lat(item.lnglat));
            points.forEach((item)=> item.lnglat = lng2lat(item.lnglat, true));
            // console.log(points, district);
            
            const mapContainer = document.querySelector('#container');
            var map = new AMap.Map("container", {
                zoom: defaultZoom,
                animateEnable: true,
                mapStyle: "amap://styles/" + mapTheme,
            });
        
            var layer_length = Object.keys(district).length;
            var dynamic_size = 5 * layer_length;
            
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
                var size = Math.round(30 + Math.pow(count / points.length, 1 / 5) * 70);
                var mainKey = clusterData[index['mainKey']];
                var text;
                if(i < 2) {
                    text = '<b class="showCount">'+ count +'<span class="showName">' + mainKey + '</span></b>';
                } else {
                    text = `<span class="showName"><img src="${clusterData.thumbnail}" alt="${mainKey}" /></span>`;
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
                if(!district[districtName]) {
                    return null;
                }
                var center = district[districtName].lnglat.split(',');
                var centerLnglat = new AMap.LngLat(center[0], center[1]);
                return centerLnglat;
            };
            
            // 在 renderClusterMarker 外部定义 infoWindow 以关闭 infoWindow.close();
            var infoWindow = new AMap.InfoWindow({offset: new AMap.Pixel(0, -30)});
            // points = points.forEach((item)=> item.lnglat[0] = lng2lat(item.lnglat[0]));
            new AMap.IndexCluster(map, points, {
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
                            // (最大缩放)
                            if (maxZoom) {
                                // console.log(infoWindow.dom)
                                if (styleObj.content) infoWindow.dom.id = 'panorama';
                                infoWindow.setContent(styleObj.content || styleObj.context);
                                infoWindow.open(map, e.target.getPosition());
                                map.setZoomAndCenter(curZoom, e.lnglat);
                                curZoom -= 1.9;  // 减小缩放倍数
                            }
                            map.setZoomAndCenter(curZoom, e.lnglat);
                        });
                        // (隐藏窗口 最大缩放)
                        if (maxZoom - 1) infoWindow.close();// console.log('exit', infoWindow);
                    };
                    // 动态样式
                    div.style.border = `solid ${dynamic_size / (index + 1)}px rgb(255 255 255 / 55%)`; // + styleObj.borderColor;
                    div.style.borderRadius = styleObj.size + 'px';
                    div.innerHTML = styleObj.text;
                    div.style.color = styleObj.color;
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
                        minZoom: 2,
                        maxZoom: 10,
                    },
                    district: {
                        minZoom: 10,
                        maxZoom: 12,
                    },
                    context: {
                        minZoom: 12,
                        maxZoom: 22,
                    },
                },
            });
    <?php
        } else {
            $mapTheme = $theme_mode === 'dark' ? 'style 6' : 'style 3';
            echo 'const mapTheme = "' . $mapTheme . '";';
            // footprint_panorama_data
            echo $footprint_datas;
            // footprint_data(geometries)
            echo $footprint_data;
    ?>
            var map;
            var ClusterBubbleClick;
            
            var drawContainer = document.getElementById('mapContainer');
            var center = new TMap.LatLng(39.953416, 116.380945);
            
            map = new TMap.Map('mapContainer', {
                zoom: defaultZoom,
                pitch: 10,
                center: center,
                minZoom: 5,
                maxZoom: 20,
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
            });
            
            // init markerData.positions LatLng
            markerData.forEach((item)=> {
                item.position = new TMap.LatLng(item.position[0], item.position[1]);
            });
            
            // 创建点聚合
            var markerCluster = new TMap.MarkerCluster({
                id: 'cluster',
                map: map,
                enableDefaultStyle: false, // 关闭默认样式
                minimumClusterSize: 3,
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
            
            // 监听聚合簇变化
            markerCluster.on('cluster_changed', function (e) {
                // 销毁旧聚合簇生成的覆盖物
                if (clusterBubbleList.length) {
                    clusterBubbleList.forEach(function (item) {
                        item.destroy();
                    })
                    clusterBubbleList = [];
                }
                markerGeometries = [];
                
                // 根据新的聚合簇数组生成新的覆盖物和点标记图层
                var clusters = markerCluster.getClusters();
                clusters.forEach(function (item, index) {
                    if (item.geometries.length > 1) {
                        let clusterBubble = new ClusterBubble({
                            map,
                            position: item.center,
                            content: item.geometries.length,
                        });
                        clusterBubble.on('click', () => {
                            // map.fitBounds(item.bounds);
                            let curZoom = map.getZoom() + 2;
                            let curCenter = new TMap.LatLng(item.center.lat, item.center.lng);
                            // 直接缩放
                            // map.setCenter(curCenter);  // exec before setZoom
                            // map.setZoom(curZoom + 1);
                            // 平滑缩放
                            // map.panTo(curCenter, 500);
                            // map.zoomTo(curZoom + 1, 500);
                            map.easeTo({
                                center: curCenter,
                                zoom: curZoom,
                            });
                        });
                        clusterBubbleList.push(clusterBubble);
                        infoWindow.close();//缩放聚合时关闭信息窗
                    } else {
                        // console.log(index)
                        markerGeometries.push({
                            position: item.center,
                            index: index,  // 定位 markerData 数据
                        });
                    };
                });
                
                // create marker
                if (marker) {
                    markerGeometries.forEach((item)=> {
                        const currentMarkerData = markerData[item.index];
                        // console.log(currentMarkerData, marker);
                        // 重写自定义 thumbnail
                        if (currentMarkerData?.thumbnail) {
                            console.log('updateGeometries', currentMarkerData)
                            // marker.updateGeometries([
                            //     {
                            //         'id': currentMarkerData.id,
                            //         "styleId": new TMap.MarkerStyle({
                            //             'width': 50,
                            //             'height': 50,
                            //             'anchor': {
                            //                 x: 17,
                            //                 y: 21,
                            //             },
                            //             'src': currentMarkerData?.thumbnail,
                            //         }),
                            //     }
                            // ]);
                        }
                    });
                    // 已创建过点标记图层，直接更新数据
                    marker.setGeometries(markerGeometries);
                } else {
                    // 创建点标记图层
                    marker = new TMap.MultiMarker({
                        map,
                        styles: {
                            default: new TMap.MarkerStyle({
                                'width': 34,
                                'height': 42,
                                'anchor': {
                                    x: 17,
                                    y: 21,
                                },
                                'src': 'https://mapapi.qq.com/web/lbs/javascriptGL/demo/img/marker_blue.png',
                            }),
                        },
                        geometries: markerGeometries
                    });
                }
                // console.log(markerGeometries)
                //marker 点击事件
                marker.on("click", function (evt) {
                    // console.log(evt)
                    const geometry = evt.geometry;
                    const markerIndex = geometry.index;
                    const customContent = markerData[markerIndex];
                    //设置infoWindow
                    infoWindow.open(); //打开信息窗
                    infoWindow.setPosition(geometry.position);//设置信息窗位置
                    // clear before set
                    infoWindow.dom.id = '';
                    let context = customContent?.context;
                    if (customContent?.content) {
                        infoWindow.dom.id = 'panorama';
                        context = customContent?.content;
                    }
                    infoWindow.setContent(context || geometry.position.toString());//设置信息窗内容
                })
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
</script>
<?php get_foot(); ?>
</body></html>