(function(blocks, editor, element) {
    var el = element.createElement;
    var __ = wp.i18n.__;
    var RichText = editor.RichText;
    var blockStyle = {
        // backgroundColor: '#f3f3f3',
        border: '1px solid',
        padding: '20px',
        marginBottom: '20px',
    };
    var _iframe = function(vid, cls="bilibili_embed"){
            return `<iframe class="${cls}" src="//player.bilibili.com/player.html?bvid=${vid}&autoplay=0&t=0" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true" autoplay="false"></iframe>`;
        },
        _create = function(html, wrap='div'){
            return wp.element.createElement(wrap, {
                dangerouslySetInnerHTML: {
                    __html: html
                }
            });
        };
    
    blocks.registerBlockType('custom-plugin/bilibili-embed', {
        title: __('Bilibili Embed', 'custom-plugin'),
        icon: 'video-alt2',
        category: 'common',
        
        attributes: {
            content: {
                type: 'array',
                source: 'children',
                selector: 'p',
            },
        },
        
        edit: function(props) {
            var content = props.attributes.content;
            return el('div', { style: blockStyle }, el(RichText, {
                    tagName: 'p',
                    value: content,
                    onChange: function(newContent) {
                        props.setAttributes({ content: newContent });
                    },
                    placeholder: __('输入B站视频VID', 'custom-plugin'),
                    style: {
                        color:'#00a1d6',
                        border: '2px dashed',
                        padding: '5px 15px',
                        display: 'inline',
                        // 'border-radius': '8px'
                    }
                })
            );
        },
        
        save: function(props) {
            var content = props.attributes.content; // + _iframe(content)
            // console.log(content, _iframe(content));
            return _create(_iframe(content));
            // return el(RichText.Content, {
            //     tagName: 'p',
            //     value: content + _iframe(content), //content,
            // });
            // return el('div', { style: blockStyle }, el(RichText.Content, {
            //     tagName: 'p',
            //     value: content,
            // }));
        },
    });
    
})(window.wp.blocks, window.wp.editor, window.wp.element);

// https://www.idcbaby.com/62717/
(function(blocks, editor, element) {
    var el =element.createElement;
    blocks.registerBlockType('pandastudio/tips', {
        title: 'Tips Text',
        icon: 'info',
        category: 'text', //layout
        attributes: {
            content: {
                type: 'array',
                source: 'children',
                selector: 'p',
            },
            typeClass: {
                source: 'attribute',
                selector: '.tip',
                attribute: 'class',
            }
        },
        edit: function(props) {
            var content = props.attributes.content,
                typeClass = props.attributes.typeClass || 'tip info',
                isSelected = props.isSelected;
     
            function onChangeContent(newContent) {
                props.setAttributes({ content: newContent });
            }
     
            function changeType(event) {
                var type = event.target.className;
                props.setAttributes({ typeClass: 'tip ' + type });
            }
     
            var richText = el(
                blocks.RichText, {
                    tagName: 'p',
                    onChange: onChangeContent,
                    value: content,
                    isSelected: props.isSelected,
                    placeholder: '请输入...'
                });
     
            var outerHtml = el('div', { className: typeClass }, richText);
     
            var selector = el('div', { className: 'panda tipSelector' }, [
                el('button', { className: 'info', onClick: changeType }, '蓝色'),
                el('button', { className: 'success', onClick: changeType }, '绿色'),
                el('button', { className: 'worning', onClick: changeType }, '橙色'),
                el('button', { className: 'error', onClick: changeType }, '红色'),
            ])
     
            return el('div', {}, [outerHtml, isSelected && selector]);
     
        },
     
        save: function(props) {
            var content = props.attributes.content,
                typeClass = props.attributes.typeClass || 'tip info';
     
            var outerHtml = el('div', { className: typeClass }, el('p', {}, content));
     
            return el('div', {}, outerHtml);
        },
    });
})(window.wp.blocks, window.wp.editor, window.wp.element);