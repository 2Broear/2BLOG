// module "my-module.js"
// function cube(x) {
//   return x * x * x;
// }

function words_typer(el, str, speed=100, replace = false) {
    if(typeof(str)!='string' || str.replace(/^\s+|\s+$/g,"").replace( /^\s*/, '')===""){
        // throw new Error();
        str = "invalid string or NULL Responsed.";
    }
    // preset random placeholder if replace
    const shuffleString = Array.from(str).sort(() => 0.5 - Math.random()).join('');
    if (replace) el.textContent = shuffleString;
    // ..
    new Promise(function(resolve, reject){
        setTimeout(() => {
            const contextLength = str.length;
            el.classList.remove('load');
            for(let i=0, l=el.textContent.length; i<l; i++) {
                let elText = el.textContent, //replace ? shuffleString : 
                    elLen = elText.length - 1; //replace ? str.length - 1 : 
                setTimeout(() => {
                    if (replace) {
                        // el.textContent = elText.replace(elText[i], str[i]);
                    } else {
                        el.textContent = elText.slice(0, elLen - i);
                    }
                    // el.textContent = elText.slice(0, elLen - i);
                    if (i===elLen) resolve(el);
                }, i * 5);
            }
        }, 300);
    }).then((res)=>{
        setTimeout(() => {
            res.classList.remove('load');
            const stringLen = str.length;
            const chars = res.textContent.split(''); // 转为数组方便修改
            for (let i = 0; i < stringLen; i++) {
                setTimeout(() => {
                    if (replace) {
                        // res.textContent = replaceString.replace(replaceString[i], str[i]);
                        if (i < chars.length) {
                            chars[i] = str[i]; // 直接替换对应位置的字符
                        } else {
                            chars.push(str[i]); // 如果新字符串更长，追加字符
                        }
                        res.textContent = chars.join(''); // 更新文本
                    } else {
                        res.textContent += str[i]; // console.log(str[i]);
                    }
                    if (i + 1 === stringLen) {
                        res.classList.add('done');
                    }
                }, i * speed);
            }
        }, 300);
    }).catch(function(err){
        console.warn(err);
    });
};

export { words_typer };
