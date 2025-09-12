class _EventBus {
    // 使用嵌套 Map 存储不同事件类型的处理器
    #eventbus = new Map();  // Map<Element, Map<EventName, Handler>>
    
    bind (element, eventName, handler) {
        if (!element || !eventName || !handler) {
            console.warn('invalid element, handler or eventName provided, check', element, eventName, handler);
            return;
        }
        
        // 存储到事件处理器Map中
        if (!this.has(element)) {
            this.#eventbus.set(element, new Map());
        }
        const elementEvents = this.#eventbus.get(element);
        
        // 同时支持两种绑定方式
        const standardEventName = eventName.replace(/^on/, '');
        elementEvents.set(standardEventName, handler);
        
        // 优先使用 addEventListener，降级使用属性赋值
        try {
            element.addEventListener(standardEventName, handler);
        } catch (e) {
            element[eventName] = handler;
        }
    }
    
    unbind (element, eventName) {
        // 如果没有指定 element，解绑所有元素的事件
        if (!element) {
            this.#eventbus.forEach((events, el) => {
                events.forEach((handler, evt) => {
                    el.removeEventListener(evt, handler);
                });
            });
            this.#eventbus.clear();
            return;
        }

        const elementEvents = this.#eventbus.get(element);
        if (!elementEvents) return;

        // 如果没有指定 eventName，解绑该元素的所有事件
        if (!eventName) {
            elementEvents.forEach((handler, evt) => {
                element.removeEventListener(evt, handler);
            });
            this.#eventbus.delete(element);
            return;
        }

        // 解绑特定事件
        const handler = elementEvents.get(eventName);
        if (handler) {
            element.removeEventListener(eventName, handler);
            elementEvents.delete(eventName);
        }

        // 如果该元素没有其他事件了，从 Map 中删除
        if (elementEvents.size === 0) {
            this.#eventbus.delete(element);
        }
    }

    has (element, eventName) {
        return element && this.#eventbus.has(element) || eventName && this.#eventbus.has(eventName);
    }

}

class _Closure {
    // constructor() {
    //     this.delay = 200;
    // }
    static delay = 200;
    #delay = 200;
    
    debouncer (callback, delay = _Closure.delay) { //this.#delay
        var timer = null;
        return function(...args) {
            if(timer) clearTimeout(timer);
            timer = setTimeout(()=> callback.apply(this, args), delay);
        };
    }
    
    throttler (callback, delay = _Closure.delay) { //this.#delay
        let closure_variable = true;  //default running
        return function(...args) {
            if(!closure_variable) return;  //now running..
            closure_variable = false;  //stop running
            setTimeout(()=> {
                callback.apply(this, args); //arguments
                closure_variable = true;  //reset running
            }, delay);
        };
    }
}

class myPromise {
    
    static CONTEXT = {
        FULLFILLED: "fullfilled",
        REJECTED: "rejected",
        PENDING: "pending",
    }
    
    #promiseQueue = [];
    #promiseState = myPromise.CONTEXT.PENDING;
    #promiseResult;
    
    /*
    ** @resolve
    ** @rejected
    */
    constructor(executor) {
        const resolve = (data)=> {
            this.#changeState(myPromise.CONTEXT.FULLFILLED, data);
        };
        const reject = (err)=> {
            this.#changeState(myPromise.CONTEXT.REJECTED, err);
        };
        try {
            executor(resolve, reject);
        } catch (e) {
            reject(e);
        }
    }
    
    _isPending() {
        return this.#promiseState === myPromise.CONTEXT.PENDING;
    }
    
    _isFullfilled() {
        return this.#promiseState === myPromise.CONTEXT.FULLFILLED;
    }
    
    #changeState(state = '', data) {
        // 跳过非 pending 状态
        if (false === this._isPending()) return;
        // 改变 promise 状态/返回值
        this.#promiseState = state;
        this.#promiseResult = data;
        // 处理 promise 队列
        this.#run();
    }
    
    #handleCallback(callback, resolve, reject) {
        if (typeof callback !== 'function') {
            // 状态穿透（继承父级）
            queueMicrotask(()=> {
                const settled = this._isFullfilled() ? resolve : reject;
                settled(this.#promiseResult);
            });
            return;
        }
        // 成功回调
        queueMicrotask(()=> {
            try {
                // callback 作为 data 返回
                const data = callback(this.#promiseResult);
                resolve(data);  // 注：返回 this.#promiseResult 为穿透 result
            } catch (e) {
                reject(e);
            }
        });
    }
    
    #run() {
        // 跳过 pending 状态
        if (this._isPending()) return;
        // 循环处理 promise 队列
        while (this.#promiseQueue.length) {
            const { onFullfilled, onRejected, resolve, reject } = this.#promiseQueue.shift();
            const settled = this._isFullfilled() ? onFullfilled : onRejected;
            // 处理传入 onFullfilled / onRejected
            this.#handleCallback(settled, resolve, reject);
        }
    }
    
    then(onFullfilled, onRejected) {
        // 返回 promise 以供链式调用
        return new myPromise((resolve, reject)=> {
            // 加入 promise 队列（非立即执行）
            this.#promiseQueue.push({ onFullfilled, onRejected, resolve, reject });
            // 处理 promise 队列
            this.#run();
        });
    }
}

