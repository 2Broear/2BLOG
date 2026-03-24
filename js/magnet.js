'use strict';
const magnetCurosr = {
    dom: {
        elements: [{
            style: document.createElement('STYLE'),
        }, {
            spot: document.createElement('SPAN'),
            spots: document.createElement('SPAN'),
        }],
        initiate: function() {
            let _c = magnetCurosr,
                _s = _c.init?.conf,
                w_ = _s.element.follower; //null;
            const s_ = _s.static,
                  _e = _c.dom.elements,
                  cls = s_.class,
                  color = s_.theme,
                  cur = s_.cursor.pointer ? 'auto' : (w_ ? 'none' : 'auto'),
                  sty = _e[0].style;
            sty.innerText = `html,body{cursor:${cur};text-align:center;}.magnets:hover,.${cls.engager}:hover{/*z-index:1;*/}.${cls.engager},.magnets{transition: .35s cubic-bezier(0.22, 0.61, 0.36, 1);position:relative;transition-property: transform,opacity;/*background,cursor:inherit!important;display:inline-block;will-change:transform;*/}.magnets:hover{background:rgb(233 233 233 / 33%);/*background:${color.heavy};*/}.magnets{width:50px;height:50px;margin:15% auto;border-radius:100%;background:${color.light};border:1px solid transparent;backdrop-filter:blur(5px);}.magnets.disabled,.${cls.presser}.${cls.engager}{/*border-color: ${color.heavy};background: ${color.light};*/}.magnets.disabled{border-color:transparent;background:linear-gradient(-90deg,orange -10%,orangered 100%);background:-webkit-linear-gradient(180deg,orange -10%,orangered 100%);}.${cls.follower}{margin:5px;position:absolute;top:0;left:0;width:100%;height:100%;max-width:${s_.cursor.size}px;max-height:${s_.cursor.size}px;border-radius:100%;transition:opacity .35s ease;/*border:1px solid ${color.heavy};*/}.${cls.follower} #spot,.${cls.follower} #spots{display:block;width:15px;height:15px;background:${color.focus};border-radius:inherit;position:fixed;opacity:.5;pointer-events:none;z-index:1;/*top:50%;left:50%;transform:translate(-50%,-50%) scale(1);*/}.${cls.follower} #spot{transition:opacity .35s ease;z-index:2;/*mix-blend-mode:difference;*/}.${cls.follower} #spots{background:${color.heavy};transition:all .15s ease-out;transform:translate(-50%,-50%) scale(1.5);/*mix-blend-mode:hard-light;*/}.${cls.follower}.${cls.hider},.${cls.follower}.${cls.presser} #spots{opacity:0;transition-duration:.35s;}.${cls.follower}.${cls.presser} #spot{opacity:1;}`;
            document.head.appendChild(sty);
            if (w_) {
                if (w_ instanceof HTMLElement === false) {
                    _s.element.follower = document.createElement('DIV');
                    w_ = _s.element.follower;  // re-active follower
                }
                // console.warn(w_)
                w_.className = cls.follower;
                for (const [key, val] of Object.entries(_e[1])) {
                    val.id = key;
                    w_.appendChild(val);
                }
                _s.element.follower = w_;
            }
            _s.element.magnets ||= document.querySelector('.' + cls.engager);
        },
    },
    mods: {
        methods: {
            class_sw: (t=null, c="", tf=false, ff=tf)=>{
                try {
                    if(!t || !t.classList) {
                        return;
                        // throw new Error('target invalid.');
                    }
                    if(t.classList.contains(c)){
                        t.classList.remove(c);
                        if(tf && typeof tf === 'function') tf();
                    }else{
                        t.classList.add(c);
                        if(ff && typeof ff === 'function') ff();
                    }
                } catch (error) {
                    console.log(error);
                }
            },
            edge_reactor: function(coords, inbound_limit, box_size, tf, ef, df){
                if(coords <= inbound_limit){
                    if(tf && typeof(tf)==='function') tf();
                }else{
                    if(coords >= box_size){
                        if(ef && typeof(ef)==='function') ef();
                    }else{
                        if(df && typeof(df)==='function') df();
                    }
                }
            },
        },
        behavior: {
            movement: function(e, t=null, s=1, ox=0, oy=0, oc=0, m = false) {
                try {
                    if(!t) throw new Error('target invalid.');
                    const _s = this.init?.conf.static;
                    const originX = this.init?.conf.dynamic.originX;
                    const originY = this.init?.conf.dynamic.originY;
                    if (t.classList.contains(_s.class.engager)) {
                        s = t.classList.contains(_s.class.presser) ? _s.scale.origin : _s.scale.engaged;
                    }
                    // oc offset for cursor-offset
                    var o_x = ox - oc,
                        o_y = oy - oc;
                    if (m && _s.magnetic) {
                        let transformStep = t.dataset.magnetStep,
                            transformScale = t.dataset.magnetScale;
                        transformStep = transformStep && !isNaN(transformStep) ? transformStep : _s.magnetic_step;
                        transformScale = transformScale && !isNaN(transformScale) ? transformScale : s;
                        let translateX = (e.clientX - o_x) * transformStep,
                            translateY = (e.clientY - o_y) * transformStep;
                        t.style.transform = `translate(${translateX + originX}px, ${translateY + originY}px) scale(${transformScale})`;
                    } else {
                        // t.style.transform = `translate(${o_x}px, ${o_y}px) scale(${s})`;
                        const i_o = _s.edge_offset + t.offsetWidth,
                            b_w = document.documentElement.scrollWidth - i_o,
                            b_h = document.documentElement.clientHeight - i_o,
                            f_l = i_o - oc,
                            f_t = f_l, //i_o - oc
                            f_r = b_w - oc,
                            f_b = b_h - oc,
                            e_r = this.mods.methods.edge_reactor;
                        if (ox <= i_o) {
                            // top-left -> bottom-left -> left
                            e_r(oy, i_o, b_h, ()=>t.style.transform = `translate(${f_l}px, ${f_t}px) scale(${transformScale})`, 
                                ()=>t.style.transform = `translate(${f_l}px, ${f_b}px) scale(${transformScale})`, 
                                ()=>t.style.transform = `translate(${f_l}px, ${o_y}px) scale(${transformScale})`);
                        } else {
                            if (ox >= b_w) {
                                // top-right -> bottom-right -> right
                                e_r(oy, i_o, b_h, ()=>t.style.transform = `translate(${f_r}px, ${f_t}px) scale(${transformScale})`, 
                                    ()=>t.style.transform = `translate(${f_r}px, ${f_b}px) scale(${transformScale})`, 
                                    ()=>t.style.transform = `translate(${f_r}px, ${o_y}px) scale(${transformScale})`);
                            } else {
                                // top -> bottom -> default
                                e_r(oy, i_o, b_h, ()=>t.style.transform = `translate(${o_x}px, ${f_t}px) scale(${transformScale})`, 
                                    ()=>t.style.transform = `translate(${o_x}px, ${f_b}px) scale(${transformScale})`, 
                                    ()=>t.style.transform = `translate(${o_x}px, ${o_y}px) scale(${transformScale})`);
                            }
                        }
                    }
                } catch(error) {
                    console.log(error);
                }
            },
            pressing: function(e){
                e = e || window.event;
                // e.preventDefault(); // known issue: !bug of unable to select context!
                let t = e.target || e.srcElement,
                    _m = this.mods,
                    _s = this.init?.conf,
                    s_ = _s.static,
                    _cp = s_.class.presser,
                    _ce = s_.class.engager,
                    cs = _m.methods.class_sw,
                    me = _m.magnetic.entry;
                cs(_s.element.follower, _cp);
                // if (t.classList.contains(_ce)) {
                //     cs(t, _cp, ()=>{
                //         me.apply(this, [e, t, s_.scale.origin]);
                //         // remove pressing statu instantly
                //         const mag_in_press = document.querySelector('.'+_ce+'.'+_cp);
                //         if(mag_in_press) mag_in_press.classList.remove(_cp);
                //     }, ()=>me.apply(this, [e, t, s_.scale.engaged]));
                //     return;
                // }
                // // bug: remove first-only // _s.element.magnets.classList.remove(_cp);
                // // remove pressing statu from outside of the magnets
                // const mag_in_press = document.querySelector('.'+_ce+'.'+_cp);
                // if(mag_in_press) mag_in_press.classList.remove(_cp);
            },
            contacts: function(e, p = null) {
                e = e || window.event;
                try {
                    let t = e.target || e.srcElement,
                        _s = this.init?.conf,
                        s_ = _s.static,
                        d_ = _s.dynamic,
                        _c = s_.class.engager,
                        _m = this.mods.magnetic;
                    if (!t || !p) throw new Error('invalid contacts.');
                    while (t !== p) {
                        if (t.classList && t.classList.contains(_c)) {
                            // exec once before moving
                            const origin_transform = window.getComputedStyle(t).transform;
                            // record originXY before entry(movement) to prevent(moving-target) dubplicate calc
                            if (origin_transform !== 'none' && !t.classList.contains(s_.class.move)) { //t.style.transform === ''
                                const matrixValues = origin_transform.match(/matrix\(([^)]+)\)/)[1].split(", ").map(Number);
                                // const scaleX = matrixValues[0];
                                // const scaleY = matrixValues[3];
                                d_.originX = matrixValues[4]; // translateX += matrixValues[4];
                                d_.originY = matrixValues[5]; // translateY += matrixValues[5];
                                console.debug(t, origin_transform);
                            }
                            // movement begain..
                            _m.entry.apply(this, [e, t]);
                            if (!t.onmouseleave) t.onmouseleave = (e)=> _m.exits.apply(this, [e, t]);
                            // fix exits bug (in-pressing-out-in-up, out-pressing-in-up) by add class
                            // (alternated class) for pressing.call() mouse-up remove presser
                            if (!t.onmouseup && _s.element.follower) t.onmouseup = ()=>t.classList.add(s_.class.presser);
                            break;
                        } else {
                            t = t.parentNode;
                        }
                    }
                } catch (error) {
                    console.log(error);
                }
            },
        },
        magnetic: {
            entry: function(e, t=null, s=0){
                // console.log('entry s' + s);
                s = s ? s : this.init?.conf.static.scale.engaged;
                const rect = t.getBoundingClientRect();  // do NOT use t.offsetTop(in case of child-offset position)
                let enter_x = e.clientX,
                    enter_y = e.clientY,
                    // half of size/scale for offsets
                    scale_t = 2, //scale decrease times
                    scale_ox = (t.offsetWidth / 2) / scale_t,
                    scale_oy = (t.offsetHeight / 2) / scale_t,
                    range_x = parseInt((enter_x - rect.left) / scale_t), //t.offsetLeft
                    range_y = parseInt((enter_y - rect.top) / scale_t), //t.offsetTop
                    // +enter_* for movement substraction
                    range_xo = (scale_ox - range_x) + enter_x,
                    range_yo = (scale_oy - range_y) + enter_y;
                // console.log(enter_x, '-', range_xo, '=', enter_x - range_xo, ' scale-offset-x:', scale_ox);
                // console.warn(enter_y, '-', range_yo, '=', enter_y - range_yo, ' scale-offset-y:', scale_oy);
                t.classList.add(this.init?.conf.static.class.move);
                this.mods.behavior.movement.apply(this, [e, t, s, range_xo, range_yo, 0, true]);
            },
            exits: function(e, t) {
                const _d = this.init?.conf.dynamic;
                // console.log('exits:', e.clientX, e.clientY);
                t.style.transform = ''; //translate(0px, 0px)
                // cancel moving-target status && reset originXY
                t.classList.remove(this.init?.conf.static.class.move);
                _d.originX = _d.originY = 0;
            },
        },
    },
    __proto__: {
        init: function(user_conf = {}) {
            try {
                const CUR = magnetCurosr,
                      INT = CUR.init;
                // rewrite user-conf.
                let that = this;
                if(Object.getPrototypeOf(that) !== INT.prototype){ //that.__proto__
                    that = INT.prototype;
                    console.warn('keyword "new" is recommended for initiate, current pointed:', this);
                    // throw new Error('"new" generator magnetCurosr init required.');
                }
                Object.defineProperty(that, '_conf', {
                    // value: that._rewriter.call(that, user_conf),
                    value: that._singleton_conf._rewriter.call(that, user_conf),
                    // enumerable: true,
                });
                user_conf = (INT.conf = that._conf);
                // initiate dom..
                CUR.dom.initiate();
                // dispatch events..
                const _warpper = user_conf.element.follower,
                      _s = user_conf.static,
                      _e = CUR.dom.elements[1],
                      _m = CUR.mods.behavior;
                let _w_width = 0,
                    _w_offset = 0;
                if (_warpper) {
                    document.body.appendChild(_warpper);
                    _w_width = _warpper.offsetWidth;
                    _w_offset = _w_width - (_w_width / 1.8);
                    document.onmouseenter = ()=>_warpper.classList.remove(_s.class.hider, _s.class.presser);
                    document.onmouseleave = ()=>_warpper.classList.add(_s.class.hider);
                    document.onmousedown = document.onmouseup = (e)=>_m.pressing.call(CUR, e);
                    document.onmousemove = function(e) {
                        const offset_x = e.clientX,
                              offset_y = e.clientY;
                        _m.movement.apply(CUR, [e, _e.spot, _s.scale.origin, offset_x, offset_y, _w_offset]);
                        _m.movement.apply(CUR, [e, _e.spots, _s.scale.followed, offset_x, offset_y, _w_offset]);
                        _m.contacts.apply(CUR, [e, this]);
                    };
                }
                document.onmousemove = function(e) {
                    _m.contacts.apply(CUR, [e, this]);
                };
                console.log('magnetCurosr initiated.', CUR);
            } catch (error) {
                console.log(error);
            }
        },
    }
};

