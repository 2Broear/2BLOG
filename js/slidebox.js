// auto slideBox
`use strict`;

class EventEmitter {
    // 使用嵌套 Map 存储不同事件类型的处理器
    #events = new Map();  // Map<Element, Map<EventName, Handler>>

    bindEvents(element, eventName, handler) {
        if (!Utils.TYPEOF.Element(element) || !Utils.TYPEOF.String(eventName) || !Utils.TYPEOF.Function(handler)) {
            console.warn('invalid element, handler or eventName provided, check', element, eventName, handler);
            return;
        }
        
        // 存储到事件处理器Map中
        if (!this.#events.has(element)) {
            this.#events.set(element, new Map());
        }
        const elementEvents = this.#events.get(element);
        
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

    unbindEvents(element, eventName) {
        // 如果没有指定 element，解绑所有元素的事件
        if (!element) {
            this.#events.forEach((events, el) => {
                events.forEach((handler, evt) => {
                    el.removeEventListener(evt, handler);
                });
            });
            this.#events.clear();
            return;
        }

        const elementEvents = this.#events.get(element);
        if (!elementEvents) return;

        // 如果没有指定 eventName，解绑该元素的所有事件
        if (!eventName) {
            elementEvents.forEach((handler, evt) => {
                element.removeEventListener(evt, handler);
            });
            this.#events.delete(element);
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
            this.#events.delete(element);
        }
    }
}

class Utils extends EventEmitter {
    static BASIC = {
        confRewriter: function _confRewriter(rewrite = {}, preset = {}, merge = true) {
            const result = { ...preset };
            for (const key in rewrite) {
                if (rewrite.hasOwnProperty(key)) {
                    const validObjects = Utils.TYPEOF.Object(result[key]) && Utils.TYPEOF.Object(rewrite[key]);
                    if (!merge) {
                        // 递归合并对象
                        result[key] = validObjects ? _confRewriter(rewrite[key], result[key] || {}, merge) : rewrite[key];
                        continue;
                    }
                    switch (true) {
                        // 合并数组
                        case Array.isArray(result[key]) && Array.isArray(rewrite[key]):
                            result[key] = [...new Set([...result[key], ...rewrite[key]])];
                            break;
                        // 覆盖元素
                        case Utils.TYPEOF.Element(rewrite[key]): // rewrite[key] instanceof HTMLElement
                            result[key] = rewrite[key];
                            break;
                        // 递归合并对象
                        default:
                            result[key] = validObjects ? _confRewriter(rewrite[key], result[key] || {}, merge) : rewrite[key];
                    }
                }
            }
            return result;
        },
        randomNumber(from = 0, to = 1, fix = 2) {
            const random = (Math.random() * (to - from) + from);
            return parseFloat(random.toFixed(fix));
        },
        isEvenNumber: (num)=> num & 1 && num % 2 === 0,
    }

    static TYPEOF = {
        String: (str)=> typeof str === 'string',
        Object: (obj)=> Object.prototype.toString.call(obj) === '[object Object]',
        Element: (node)=> node && node instanceof HTMLElement && node.nodeType === 1,
        Function: (func)=> func && typeof func === 'function',
        Number: (num, int = false)=> {
            let isNum = !isNaN(num) && typeof num === 'number';
            if (int) return Utils.TYPEOF.Function(Number.isInteger) ? isNum && Number.isInteger(num) : isNum && num % 1 === 0;
            return isNum;
        },
    }
    
    static CLOSURE = {
        debounce: (func, delay = 200, iife = false)=> {
            let timer = null;
            return function() {
                if (Utils.TYPEOF.Function(iife)) iife();  // 每次触发防抖时 可选执行前置函数
                if (timer) clearTimeout(timer);  // 清除定时器
                timer = setTimeout(func, delay);  // 设置定时器
            }
        },
        throttle: (func, delay = 200)=> {
            let timer = null;
            return function() {
                if (timer) return;  // timer 正在执行..
                func.apply(this, arguments);  // 立即执行一次
                // 延迟清除 timer
                timer = setTimeout(()=> {
                    clearTimeout(timer);  // 清除定时器（内存泄漏）
                    timer = null;  // 重置 timer
                }, delay);
            }
        }
    }
}

class AutoSlideBox extends Utils {
    static DEFAULTS = {
        slideDebug: false,
        slideRandom: true,
        slideAnimate: null,
        slideReverse: false,
        slideRestart: 1000,
        slideDirection: 0, //1,-1,0
        slideOffsetsX: 0,
        slideOffsetsY: 0,
        slideClass: 'sliding',
        slideSpeed: .25,
        slideRound: -1,
        slideCount: 0,
        slideWidth: 0,
        slideHeight: 0,
        slideElements: {
            slideFrame: document.documentElement,
            slideBox: document.body,
        }
    }
    
    // 将 config 改为私有属性
    #config;
    