/*
**
*const pool = new RequestPool(2);
 pool.add(()=>fetch('/api')
     .then((res)=>console.log(res))
     .catch((err)=>console.warn(err)));
**
*/
class RequestPool {
    #queue = [];
    #running = 0;
    
    constructor(limit = 2) {
        this.limit = limit;
    }
    
    /*
    ** @requestFn => Promise
    */
    add(requestFn) {
        if (typeof requestFn !== 'function') {
            console.warn('invalid requestFn/');
            return;
        }
        return new MyPromise((resolve, reject)=> {
           this.#queue.push({requestFn, resolve, reject});
           this.#run();
        });
    }
    
    #run() {
        // if /while 每次循环检查队列后递归
        while (this.#queue.length && this.#running < this.limit) {
            // 取出队列任务
            const { requestFn, resolve, reject } = this.#queue.shift();
            // 处理状态 +1
            this.#running++;
            // 执行回调 promise
            requestFn()
            // ..
            .then(resolve)
            .catch(reject)
            .finally((res)=> {
                // 处理状态 -1
                this.#running--;
                // 递归调用
                this.#run();
            });
        }
    }
}

class _events extends _EventBus {
    
    constructor () {
        super();
    }

    get (event) {
        return event ? event : window.event;
    }

    add (element=null, type='', handler) {
        let //addEvent = this.add,
            init_func = function(element=null, type='', handler, callback){
                if(!element || !type) return;
                if(!handler || typeof handler!=='function') {
                    console.warn('addEvent callback handler err.');
                    return;
                }
                if (null !== element['on' + type]) {
                    console.warn('event registered.');
                    return;
                }
                callback?.();
                console.log(`event[${type}] set.`);
            };
        try {
            if (element.addEventListener) {
                this.add = function(element=null, type='', handler) {
                    init_func(element, type, handler, ()=>{
                        element.addEventListener(type, handler);
                    });
                };
            } else if (element.attachEvent) {
                this.add = function(element=null, type='', handler) {
                    init_func(element, type, handler, ()=>{
                        element.attachEvent('on'+type, handler);
                    });
                };
            } else {
                this.add = function(element=null, type='', handler) {
                    init_func(element, type, handler, ()=>{
                        element['on'+type] = handler;
                    });
                };
            }
            this.add(element, type, handler);
        } catch (error) {}
    }

    del (element=null, type='', handler) {
        // let delEvent = this.del;
        let init_func = function(element=null, type='', handler, callback){
            if(!element || !type) return;
            if(!handler || typeof handler!=='function') {
                console.warn('removeEvent callback err.');
                return;
            }
            callback?.();
            console.log(`event[${type}] clear.`);
        };
        try {
            if (element.removeEventListener) {
                this.del = function(element=null, type='', handler) {
                    init_func(element, type, handler, ()=>{
                        element.removeEventListener(type, handler);
                    });
                };
            } else if (element.detachEvent) {
                this.del = function(element=null, type='', handler){
                    init_func(element, type, handler, ()=>{
                        element.detachEvent('on'+type, handler);
                    });
                };
            } else {
                this.del = function(element=null, type='', handler){
                    init_func(element, type, handler, ()=>{
                        element['on'+type] = handler;
                    });
                };
            }
            this.del(element, type, handler);
        } catch (error) {}
    }
    
    target (event) {
        return this.get(event).target || window.srcElement;
    }

