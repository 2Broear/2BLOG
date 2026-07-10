class MagneticEffect {
  static defaultConfig = {
    magnetic: true,
    magneticStep: 0.5,
    cursor: { size: 28, pointer: false },
    scale: { origin: 1, engaged: 1.3, followed: 1.5 },
    theme: { light: 'whitesmoke', heavy: 'lightgrey', focus: 'darkgrey' },
    class: {
      follower: 'follower',
      engager: 'magnetic',
      presser: 'pressing',
      hider: 'hide',
      move: 'moving'
    },
    element: {
      follower: true,
      magnets: null
    }
  };

  constructor(userConfig = {}) {
    this.config = this._mergeConfig(MagneticEffect.defaultConfig, userConfig);
    this._originMap = new WeakMap();
    this._styleElement = null;
    this._follower = null;
    this._followerSize = 0;       // 缓存 follower 宽度
    this._followerOffset = 0;     // 缓存偏移量
    this._boundHandlers = {};
    this._magneticElements = new Set();
    this._init();
  }

  _mergeConfig(defaults, custom) {
    const merged = { ...defaults };
    for (const key of Object.keys(custom)) {
      const customVal = custom[key];
      const defaultVal = defaults[key];
      if (
        customVal !== null &&
        typeof customVal === 'object' &&
        !Array.isArray(customVal) &&
        defaultVal !== null &&
        typeof defaultVal === 'object' &&
        !Array.isArray(defaultVal)
      ) {
        merged[key] = this._mergeConfig(defaultVal, customVal);
      } else if (Array.isArray(customVal)) {
        merged[key] = [...customVal];      // 数组浅拷贝
      } else {
        merged[key] = customVal;
      }
    }
    return merged;
  }

  _init() {
    this._createStyle();
    this._setupFollower();
    this._bindEvents();
  }

  _createStyle() {
    const { cursor, theme, class: cls } = this.config;
    const cursorStyle = cursor.pointer
      ? 'auto'
      : this.config.element.follower
        ? 'none'
        : 'auto';

    const css = `
      html, body { cursor: ${cursorStyle} !important; text-align: center; }
      .magnets:hover, .${cls.engager}:hover {
        transition-timing-function: cubic-bezier(0.22, 0.61, 0.36, 1) !important;
      }
      .${cls.engager}, .magnets {
        transition: .35s;
        position: relative;
        transition-property: transform, opacity;
      }
      .magnets:hover { background: rgb(233 233 233 / 33%); }
      .magnets {
        width: 50px; height: 50px; margin: 15% auto; border-radius: 100%;
        background: ${theme.light}; border: 1px solid transparent; backdrop-filter: blur(5px);
      }
      .magnets.disabled {
        border-color: transparent;
        background: linear-gradient(-90deg, orange -10%, orangered 100%);
        background: -webkit-linear-gradient(180deg, orange -10%, orangered 100%);
      }
      .${cls.follower} {
        margin: 5px; position: absolute; top: 0; left: 0; z-index: 9999;
        width: 100%; height: 100%; max-width: ${cursor.size}px; max-height: ${cursor.size}px;
        border-radius: 100%; transition: opacity .35s ease;
      }
      .${cls.follower} #spot, .${cls.follower} #spots {
        display: block; width: 15px; height: 15px; background: ${theme.focus};
        border-radius: inherit; position: fixed; opacity: .5; pointer-events: none; z-index: 1;
      }
      .${cls.follower} #spot {
        transition: opacity .35s ease; z-index: 2;
      }
      .${cls.follower} #spots {
        background: ${theme.heavy};
        transition: all .15s ease-out;
        transform: translate(-50%, -50%) scale(1.5);
      }
      .${cls.follower}.${cls.hider}, .${cls.follower}.${cls.presser} #spots {
        opacity: 0; transition-duration: .35s;
      }
      .${cls.follower}.${cls.presser} #spot { opacity: 1; }
    `;

    const style = document.createElement('style');
    style.textContent = css;
    document.head.appendChild(style);
    this._styleElement = style;
  }

  _setupFollower() {
    const config = this.config;
    if (!config.element.follower) return;

    let follower = config.element.follower;
    if (!(follower instanceof HTMLElement)) {
      follower = document.createElement('div');
      config.element.follower = follower;
    }
    follower.className = config.class.follower;

    const spot = document.createElement('span');
    spot.id = 'spot';
    const spots = document.createElement('span');
    spots.id = 'spots';

    follower.appendChild(spot);
    follower.appendChild(spots);
    document.body.appendChild(follower);

    // 缓存尺寸，避免频繁重排
    this._followerSize = follower.offsetWidth;
    this._followerOffset = this._followerSize - this._followerSize / 1.8;

    this._follower = { element: follower, spot, spots };
  }

  _bindEvents() {
    const handler = this._boundHandlers;
    handler.onMouseMove = this._onMouseMove.bind(this);
    handler.onMouseDown = this._onMouseDown.bind(this);
    handler.onMouseUp = this._onMouseUp.bind(this);
    handler.onMouseEnter = this._onMouseEnter.bind(this);
    handler.onMouseLeave = this._onMouseLeave.bind(this);

    document.addEventListener('mousemove', handler.onMouseMove);
    if (this._follower) {
      document.addEventListener('mousedown', handler.onMouseDown);
      document.addEventListener('mouseup', handler.onMouseUp);
      document.documentElement.addEventListener('mouseenter', handler.onMouseEnter);
      document.documentElement.addEventListener('mouseleave', handler.onMouseLeave);
    }
  }

  _onMouseMove(e) {
    this._handleMagnetic(e);
    this._moveFollower(e);
  }

  _handleMagnetic(e) {
    let target = e.target;
    const { class: cls, scale, magnetic, magneticStep } = this.config;

    while (target) {
      if (target.classList && target.classList.contains(cls.engager)) {
        if (!this._magneticElements.has(target)) {
          this._setupMagneticElement(target);
        }

        // 读取/初始化元素独立 origin
        let origin = this._originMap.get(target);
        if (!origin) {
          origin = { x: 0, y: 0 };
        }

        if (!target.classList.contains(cls.move)) {
          const originTransform = window.getComputedStyle(target).transform;
          if (originTransform !== 'none') {
            // 兼容 matrix 和 matrix3d
            const matrixMatch = originTransform.match(/matrix(3d)?\(([^)]+)\)/);
            if (matrixMatch) {
              const values = matrixMatch[2].split(', ').map(Number);
              if (matrixMatch[1] === '3d') {
                origin.x = values[12];
                origin.y = values[13];
              } else {
                origin.x = values[4];
                origin.y = values[5];
              }
            } else {
              origin.x = 0;
              origin.y = 0;
            }
          } else {
            origin.x = 0;
            origin.y = 0;
          }
          this._originMap.set(target, origin);
          target.classList.add(cls.move);
        }

        const s = target.classList.contains(cls.presser) ? scale.origin : scale.engaged;
        const rect = target.getBoundingClientRect();
        const scale_t = 2;
        const scale_ox = (target.offsetWidth / scale_t) / scale_t;
        const scale_oy = (target.offsetHeight / scale_t) / scale_t;
        const range_x = Math.floor((e.clientX - rect.left) / scale_t);
        const range_y = Math.floor((e.clientY - rect.top) / scale_t);
        const range_xo = (scale_ox - range_x) + e.clientX;
        const range_yo = (scale_oy - range_y) + e.clientY;

        const useMagnetic = magnetic && magneticStep > 0;
        this._applyMovement(e, target, s, range_xo, range_yo, 0, useMagnetic, origin.x, origin.y);
        break;
      }
      target = target.parentElement;
    }
  }

  _setupMagneticElement(element) {
    this._magneticElements.add(element);
    const cls = this.config.class;

    const onLeave = () => {
      element.style.transform = '';
      element.classList.remove(cls.move, cls.presser);

      // 彻底清理
      this._originMap.delete(element);
      this._magneticElements.delete(element);

      element.removeEventListener('mouseleave', element._magneticLeave);
      if (element._magneticDown)
        element.removeEventListener('mousedown', element._magneticDown);
      if (element._magneticUp)
        element.removeEventListener('mouseup', element._magneticUp);

      delete element._magneticLeave;
      delete element._magneticDown;
      delete element._magneticUp;
    };

    element.addEventListener('mouseleave', onLeave);
    element._magneticLeave = onLeave;

    if (this._follower) {
      const onDown = () => element.classList.add(cls.presser);
      const onUp = () => element.classList.remove(cls.presser);
      element.addEventListener('mousedown', onDown);
      element.addEventListener('mouseup', onUp);
      element._magneticDown = onDown;
      element._magneticUp = onUp;
    }
  }

  _applyMovement(e, target, scale, ox, oy, oc = 0, useMagnetic = false, originX = 0, originY = 0) {
    const config = this.config;
    const o_x = ox - oc;
    const o_y = oy - oc;

    if (useMagnetic && config.magnetic) {
      let step = target.dataset.magnetStep;
      let targetScale = target.dataset.magnetScale;
      step = step && !isNaN(step) ? parseFloat(step) : config.magneticStep;
      targetScale = targetScale && !isNaN(targetScale) ? parseFloat(targetScale) : scale;

      const translateX = (e.clientX - o_x) * step + originX;
      const translateY = (e.clientY - o_y) * step + originY;
      target.style.transform = `translate(${+translateX.toFixed(2)}px, ${+translateY.toFixed(2)}px) scale(${targetScale})`;
    } else {
      target.style.transform = `translate(${o_x}px, ${o_y}px) scale(${scale})`;
    }
  }

  _moveFollower(e) {
    if (!this._follower) return;

    const { spot, spots } = this._follower;
    const config = this.config;
    const w_offset = this._followerOffset;

    // 使用 closest 向上查找 <a> 标签
    const step = e.target.closest('a') ? 2 : 1;

    this._applyMovement(e, spot, config.scale.origin * step, e.clientX, e.clientY, w_offset);
    this._applyMovement(e, spots, config.scale.followed * step, e.clientX, e.clientY, w_offset);
  }

  _onMouseDown() {
    if (this._follower) {
      this._follower.element.classList.add(this.config.class.presser);
    }
  }

  _onMouseUp() {
    if (this._follower) {
      this._follower.element.classList.remove(this.config.class.presser);
    }
  }

  _onMouseEnter() {
    if (this._follower) {
      const el = this._follower.element;
      el.classList.remove(this.config.class.hider);
      el.classList.remove(this.config.class.presser);  // 防止残留
    }
  }

  _onMouseLeave() {
    if (this._follower) {
      this._follower.element.classList.add(this.config.class.hider);
    }
  }

  /**
   * 手动移除某个元素的磁性效果，常用于动态移除元素前调用
   */
  unmagnetic(element) {
    const cls = this.config.class;
    element.style.transform = '';
    element.classList.remove(cls.move, cls.presser);
    this._originMap.delete(element);
    this._magneticElements.delete(element);

    if (element._magneticLeave) {
      element.removeEventListener('mouseleave', element._magneticLeave);
      delete element._magneticLeave;
    }
    if (element._magneticDown) {
      element.removeEventListener('mousedown', element._magneticDown);
      delete element._magneticDown;
    }
    if (element._magneticUp) {
      element.removeEventListener('mouseup', element._magneticUp);
      delete element._magneticUp;
    }
  }

  destroy() {
    document.removeEventListener('mousemove', this._boundHandlers.onMouseMove);
    if (this._follower) {
      document.removeEventListener('mousedown', this._boundHandlers.onMouseDown);
      document.removeEventListener('mouseup', this._boundHandlers.onMouseUp);
      document.documentElement.removeEventListener('mouseenter', this._boundHandlers.onMouseEnter);
      document.documentElement.removeEventListener('mouseleave', this._boundHandlers.onMouseLeave);
      if (this._follower.element.parentNode) {
        this._follower.element.parentNode.removeChild(this._follower.element);
      }
    }

    // 清理所有磁性元素
    this._magneticElements.forEach(el => this.unmagnetic(el));
    this._magneticElements.clear();

    if (this._styleElement && this._styleElement.parentNode) {
      this._styleElement.parentNode.removeChild(this._styleElement);
    }

    this._follower = null;
    this._styleElement = null;
    this._boundHandlers = {};
    this._originMap = new WeakMap();
  }
}

export { MagneticEffect };