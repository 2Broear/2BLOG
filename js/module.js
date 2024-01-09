// module "my-module.js"
// function cube(x) {
//   return x * x * x;
// }

function words_typer(el, str, speed=100){
    if(typeof(str)!='string' || str.replace(/^\s+|\s+$/g,"").replace( /^\s*/, '')===""){
        // throw new Error();
        str = "invalid string or NULL responsed.";
    }
    new Promise(function(resolve,reject){
        setTimeout(() => {
            el.classList.remove('load');
            for(let i=0,textLen=el.innerText.length,timer=null;i<textLen;i++){
                // real-time data stream
                let elText = el.innerText,
                    elLen = elText.length-1;
                // timer = 
                setTimeout(() => {
                    el.innerText = elText.slice(0, elLen-i);
                    if(i===elLen) resolve(el);
                    // clearTimeout(timer);
                }, i*5);
            }
        }, 300);
    }).then((res)=>{
        setTimeout(() => {
            res.classList.remove('load');
            for(let i=0,strLen=str.length;i<strLen;i++){
                setTimeout(() => {
                    res.innerText += str[i]; // console.log(str[i]);
                    if(i+1===strLen) res.classList.add('done');
                }, i*speed);
            }
        }, 300);
    }).catch(function(err){
        console.warn(err);
    });
};

export { words_typer };