    click (effectArea, id, callback, debounce=200) {
        let execFn = (e) => {
            e = this.get(e);
            let t = this.target(e);
            if (!t || !t instanceof HTMLElement) return;
            if (!id) {
                // 如果未传入id参数，直接调用callback
                if (callback && typeof callback === 'function') callback(e, ...arguments);
                return;
            }
            while (t && t !== effectArea) {
                if (t.id === id || (t.classList && t.classList.contains(id)) || t.tagName.toLowerCase() === id.toLowerCase()) {
                    if (callback && typeof callback === 'function') callback(e, ...arguments);
                    break;
                }
                t = t.parentNode;
            }
        };
        let handler = debounce ? this.debouncer(execFn, debounce) : execFn;
        this.add(effectArea, 'click', handler);
    }

    scroll (effectArea, callback, throttle=200) {
        if(throttle) {
            // NOTE: throttled events can NOT be removed!!!
            this.add(effectArea, 'scroll', this.throttler(callback, throttle));
            return;
        }
        this.add(effectArea, 'scroll', callback);
    }
}

class _Basics {
    constructor () {
        this.detects = {
            validObj (obj) {
                return obj && Object.prototype.toString.call(obj)==='[object Object]';
            },
            validDom (node, textNode = true) {
                const valid_node = node && node instanceof HTMLElement;
                return textNode ? valid_node && node.nodeType===1 : valid_node;
            },
            validFun (fn) {
                // if(fn&&typeof fn==='function') fn?.();
                return fn && typeof fn === 'function';
            },
        };
        this.confRewriter = function Callee(rewrites, presets) {
            if(Object.prototype.toString.call(presets)!=='[object Object]') {
                return false;
            }
            for(const property in rewrites) {
                if(!rewrites.hasOwnProperty(property)) continue;
                const rewrite_conf = rewrites[property];
                if(Object.prototype.toString.call(rewrite_conf)==='[object Object]' && Reflect.ownKeys(rewrite_conf).length===0) continue;
                if (Object.prototype.toString.call(rewrite_conf) === '[object Object]') {
                    presets[property] = Callee(rewrite_conf, presets[property] || {}); //this.confRewriter
                } else {
                    presets[property] = rewrite_conf;
                }
            }
            return presets;
        };
    }
}