Object.defineProperties(magnetCurosr.init.prototype, {
    _singleton_conf: {
        value: function(){
            let private_presets = {
                    static: {
                        magnetic: true,
                        magnetic_step: 0.5,
                        edge_offset: 0.1,
                        cursor: {size: 28, pointer: false},
                        scale: {origin: 1,engaged: 1.3,followed: 1.5},
                        theme: {light: 'whitesmoke',heavy: 'lightgrey',focus: 'darkgrey'},
                        class: {follower: 'follower',engager: 'magnetic',presser: 'pressing',hider: 'hide',move: 'moving'},
                    },
                    dynamic: {
                        originX: 0,
                        originY: 0,
                    },
                    element: {
                        follower: true,
                        magnets: null
                    },
                };
            return {
                // private_preset: private_presets,
                public_default: Object.create(null),
                _rewriter: function fn(conf=this.public_default, opts=private_presets) {
                    if(opts &&typeof opts === "object"){
                        for(const [key, val] of Object.entries(opts)){
                            // conf[key] ||= val;  // back-write (mark non-existent property)
                            conf[key] ??= val;
                            // this._rewriter.apply(this, [opts[key], val]);
                            fn.apply(this, [conf[key], val]);  // recursion-loop (use fn insted for recursion-func)
                            // arguments.callee.apply(this, [conf[key], val]);
                        }
                    }
                    private_presets = null;  // clear closure recycle-quotes
                    return conf;
                },
            }
        }(),
        configurable: false,
    },
});

export { magnetCurosr };