    constructor(_config = {}) {
        super();
        this.#config = Utils.BASIC.confRewriter.call(this, _config, AutoSlideBox.DEFAULTS);
    }
    
    // 提供受控的配置方法集合
    _config = {
        get: (key)=> key ? this.#config[key] : { ...this.#config },
        set: (key, value)=> {
            if (this._config.validate(key, value)) {
                this.#config[key] = value;
                this._config.changes(key);
                return true;
            }
            return false;
        },
        validate: (key, value)=> {
            switch(key) {
                case 'slideDirection':
                    return [-1, 0, 1].includes(value);
                case 'slideSpeed':
                    return Utils.TYPEOF.Number(value) && value > 0;
                case 'slideRound':
                    return Utils.TYPEOF.Number(value, true) && value >= -1; // 验证轮次
                case 'slideRestart':
                    return Utils.TYPEOF.Number(value, true) && value >= 0; // 验证重启延迟
            }
            return true;
        },
        // 处理配置变更后的副作用
        changes: (key)=> {
            switch(key) {
                case 'slideSpeed':
                    this._config.effects.updateSpeed();
                    break;
                case 'slideDirection':
                    this._config.effects.updateDirection();
                    break;
            }
        },
        effects: {
            updateSpeed: ()=> {
                // 更新动画速度
            },
            updateDirection: ()=> {
                // 更新动画方向
            }
        }
    }