class _storage extends _Basics {
    constructor () {
        super();
        this.cookie = {
            set: (name, value, path='/', days=30)=> {
                let exp = new Date();
                exp.setTime(exp.getTime() + days*(24*60*60*1000));
                document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exp.toGMTString() + ';path=' + path; //escape
            },
            get: (cname)=> {
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for(var i=0,caLen=ca.length; i<caLen; i++) {
                    var c = ca[i];
                    while (c.charAt(0)==' ') c=c.substring(1);
                    if(c.indexOf(name) != -1) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            },
            del: function(name, path='/') {
                var exp = new Date();
                exp.setTime(exp.getTime() - 1);
                var cval = this.get(name);
                if(cval!=null){
                    document.cookie = name+"="+cval+";expires="+exp.toGMTString()+";path="+path;
                }
            },
        };
        this.local = {
            getLocalStorage: ()=> {
                if (typeof localStorage == "object"){
                    return localStorage;
                } else if (typeof globalStorage == "object"){
                    return globalStorage[location.host];
                } else {
                    throw new Error("Local storage not available.");
                }
            },
            set: (name="", data={})=> {
                try {
                    if(Object.prototype.toString.call(data)==='[object Object]') data = JSON.stringify(data);
                    this.getLocalStorage().setItem(name, data);
                } catch (e) {
                    console.warn(e);
                    return null;
                }
            },
            get: (name="", expires=0)=> {
                try {
                    if(isNaN(expires) || typeof expires !== 'number') throw new Error('maxAge must be number of millseconds!');
                    const storage = getLocalStorage();
                    const ts = storage.getItem(name),
                          ms = expires ? expires : 1*24*60*60;
                    if(parseInt(ts)+ms < Date.now()) {
                        storage.removeItem(name);
                        return null;
                    }
                    return ts;
                } catch (e) {
                    console.warn(e);
                    return null;
                }
            },
            _get: (name="")=> {
                try {
                    return this.getLocalStorage().getItem(name);
                } catch (e) {
                    console.warn(e);
                    return null;
                }
            },
        };
    }
}

class VisibilityObserver {
    /**
    * 创建一个 VisibilityObserver 实例
    * @param {Object} options - 配置选项
    * @param {number} options.threshold - 可见比例阈值 (0-1)
    * @param {number} options.rootMargin - 根元素的 margin
    * @param {Element} options.root - 根元素
    */
    constructor(options = {}) {
        this.options = {
            threshold: 0.01,
            rootMargin: '0px',
            root: null,
            ...options
        };
        
        this.observers = new Map();
        this.callbacks = new Map();
        
        // 检查浏览器是否支持 IntersectionObserver
        this.supportsIntersectionObserver = 
        'IntersectionObserver' in window &&
        'IntersectionObserverEntry' in window &&
        'intersectionRatio' in window.IntersectionObserverEntry.prototype;
        
        if (!this.supportsIntersectionObserver) console.warn('IntersectionObserver not supported, falling back to manual checking');
    }
    /**
    * 观察目标元素的可见性
    * @param {Element} target - 要观察的 DOM 元素
    * @param {Function} callback - 可见性变化时的回调函数
    * @param {Object} options - 覆盖实例选项的配置
    */
    observe(target, callback, options = {}) {
        if (!target || !(target instanceof Element)) throw new Error('Invalid target element');
        if (typeof callback !== 'function') throw new Error('Callback must be a function');
        
        const mergedOptions = { ...this.options, ...options };
        
        // 如果已经观察过这个元素，先取消观察
        if (this.observers.has(target)) this.unobserve(target);
        
        // 开始观察 IntersectionObserver
        if (this.supportsIntersectionObserver) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    callback({
                        target: entry.target,
                        isVisible: entry.isIntersecting && entry.intersectionRatio >= mergedOptions.threshold,
                        intersectionRatio: entry.intersectionRatio
                    });
                });
            }, mergedOptions);
            
            observer.observe(target);
            this.observers.set(target, observer);
        } else {
            // 兼容方案
            const checkVisibility = () => {
                const isVisible = this._isElementVisible(target, mergedOptions);
                callback({
                    target,
                    isVisible,
                    intersectionRatio: isVisible ? 1 : 0
                });
            };
            // 初始检查
            checkVisibility();
            // 存储回调以便后续取消
            this.callbacks.set(target, { callback, checkVisibility });
            // 添加滚动和resize事件监听
            // const eventbus = new _Closure();
            // eventbus.bind(window, 'scroll', eventbus.debouncer(checkVisibility, 1000));
            window.addEventListener('scroll', checkVisibility, { passive: true });
            window.addEventListener('resize', checkVisibility, { passive: true });
        }
    }
    /**
    * 停止观察目标元素
    * @param {Element} target - 要停止观察的 DOM 元素
    */
    unobserve(target) {
        if (this.supportsIntersectionObserver) {
            const observer = this.observers.get(target);
            if (observer) {
                observer.disconnect();
                this.observers.delete(target);
            }
        } else {
            const entry = this.callbacks.get(target);
            if (entry) {
                window.removeEventListener('scroll', entry.checkVisibility);
                window.removeEventListener('resize', entry.checkVisibility);
                this.callbacks.delete(target);
            }
        }
    }
    /**
    * 销毁所有观察者
    */
    disconnect() {
        if (this.supportsIntersectionObserver) {
            this.observers.forEach(observer => observer.disconnect());
            this.observers.clear();
        } else {
            this.callbacks.forEach(entry => {
                window.removeEventListener('scroll', entry.checkVisibility);
                window.removeEventListener('resize', entry.checkVisibility);
            });
            this.callbacks.clear();
        }
    }
    /**
    * 手动检查元素是否可见 (兼容方案)
    * @private
    */
    _isElementVisible(element, options) {
        if (!element || !element.getBoundingClientRect) return false;
        
        const rect = element.getBoundingClientRect();
        const rootRect = options.root ? options.root.getBoundingClientRect() : {
            top: 0,
            left: 0,
            right: window.innerWidth,
            bottom: window.innerHeight,
            width: window.innerWidth,
            height: window.innerHeight
        };
        
        // 计算元素与根元素的交集
        const intersectionRect = {
            top: Math.max(rect.top, rootRect.top),
            left: Math.max(rect.left, rootRect.left),
            bottom: Math.min(rect.bottom, rootRect.bottom),
            right: Math.min(rect.right, rootRect.right)
        };
        // 计算交集区域面积
        const intersectionArea = Math.max(0, intersectionRect.bottom - intersectionRect.top) * Math.max(0, intersectionRect.right - intersectionRect.left);
        // 计算元素总面积
        const elementArea = (rect.bottom - rect.top) * (rect.right - rect.left);
        // 计算可见比例
        const ratio = elementArea > 0 ? intersectionArea / elementArea : 0;
        
        return ratio >= options.threshold;
    }
}

// exports
export {
    _EventBus, _Closure, _Basics, _events, _storage, 
    VisibilityObserver,
};