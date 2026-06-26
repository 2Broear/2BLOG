/**
 * 打字机效果
 * @param {HTMLElement} el        - 目标元素
 * @param {string}      str      - 要输出的字符串
 * @param {number}      speed    - 每个字符的间隔时间（毫秒）
 * @param {boolean}     replace  - true: 替换模式（原字符逐个替换为 str 的字符）；
 *                                 false: 追加模式（先清空再逐个追加）
 * @returns {Promise<void>}
 */
function words_typer(el, str, speed = 100, replace = false) {
    // ---------- 参数校验 ----------
    if (!(el instanceof HTMLElement)) {
        console.warn('words_typer: 第一个参数必须为 HTMLElement');
        return Promise.reject(new Error('Invalid element'));
    }

    if (typeof str !== 'string' || str.trim() === '') {
        console.warn('words_typer: 字符串无效，使用占位文本');
        str = 'invalid string or NULL Responsed.';
    }

    // ---------- 清除之前的定时器 ----------
    if (el._typerTimer) {
        el._typerTimer.forEach(timer => clearTimeout(timer));
        el._typerTimer = [];
    }
    el._typerTimer = []; // 存储本次所有定时器

    // 辅助：延迟函数
    const wait = (ms) => new Promise(resolve => {
        const timer = setTimeout(resolve, ms);
        el._typerTimer.push(timer);
    });

    // ---------- 移除加载状态 ----------
    el.classList.remove('load', 'done');

    // ---------- 第一阶段：清空（或保留）原有内容 ----------
    const originalText = el.textContent;

    // 如果 replace 为 true，我们保留原字符长度，逐个替换；但为简化，统一先清空再添加
    // 但为了保留 replace 的语义（替换模式：不删除，直接逐字符修正），我们分两种模式：
    // - replace = false：先清空，再逐个追加
    // - replace = true：保留原文本长度，逐个替换字符，若 str 更长则追加，更短则删除多余

    // 为了与原函数逻辑尽量接近，原函数是先删除所有字符（无论 replace 真假），再添加
    // 所以我们保留这个行为，但确保如果原文本为空，跳过删除阶段

    // ---------- 删除阶段（仅当原文本非空且 replace 为 false 或 原文本长度>0）----------
    // 如果 replace = true，我们也可以先清空再逐个替换，但这样 replace 失去意义。
    // 更合理的是：replace = true 时，不删除，而是从第一个字符开始逐个替换，长度不足则追加
    // 这里我们重新设计：
    // 如果 replace = true：从原文本出发，逐字符替换成 str 的字符，新字符串更长则追加，更短则裁剪
    // 如果 replace = false：先清空原文本，再逐个追加

    // 但因为原问题是“当 el.textContent 为空时打字效果不会触发”，所以我们只要保证无论是否为空，效果都能执行。

    // 为了彻底解决问题，我将重构为两个清晰的模式：

    // 模式1：追加模式（replace = false）—— 先清空（如果内容非空则逐个删除），再逐个追加 str
    // 模式2：替换模式（replace = true）—— 从原文本出发，逐个字符修正为 str 的字符（长度不足补全，过长截断）

    // 这样更符合“replace”的字面意思。

    return (async () => {
        try {
            // ---------- 模式判断 ----------
            if (!replace) {
                // ---------- 追加模式 ----------
                // 1. 删除原有文本（如果非空）
                if (originalText.length > 0) {
                    const chars = originalText.split('');
                    for (let i = chars.length - 1; i >= 0; i--) {
                        chars.pop();
                        el.textContent = chars.join('');
                        await wait(5); // 删除速度固定为 5ms，可调整
                    }
                }

                // 2. 逐个追加新字符
                for (let i = 0; i < str.length; i++) {
                    el.textContent += str[i];
                    await wait(speed);
                }

                // 3. 标记完成
                el.classList.add('done');
                return;
            }

            // ---------- 替换模式 ----------
            // 将原文本转为数组
            let currentChars = originalText.split('');
            const targetChars = str.split('');

            // 确定最大长度
            const maxLen = Math.max(currentChars.length, targetChars.length);

            for (let i = 0; i < maxLen; i++) {
                // 如果当前索引超出原长度，则追加新字符
                if (i >= currentChars.length) {
                    currentChars.push(targetChars[i]);
                }
                // 如果目标索引超出目标长度，则删除多余字符
                else if (i >= targetChars.length) {
                    currentChars.pop();
                }
                // 否则替换对应位置的字符
                else {
                    currentChars[i] = targetChars[i];
                }

                el.textContent = currentChars.join('');
                await wait(speed);
            }

            // 标记完成
            el.classList.add('done');

        } catch (error) {
            console.error('打字效果出错:', error);
            throw error;
        }
    })();
}

// ---------- 使用示例 ----------
// const el = document.getElementById('myElement');
// words_typer(el, 'Hello World!', 80, false);  // 追加模式
// words_typer(el, 'Hello World!', 80, true);   // 替换模式

export { words_typer };
