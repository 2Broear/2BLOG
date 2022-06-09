const cursor = document.createElement("div");
      style = document.createElement("style");
cursor.setAttribute("class","pointer");
cursor.innerHTML = `<span id="dot"></span><span id="pot"></span>`;
style.innerHTML = `body.dark .pointer #dot,body.dark .pointer #pot{background:white;}.pointer.entering #dot{opacity:.66;}.pointer.entering > #pot{border-radius:10px;}.pointer{width:15px;height:15px;position:fixed;pointer-events:none;transition:opacity .35s ease;top:-15px;left:-15px;z-index:99999;}.pointer #dot,.pointer #pot{width:inherit;height:inherit;display:block;background:black;border-radius:50%;position:absolute;top:0;left:0;}.pointer #dot{opacity:.32;transition:opacity .36s ease;}.pointer #pot{opacity:.08;transform:scale(1.5);transition:all .15s ease-out;}`;
document.head.appendChild(style);
document.body.appendChild(cursor);
var pointer = document.querySelector(".pointer"),
    dot = pointer.querySelector("#dot"),
    pot = pointer.querySelector("#pot"),
    all = document.querySelector(".content-all"),
    ps = pointer.getBoundingClientRect().width,
    pow = 5,
    scale = [1,1.5],
    curXpos = 0,
    curYpos = 0,
    autoMove = (e,p,s,t)=>{
        let Z = pointer.offsetWidth-pow,
            X = e.clientX+Z,
            Y = e.clientY+Z;
        t ? p.style.transform=`translate(${X}px,${Y}px) scale(${s||1})` : false;  //静止运动（未解绑事件）
    },
    autoMoving = (d,p)=>{
        document.onmousemove=null;
        document.onmousemove=(e)=>{
            autoMove(e,dot,scale[0],d);
            autoMove(e,pot,scale[1],p)
        }
    },
    pressing = (e,s)=>{
        let z = pointer.offsetWidth-pow,
            x = e.clientX+z,
            y = e.clientY+z;
        pot.style.transform=`translate(${x}px,${y}px) scale(${s})`;
        dot.style.opacity='0.75';
    },
    enterLink = (t)=>{
        autoMoving(true,false)
        let linkWidth = t.offsetWidth,
            linkHeight = t.offsetHeight
            linkOffset = pointer.offsetWidth,
            linkLeft = t.getBoundingClientRect().x+linkOffset,
            linkTop = t.getBoundingClientRect().y+linkOffset;
        pot.style.width = `${linkWidth}px`;
        pot.style.height = `${linkHeight}px`;
        pot.style.transform=`translate(${linkLeft}px,${linkTop}px) scale(${scale[0]})`;
        pointer.classList.add("entering");
    },
    leaveLink = (e)=>{
        let linkOffset = pointer.offsetWidth;
            curXpos = dot.getBoundingClientRect().x+linkOffset;
            curYpos = dot.getBoundingClientRect().y+linkOffset;
        autoMoving(true,true)
        pot.style.width = ``;
        pot.style.height = ``;
        pot.style.transform = ``;
        pointer.classList.remove("entering");
    };
    document.onmouseup=(e)=>{pressing(e,scale[1]);dot.style.opacity='';}
    document.onmousedown=(e)=>{pressing(e,scale[0])}
    document.onmouseenter=()=>{pointer.style.opacity=1}
    document.onmouseleave=()=>{pointer.style.opacity=0}
    //解除绑定事件的时候一定要用函数的句柄，把整个函数写上是无法解除绑定的
    autoMoving(true,true);
all.onmouseover=function(e){
    var t = e.target;
    // 创建 while 循环，直到 t="a"，否则循环向上遍历 t 的 parentNode 是否为 "a"
    while (t!=all) {
        if(t.nodeName.toLowerCase()=="a"){
            t.addEventListener("mouseenter",function(e){enterLink(t)},false);
            t.addEventListener("mouseleave",leaveLink,false);
            break;
        }else{
            t = t.parentNode;
        }
    }
}