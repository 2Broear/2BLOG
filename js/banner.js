
  const box = document.querySelector(".banner"),
        banner = box.querySelector(".banner ul"),
        lists = banner.children,
        first = banner.children[0].cloneNode(true),
        prev = box.querySelector(".switcher #banner-prev"),
        next = box.querySelector(".switcher #banner-next"),
        dots = box.querySelector(".dots"),
        currentOffset = -num*preWidth,  //never reads
        // delayDuration = 500,
        animate = (el,set,unit,cbk)=>{
            el.timer = setInterval(() => {
                el.style.cssText += `transition-duration:${transitionDelay}ms`;  //过渡动画
                el.style.transform = `translate(${set+unit},0${unit})`;  //瞬间完成事件，需要设置延时
                //计算当前px相对百分比
                // let off = unit==="%" ? -num*preWidth : num*_getComputedStyle(el,"transform",4);
                // console.log(num*preWidth+" , "+set)
                if(set==set){
                    clearInterval(el.timer);  //清除定时器
                    el.timer = null;  //清空定时器
                    cbk ? cbk() : false;
                }
            }, 15);
        },
        statu = (els,index,reset,delay)=>{
            let hold = null,
                cur = "active",
                pre = "preload",
                run = function(){
                    let curstep = index==lists.length-1 ? index=0 : index,  //克隆节点位置判断
                        nexstep = index+1==lists.length-1 ? index=0 : index+1;  //预备节点
                    for(let i=0,elsLen=els.length;i<elsLen;i++){
                        els[i].classList.remove(cur)
                        els[i].classList.remove(pre)
                    };
                    els[curstep].classList.add(cur);  //active style
                    els[nexstep].classList.add(pre);  //preload style
                };
            if(reset){
                delay||delay==0 ? delay : delay=350;  //触发型statu函数，判断是否立即执行（注意0为false）
                for(let i=0,elsLen=els.length;i<elsLen;i++){
                    els[i].classList.remove(pre);
                    els[i].classList.remove(cur);
                };
                switch (reset) {
                    case 'stop':
                        clearTimeout(hold);
                        run();
                        break;
                    default:
                        hold = setTimeout(() => {
                            run();
                            clearTimeout(hold)
                        }, delay);
                        break;
                }
            }else{
                run()
            }
        },
        _getComputedStyle = function(element,property,which) {
            const st = window.getComputedStyle(element, null),
                tr = st.getPropertyValue('-webkit-'+property) ||
                    st.getPropertyValue('-moz-'+property) ||
                    st.getPropertyValue('-ms-'+property) ||
                    st.getPropertyValue('-o-'+property) ||
                    st.getPropertyValue(property) || 'FAIL';
            if (tr === 'FAIL') {
                return '';
            };
            if(which){
                let target = tr.split(",")[which];
                if(target){
                    return Number(target)
                }else{
                    return 0
                }
            }else{
                return tr
            }
        },
        debounce = function(fn, wait) {    
          var timeout = null;    
          return function() {
              if(timeout !== null)   clearTimeout(timeout);        
              timeout = setTimeout(fn, wait);    
          }
        },
        throttle = function(fn, wait){
          var timer = null;
          return function(){
              if(timer==null){  //!timer
                  timer = setTimeout(function(){
                      fn();
                      timer = null  //消除定时器表示激活
                  },wait)
              }
          }
        };
  var num = 0,
      flag = true,
      preUnit = "%",
      preWidth = 100,
    //   preUnit = "px",
    //   preWidth = banner.offsetWidth,  //仅计算第一次窗口相对距离（无法自动更新）宽度不确定有小数点偏差
      preOffset = preUnit=="%" ? 15 : 10,
      transitionDelay = 750,
      intervalDelay = 3000,
      debounceDelay = 300;
  for(let i=0,listLen=lists.length;i<listLen;i++){
      let dot = document.createElement("span");
      dot.setAttribute("index",i);
      dot.classList.add("dot");
      dot.innerHTML = "<em></em>";
      dots.appendChild(dot);
  };
  dots.children[0].classList.add("active");
  dots.children[1] ? dots.children[1].classList.add("preload") : false;
  const circle = dots.children;
  banner.appendChild(first);
  var clearEvent = function(el){
          // el.onmousedown = null;
          el.onmousemove = null;
          el.onmouseup = null;
          el.onmouseleave = null;
          // el.ontouchstart = null;
          el.ontouchmove = null;
          el.ontouchend = null;
      },
      //releaseEvent 拖拽释放（完成）事件处理程序
      releaseEvent = function(target,start,route,offset,max){
        //   runTimer();  //timer clear&run for mouseevent [PC]
          //..
          clearEvent(target);  //解绑所有事件
          // target.style.transform="";
          let curTrans = num*preWidth,  //lastTrans = _getComputedStyle(target,"transform",4),  //BUG：需要在animate执行后才能获取到准确值
              condition = curTrans+max,  //执行条件（当前偏移量+最大偏移宽度）
              checknum = (n)=>{
                  if(n||n==0){  //当n=0时会返回false
                      curTrans = n*preWidth;
                      target.style.transform=`translate(${-curTrans+preUnit},0${preUnit})`
                  }else{
                      target.style.transform=`translate(${-curTrans+preUnit},0${preUnit})`
                  };
                  statu(circle,num,true,0);
                //   statu(circle,num,true);  //状态点（进度条）重置
              },
              connect = function(number,isNext){
                  //debounce bug: 拖拽释放时使用 moving(fn,delay) 函数会导致过渡动画中断一次（最后或最前时）
                  if(isNext){
                      offset<-condition ? toNext() : checknum(number)
                  }else{
                      offset>condition ? toPrev() : checknum(number)
                  }
              };
          if(route>start){
              condition = -curTrans+max;  //反向
              if(num==0){
                  //banner.style.cssText += `transition-duration:${transitionDelay}ms!important`;  //过渡
                  connect(lists.length-1)
              }else{
                  connect(null)
              }
          }else{
              if(num==lists.length-1){
                  connect(0,true)
              }else{
                  connect(null,true)
              }
          }
      },
      //dragLogic 拖拽事件（处理中）处理逻辑
      dragLogic = function(target,startPoint,moveRoute,moveOffset,preUnit){
          clearInterval(timer)//clearTimer();  //clear-only for mouseevent [PC] 
          //..
          target.style.transform = `translate(${moveOffset+preUnit},0${preUnit})`;  //上次+当前偏移量
          if(num==0){
              if(moveRoute>startPoint){
                //   console.log('prev')
                  target.style.transform =  `translate(${moveOffset+(-(lists.length-1)*preWidth)+preUnit},0${preUnit})`;
              }
          }else if(num==lists.length-1){
              if(moveRoute<startPoint){
                //   console.log('next')
                  target.style.transform =  `translate(${moveOffset-(-(lists.length-1)*preWidth)+preUnit},0${preUnit})`;
              }
          }
      },
      mouseEvent = function(){
          let startPoint, moveRoute, moveOffset;
          banner.onmousedown=function(e){
              e.preventDefault();  //阻止默认行为（拖拽）
              var _this = this,  //prevTrans = preUnit==="%" ? -num*preWidth : num*_getComputedStyle(_this,"transform",4),  //通过单位判断上次偏移量记录;
                  maxWidth = parseInt(preWidth/20);
              _this.style.cssText += `transition-duration:0ms;cursor:grabbing;`;
              startPoint = parseInt(e.clientX);
              _this.onmousemove=function(e){
                  e.preventDefault();  //阻止默认行为（拖拽）
                  moveRoute = parseInt(e.clientX);  //移动路径
                  moveOffset = -num*preWidth+(moveRoute-startPoint)/preOffset;  //减少位移量
                  //必须移动后再触发事件（bug：原地点击触发）
                  if(startPoint!=moveRoute){
                      dragLogic(_this,startPoint,moveRoute,moveOffset,preUnit);  //拖拽及方向无缝逻辑
                      //按下后松开/离开
                      _this.onmouseup = _this.onmouseleave = function(){
                          releaseEvent(_this,startPoint,moveRoute,moveOffset,maxWidth);
                          _this.style.cssText += `transition-duration:${transitionDelay}ms;cursor:grab;`;
                      }
                  }
              };
              _this.onmouseup = _this.onmouseleave = function(){
                  clearEvent(_this)  // 始终执行松开后销毁
                  //必须移动后再触发事件（bug：原地点击触发）
                  if(startPoint!=moveRoute){
                      _this.style.cssText += `transition-duration:${transitionDelay}ms;cursor:grab;`;
                  }
              };
          }
      },
      touchEvent = function() {
          let startPoint, moveRoute, moveOffset;
          banner.ontouchstart=function(e){
              var _this = this,  //prevTrans = preUnit==="%" ? -num*preWidth : num*_getComputedStyle(_this,"transform",4),  //通过单位判断上次偏移量记录;
                  maxWidth = parseInt(preWidth/20);
              _this.style.cssText += `transition-duration:0ms;cursor:grabbing;`;
              startPoint = parseInt(e.touches[0].clientX);
              _this.ontouchmove=function(e){
                  moveRoute = parseInt(e.touches[0].clientX);  //移动路径
                  moveOffset = -num*preWidth+(moveRoute-startPoint)/preOffset;  //减少位移量
                  //必须存在移动操作才能执行（bug：原地点击触发事件）
                  if(startPoint!=moveRoute){
                      dragLogic(_this,startPoint,moveRoute,moveOffset,preUnit);  //拖拽及方向无缝逻辑
                      _this.ontouchend=function(){
                          releaseEvent(_this,startPoint,moveRoute,moveOffset,maxWidth);
                          _this.style.cssText += `transition-duration:${transitionDelay}ms;cursor:grab;`;
                      }
                  }
              }
          }
      },
      toPrev = function(delay){
          preUnit!=="%" ? preWidth = banner.offsetWidth : preWidth;  //每次点击更新当前每次移动距离（仅限px）
          if(num==0){
              num = lists.length-1;
              banner.style.cssText += `transition-duration:0ms;`;  //取消过渡
              banner.style.transform = `translate(${-num*preWidth+preUnit},0${preUnit})`;  //瞬移
          }
          num--;  //执行判断后递减，再执行 banner 位移
          animate(banner,-num*preWidth,preUnit);
          statu(circle,num);  //状态点跟随
        //   console.log(num)
      },
      toNext = function(){
          preUnit!=="%" ? preWidth = banner.offsetWidth : preWidth;  //每次点击更新当前每次移动距离（仅限px）
          if(num==lists.length-1){
              num = 0;
              banner.style.cssText += `transition-duration:0ms;`;  //瞬移
              banner.style.transform = `translate(0${preUnit},0${preUnit})`;
          }
          num++;  //执行判断后递增，再执行 banner 位移
          animate(banner,-num*preWidth,preUnit);
          statu(circle,num);  //状态点跟随
        //   console.log(num)
      },
      forward = debounce(toNext,debounceDelay),
      backward = debounce(toPrev,debounceDelay),
      moving = function(fn,delay){
          return debounce(fn,delay)()
      },
      timer,
      timerSetup = function(){
        timer = setInterval(() => {
              moving(toNext,debounceDelay)//forward()  //next.click();
          }, intervalDelay)
      },
      clearTimer = function(){
          clearInterval(timer);   //timer = null;
      },
      runTimer = function(){
          clearInterval(timer)//clearTimer();
          timerSetup()
      };

    // runTimer();  // auto start
    // box.onmousedown = 
    box.ontouchstart = box.onmousedown = 
    dots.onmouseenter = dots.ontouchstart = 
    prev.onmouseenter = prev.ontouchstart = 
    next.onmouseenter = next.ontouchstart = function(){
        clearInterval(timer)//clearTimer();
        statu(circle,num,'stop');  //状态点（进度条）终止（解绑box事件）
    };
    // box.onmouseup = 
    box.ontouchend = box.onmouseup = 
    dots.ontouchend = dots.onmouseleave = 
    prev.onmouseleave = next.onmouseleave = function(){
        // runTimer();
        statu(circle,num,true,0);  //状态点（进度条瞬间完成）重置
    };
    
    //swipe mouseevent
    mouseEvent();
    //swipe touchevent..
    touchEvent();
    // bind click event
    box.onclick=(e)=>{
        e = e || window.event;
        let t = e.target || e.srcElement;
        if(!t) return;
        while(t!=box){
            if(t.id=="banner-prev"){
                timer ? clearInterval(timer) : false;
                backward();  //点击事件可使用 debounce 防抖，拖拽释放事件直接执行动画函数
                break;
            }else if(t.id=="banner-next"){
                timer ? clearInterval(timer) : false;
                forward();  //moving(toNext,debounceDelay);//debounce(toNext,debounce
                break;
            }else if(t.nodeName.toLowerCase()=="em"){
                preUnit!=="%" ? preWidth = banner.offsetWidth : preWidth;  //每次点击更新当前每次移动距离（仅限px）
                let index = t.parentNode.getAttribute("index");  //获取到的是 String 类型
                num = Number(index);  //赋值当前 index 到全局 num
                animate(banner,-num*preWidth,preUnit,function(){
                    flag=true;  //banner 位移动画结束后重新启用运动
                });
                statu(circle,num);  //激活当前状态点
                break;
            }else{
                t = t.parentNode;
            }
        }
    }
