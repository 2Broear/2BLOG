
//macos test
$(function(){
	var jsonObj = [{
		"title":{
			"en":"anime",
			"cn":"动漫电影"
		}
	}, {
		"title":{
			"en":"comic",
			"cn":"动画漫画"
		}
	}, {
		"title":{
			"en":"game",
			"cn":"游戏佳作"
		}
	}, {
		"title":{
			"en":"movie",
			"cn":"精彩影片"
		}
	}, {
		"title":{
			"en":"tv",
			"cn":"电视剧"
		}
	}];
	for(k in jsonObj){
		let val = jsonObj[k],
			jsonc = val.title.cn,
			jsone = val.title.en;
		// 加载元素/00
		$(".counter").append('<div class="'+jsone+'"><a href="#'+jsone+'" rel="nofollow"><h2>0</h2><p>'+jsonc+'/'+jsone.toUpperCase()+'</p></a></div>')
		$("#comment_txt").before('<div class="inbox-clip wow fadeInUp" data-wow-delay="0.25s"><h2 id="'+jsone+'">'+jsonc+'<sup> '+jsone+' </sup></h2></div><div class="info '+jsone+' flexboxes wow fadeInUp" data-wow-delay="0.15s"><div class="inbox more flexboxes"><div class="inbox-more flexboxes"><a href="javascript:;" title="加载更多" rel="nofollow"></a></div></div></div>');
	};

	//request AV.Query
	const query_acg = new AV.Query("acg"),
		  rcmdbox = document.querySelector(".rcmd-boxes"),
		  loadlist = ["anime","comic","game","movie","tv"],
		  template = (title,subtitle,desc,link,img)=>{
			  return `<div class="inbox-headside flexboxes"><span class="author">${subtitle}</span><img class="bg" src="${img}" /><img src="${img}" /></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="${link}" target="_blank" rel="nofollow">${title}</a></h4></span><span class="lowside-description"><p>${desc}</p></span></div>`
		  },
		  templates = (title,subtitle,desc,link,img,rating,ign,gs)=>{
			  return `<div class="inbox-headside flexboxes"><span class="author">${subtitle}</span><img class="bg" src="${img}" /><img src="${img}" /></div><div class="inbox-aside"><span class="lowside-title"><h4><a href="${link}" target="_blank" rel="nofollow">${title}</a></h4></span><span class="lowside-description"><p>${desc}</p></span><div class="game-ratings ${rating}"><div class="ign" title="IGN Ratings"><h3 style="color: #fff;">${ign}</h3></div><div class="gamespot" title="GameSpot Ratings"><div class="range"><span id="before"></span><span id="after"></span></div><span id="spot"><h3 style="color: #fff;">${gs}</h3></span></div></div></div>`
		  },
		  counting = function(timer,times,tjson,tel){
			  timer = setInterval(function(){
				  times++;
				  tel.innerHTML=`${times}<sup>+</sup>`;
				  times>=tjson ? clearInterval(timer) : false
			  },50)
		  };
	for(let i=0;i<loadlist.length;i++){
		let eachload = loadlist[i],
			loading = document.createElement("span"),
			evalload = rcmdbox.querySelector(`.${eachload}`);
		loading.id="loading";
		evalload.appendChild(loading);
	};
	query_acg.addAscending("createdAt").limit(999).find().then(result=>{  //Descending
		var times = 0,
			types = [],
			typed = [];
		for (let i=0; i<result.length;i++) {
			let res = result[i],
				title = res.attributes.title,
				subtitle = res.attributes.subtitle,
				desc = res.attributes.desc,
				link = res.attributes.src,
				img = res.attributes.img,
				type = res.attributes.type_acg,
				rating = res.attributes.rating,
				ign = res.attributes.ign,
				gs = res.attributes.gs,
				standard = template(title,subtitle,desc,link,img),
				gameplay = templates(title,subtitle,desc,link,img,rating,ign,gs);
			types.push(type);
			types.sort();
			for(let i=0;i<loadlist.length;i++){
				let eachload = loadlist[i],
					evalload = rcmdbox.querySelector(`.${eachload}`),
					inbox = document.createElement("div");
				inbox.setAttribute("class","inbox flexboxes");
				if(type==eachload){
					if(rating!="disabled"||ign||gs){
						inbox.innerHTML += gameplay;
						evalload.insertBefore(inbox,evalload.firstChild);
						ratingRange();
					}else{
						inbox.innerHTML += standard;
						evalload.insertBefore(inbox,evalload.firstChild);
					}
				}
			}
		};
		for(let i=0;i<types.length;){  
			let count=0;  
			for(var j=i;j<types.length;j++){  
				if(types[i]==types[j]){  
					count++;  
				}
			}
			typed.push([types[i],count]);  
			i+=count;  
		};
		//res 二维数维中保存了 值和值的重复数  
		for(let  i=0 ;i<typed.length;i++){  
			let typename = typed[i][0],
				typenum = typed[i][1],
				count = document.querySelector(`.counter .${typename} h2`);
			counting(typenum,times,typenum,count);
		};
// 		$("img.lazy").lazyload();
		const loads = document.querySelectorAll("span#loading");
		for(let i=0;i<loads.length;i++){
			loads[i].remove()
		}
	});
	
	// jsonObj-jgame 评分逻辑
	function ratingRange(){
		var baseRange = 90,afterRange = 180,
			fullRangeA = baseRange+afterRange;
		$('.inbox-aside .game-ratings.gs .gamespot h3').each(function(){
			var RS = $(this),
				RSP = RS.parent().siblings('.range'),
				RSPA = RSP.children('#after'),
				RSPB = RSP.children('#before'),
				RSLen = RS.length,
				RSTxt = $(this).text(), //String
				RSNum = (parseInt(RSTxt)),
				RSNumFloat = (parseFloat(RSTxt)),
				RSNumPer = (RSNumFloat/10).toFixed(2), //Number Percentage 0.85
				RSNumPerFloat = (RSNumPer*fullRangeA).toFixed(1); //0.85(percent)*270(fullRangeA)=rotateAngle
			if(RSNum > 0){
				var cSpots,_cSpots,
					_RSNumPerFloat = RSNumPerFloat, //设定对比值]
					_RSNumPerFloat_ = (RSNumPer*(90)).toFixed(1); //特定对比值
				function numFloat(){
					var num=0,
						timer = setInterval(function(){
						num+=0.1;
						if(RSNumFloat > 0 && RSNumFloat <= 5){
							RS.text((num+0.0).toFixed(1));
						}else if(RSNumFloat > 5 && RSNumFloat < 10){
							RS.text((num-0.1).toFixed(1));
						}else if(RSNumFloat == 10){
							RS.text((num-0.1).toFixed(0));
						}
						if(num>=RSTxt){
							clearInterval(timer);
						}
					},20);
				};
				//得分大于0，小于等于5
				if(RSNumFloat > 0 && RSNumFloat <= 5){
					RSP.addClass('RSBIndex');
					RSPA.hide();
					RSPB.css({'transform':'rotate('+(_RSNumPerFloat_)+'deg)'}); //(-RSNumPerFloat)反向旋转
					//mouseenter/leave:
					RS.parents('.rcmd-boxes .info.game .inbox').mouseenter(function(){
						RSPB.css({'transform':'rotate('+(-RSNumPerFloat)+'deg)'}); //-90
						numFloat(); //浮动数字
					}).mouseleave(function(){
						RSPB.css({'transform':'rotate('+(_RSNumPerFloat_)+'deg)'}); //%
					});
				};
				
				//得分大于5，小于等于10
				if(RSNumFloat > 5 && RSNumFloat <= 10){
					RSPA.css({'transform':'rotate('+RSNumPerFloat+'deg)'}); //按百分比设定初始值
					//mouseenter/leave:animation
					RS.parents('.rcmd-boxes .info.game .inbox').mouseenter(function(){
						//clearInterval(timer); //清除浮动数字
						clearInterval(_cSpots); //清除本地定时器
						clearInterval(cSpots); //清除(鼠标移出)定时器
						//设定本地定时器
						_cSpots = setInterval(function(){
							RSNumPerFloat--;
							RSPA.css({'transform':'rotate('+(RSNumPerFloat)+'deg)'});
							if(RSNumPerFloat <= 30){
								RSP.addClass('RSBIndex');
								RSPB.css({'transform':'rotate('+(RSNumPerFloat)+'deg)'});
								RSPA.css({'z-index':''});
							}
							if(RSNumPerFloat <= (-90)){
								clearInterval(_cSpots); //清除本地定时器
							}
						},0);
						numFloat(); //浮动数字
					}).mouseleave(function(){
						clearInterval(_cSpots); //清除(鼠标移入)定时器
						clearInterval(cSpots); //清除本地定时器
						//设定本地定时器
						cSpots = setInterval(function(){
							RSNumPerFloat++;
							RSPA.css({'transform':'rotate('+(RSNumPerFloat)+'deg)'});
							if(RSNumPerFloat > 160){
								RSPB.css({'transform':''});
								RSPA.css({'z-index':'4'});
							}
							if(RSNumPerFloat >= (_RSNumPerFloat)){
								clearInterval(cSpots); //清除本地定时器
							}
						},0)
					});
				};
			};
			switch(true){
				case (RSNumFloat == 10):
					RSP.addClass('Essential');
					break;
				case (RSNumFloat>=9 && RSNumFloat<10):
					RSP.addClass('Superb');
					break;
				case (RSNumFloat>=8 && RSNumFloat<9):
					RSP.addClass('Great');
					break;
				case (RSNumFloat>=7 && RSNumFloat<8):
					RSP.addClass('Good');
					break;
				case (RSNumFloat>=6 && RSNumFloat<7):
					RSP.addClass('Fair');
					break;
				case (RSNumFloat>=5 && RSNumFloat<6):
					RSP.addClass('Medicore');
					break;
				case (RSNumFloat>=4 && RSNumFloat<5):
					RSP.addClass('Poor');
					break;
				case (RSNumFloat>=3 && RSNumFloat<4):
					RSP.addClass('Bad');
					break;
				case (RSNumFloat>=2 && RSNumFloat<3):
					RSP.addClass('Terrible');
					break;
				case (RSNumFloat>=1 && RSNumFloat<2):
					RSP.addClass('Abysmal');
					break;
				default:
					return 'error'
			};
		})
	}
})