    // #status 方法依赖访问实例的状态
    #status = {
        // static
        isScrollToEnabled: ()=> Utils.TYPEOF.Function(this.#config.slideElements.slideFrame.scrollTo),
        isScrollElementValid: ()=> Utils.TYPEOF.Element(this.#config.slideElements.slideFrame) && Utils.TYPEOF.Element(this.#config.slideElements.slideBox),
        // dynamic
        isScrollAvailable: ()=> {
            if (!this.#status.isScrollElementValid()) {
                console.warn('slideElements is NOT valid!', this.#config.slideElements);
                return false;
            }
            return this.#config.slideDirection ? this.#config.slideElements.slideFrame.scrollHeight >= this.#config.slideElements.slideFrame.offsetHeight : this.#config.slideElements.slideFrame.scrollWidth >= this.#config.slideElements.slideFrame.offsetWidth; // use >= incase default elements(html,body) provided
        },
    }
    
    // 更新动画状态
    #animationState() {
        // 缓存频繁访问的值（性能优化）
        const { slideDirection, slideOffsetsX, slideOffsetsY, slideWidth, slideHeight } = this.#config;
        return { 
            status: {
                isStart: slideDirection ? slideOffsetsY <= 0 : slideOffsetsX <= 0,  //this.#status.isScrollToStart(),
                isEnd: slideDirection ? slideOffsetsY >= slideHeight : slideOffsetsX >= slideWidth  //this.#status.isScrollToEnd(),
            }, 
            config: {
                direction: slideDirection,
                speed: this.#config.slideSpeed,
            }
        };
    }
    
    #animationDebug() {
        if (this.#config.slideDirection) {
            // if (this.#config.slideHeight !== this.#config.slideElements.slideFrame.offsetHeight) console.warn(`slideHeight(${this.#config.slideHeight}) !== slideFrame.offsetHeight(${this.#config.slideElements.slideFrame.offsetHeight})!`);
            console.log(this.#config.slideOffsetsY, this.#config.slideHeight);
        } else {
            // if (this.#config.slideWidth !== this.#config.slideElements.slideFrame.offsetWidth) console.warn(`slideWidth(${this.#config.slideWidth}) !== slideFrame.offsetWidth(${this.#config.slideElements.slideFrame.offsetWidth})!`);
            console.log(this.#config.slideOffsetsX, this.#config.slideWidth)
        }
    }
    
    #animationAdjust() {
        // scrollBy direction&reversible
        if (this.#config.slideDirection) {
            this.#config.slideReverse ? this.#config.slideOffsetsY -= this.#config.slideSpeed : this.#config.slideOffsetsY += this.#config.slideSpeed;
        } else {
            this.#config.slideReverse ? this.#config.slideOffsetsX -= this.#config.slideSpeed : this.#config.slideOffsetsX += this.#config.slideSpeed;
        }
        // dynamic(sync) adjust overflows(max&min)
        if (this.#config.slideOffsetsX < 0 || this.#config.slideOffsetsY < 0) this.#config.slideOffsetsX = this.#config.slideOffsetsY = 0;
        if (this.#config.slideOffsetsX > this.#config.slideWidth || this.#config.slideOffsetsY > this.#config.slideHeight) {
            this.#config.slideOffsetsX = this.#config.slideWidth;
            this.#config.slideOffsetsY = this.#config.slideHeight;
        }
    }

    #animationInbound() {
        const animationStates = this.#animationState();
        if (animationStates.status.isStart || animationStates.status.isEnd) {
            // specific rounds
            if (Utils.TYPEOF.Number(this.#config.slideRound, true) && this.#config.slideRound >= 0) {
                if (this.#config.slideRound === 0) {
                    this.abortAnimation(()=> {
                        console.log(`slideCount(${this.#config.slideCount})`, this.#config);
                    });
                    // unbind events
                    this.unbindEvents(this.#config.slideElements.slideFrame, 'onpointermove');
                    return;
                }
                ++this.#config.slideCount; // use ++i insted of i++ unbind events
                // prefix odd rounds
                if (this.#config.slideCount >= this.#config.slideRound) this.#config.slideRound = 0;
            }
            // Infinity loop
            this.abortAnimation(()=> {
                this.#config.slideReverse = animationStates.status.isEnd;  // reverse only if isScrollToEnd
                if (this.#config.slideRandom) this.#config.slideSpeed = Utils.BASIC.randomNumber(0.25);
                this.startAnimation();
            });
        }
    }

    startAnimation (callback = false) {
        // clear animation frame(if animateKey exists) before startAnimation
        if (this.#config.slideAnimate) cancelAnimationFrame(this.#config.slideAnimate);
        
        // requestAnimationFrame 中的箭头函数会确保 this 指向 slideBox 实例，从而避免 undefined 的问题 //()=>this.startAnimation()
        this.#config.slideAnimate = requestAnimationFrame(this.startAnimation.bind(this));

        // animation adjust(before start animation)
        this.#animationAdjust();

        // start animation
        this.#status.isScrollToEnabled ? this.#config.slideElements.slideFrame.scrollTo(this.#config.slideOffsetsX, this.#config.slideOffsetsY) : this.#config.slideElements.slideBox.style.transform = `translate(-${this.#config.slideOffsetsX}px, ${this.#config.slideOffsetsY}px)`;

        // animation inbound(after start animation)
        this.#animationInbound();

        // animation debug
        if (this.#config.slideDebug) this.#animationDebug();

        // animation callback
        if (Utils.TYPEOF.Function(callback)) callback();
    }
    
    abortAnimation (callback = false, delay = 0) {
        if (this.#config.slideAnimate) {
            cancelAnimationFrame(this.#config.slideAnimate);
            this.#config.slideAnimate = null; // 确保清除引用（内存管理优化）
        }
        if (Utils.TYPEOF.Function(callback)) {
            const restartDelay = delay ? delay : this.#config.slideRestart;
            if (restartDelay === 0 && delay === 0) {
                callback();
                return;
            }
            const timer = setTimeout(()=> {
                callback();
                clearTimeout(timer);
            }, restartDelay);
            console.log(`animation(${this.#config.slideAnimate}) abort, restart in ${restartDelay} ms..`);
            return;
        }
        console.log(`animation(${this.#config.slideAnimate}) stoped(without callback).`);
    }
    
    initAnimation(callback = false) {
        // console.log(this.#config.slideElements.slideFrame.scrollWidth , this.#config.slideElements.slideFrame.offsetWidth, this.#config.slideElements.slideFrame);
        if (!this.#status.isScrollAvailable()) {
            const error = new Error('Scroll is NOT Available!');
            error.details = {
                elements: this.#config.slideElements,
                direction: this.#config.slideDirection
            };
            throw error;
        }
        
        // update slideWidth/slideHeight after scrollWidth/scrollHeight updated.
        this.#config.slideElements.slideFrame.style.scrollBehavior = 'auto';
        this.#config.slideElements.slideFrame.classList.add(this.#config.slideClass);
        if (this.#config.slideDirection) {
            this.#config.slideHeight = this.#config.slideElements.slideFrame.scrollHeight - this.#config.slideElements.slideFrame.offsetHeight; 
            if (this.#config.slideHeight === 0) this.#config.slideHeight = this.#config.slideElements.slideFrame.scrollHeight;
        } else {
            this.#config.slideWidth = this.#config.slideElements.slideFrame.scrollWidth - this.#config.slideElements.slideFrame.offsetWidth;
            if (this.#config.slideWidth === 0) this.#config.slideWidth = this.#config.slideElements.slideFrame.scrollWidth;
        }
        
        // update dynamic status to static(multi-call performance issue)
        this.#status.isScrollToEnabled = Utils.TYPEOF.Function(this.#config.slideElements.slideFrame.scrollTo);
        
        // start animation
        this.startAnimation();
        console.log('animation init.', this);
        
        // bind events
        const that = this;
        this.bindEvents(this.#config.slideElements.slideFrame, 'onpointermove', Utils.CLOSURE.debounce(()=> {
            that.abortAnimation(that.startAnimation.bind(that), 0);
        }, 500, ()=> {
            // 立即取消动画
            if (that.#config.slideAnimate) {
                cancelAnimationFrame(that.#config.slideAnimate);
                that.#config.slideAnimate = null;
            }
        }));
        
        // init callback
        if (Utils.TYPEOF.Function(callback)) callback();
    }
}

export { AutoSlideBox, Utils }
